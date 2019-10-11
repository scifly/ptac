<?php
namespace App\Jobs;

use App\Facades\Wechat;
use App\Helpers\{Broadcaster, Constant, JobTrait, ModelTrait};
use App\Models\{Corp, Department, DepartmentUser, User};
use Exception;
use Illuminate\{Bus\Queueable,
    Contracts\Queue\ShouldQueue,
    Foundation\Bus\Dispatchable,
    Queue\InteractsWithQueue,
    Queue\SerializesModels,
    Support\Collection,
    Support\Facades\DB};
use Pusher\PusherException;
use Throwable;

/**
 * 企业号部门管理
 *
 * Class SyncDepartment
 * @package App\Jobs
 */
class SyncDepartment implements ShouldQueue {
    
    use Dispatchable, InteractsWithQueue, Queueable,
        SerializesModels, ModelTrait, JobTrait;
    
    protected $departmentIds, $action, $userId;
    protected $corp, $bc, $response, $deptIds;
    
    /**
     * Create a new job instance.
     *
     * @param array $departmentIds
     * @param $action
     * @param $userId
     * @throws PusherException
     */
    function __construct(array $departmentIds, $action, $userId) {
        
        $this->departmentIds = $departmentIds;
        $this->action = $action;
        $this->userId = $userId;
        $this->bc = new Broadcaster;
        $this->response = array_combine(Constant::BROADCAST_FIELDS, [
            $userId, Constant::SYNC_ACTIONS[$action] . '企业微信部门',
            Constant::OK, __('messages.ok'),
        ]);
        
    }
    
    /**
     * Execute the job
     *
     * @throws Throwable
     */
    function handle() {
        
        try {
            DB::transaction(function () {
                $d = new Department();
                if ($this->action == 'delete') {
                    # 同步企业微信通讯录并获取已删除的部门id
                    $this->remove();
                    # 删除部门&用户绑定关系 / 部门&标签绑定关系 / 指定部门及其子部门
                    array_map(
                        function ($class, $field) {
                            $this->model($class)->whereIn($field, $this->deptIds)->delete();
                        },
                        ['DepartmentUser', 'DepartmentTag', 'Department'],
                        ['department_id', 'department_id', 'id']
                    );
                    $this->response['message'] = __('messages.department.deleted');
                } else {
                    $this->corp = Corp::find($d->corpId($this->departmentIds[0]));
                    $this->syncParty();
                }
            });
        } catch (Exception $e) {
           $this->eHandler($this, $e);
        }
        !$this->userId ?: $this->bc->broadcast($this->response);
        
    }
    
    /**
     * @param Exception $e
     * @throws Exception
     */
    function failed(Exception $e) {
        
        $this->eHandler($this, $e);
        
    }
    
    /**
     * 删除企业微信部门(并更新/删除部门中的会员)
     *
     * @return array
     * @throws Throwable
     */
    private function remove() {
        
        $deletedIds = [];
        try {
            DB::transaction(function () use (&$deletedIds) {
                $d = new Department;
                $this->deptIds = $this->deptIds();
                foreach ($this->deptIds as $id) {
                    if ($d->needSync($d->find($id))) {
                        if (!($corpId = $d->corpId($id))) continue;
                        $level = 0;
                        $syncIds[$corpId][$id] = $d->level($id, $level);
                    }
                }
                foreach ($syncIds ?? [] as $corpId => $_syncIds) {
                    # step 1 - 同步处于需要删除部门下的所有会员
                    $this->corp = Corp::find($corpId);
                    $corpDIds = collect([$this->corp->department_id])->merge(
                        $this->subIds($this->corp->department_id)
                    );
                    arsort($_syncIds);
                    $deptIds = collect(array_keys($_syncIds));
                    $uIds = DepartmentUser::whereIn('department_id', $deptIds)
                        ->pluck('user_id')->unique();
                    $uDeptIds = DepartmentUser::whereIn('user_id', $uIds)
                        ->pluck('department_id')->unique();
                    // $uUIds / $dUIds - 需要更新 / 删除的用户Id
                    $uUIds = DepartmentUser::whereIn('department_id', $uDeptIds->diff($deptIds))
                        ->pluck('user_id')->unique()->toArray();
                    /** @var User $user */
                    foreach (User::whereIn('id', $uUIds)->get() as $user) {
                        $departments = $user->depts->pluck('id')
                            ->intersect($corpDIds)->diff($deptIds);
                        if ($departments->has($this->corp->department_id)) {
                            $departments = collect([$this->corp->departmentid])->merge(
                                $departments->diff([$this->corp->department_id])
                            );
                        }
                        $entAttrs = json_decode($user->ent_attrs, true);
                        $updates[] = array_combine(Constant::MEMBER_FIELDS, [
                            $entAttrs['userid'], $user->username, $entAttrs['position'],
                            $user->realname, $entAttrs['english_name'], $user->mobile,
                            $user->email, $departments,
                        ]);
                    }
                    $deletions = User::whereIn('id', $uIds->diff($uUIds))->pluck('userid');
                    
                    [$updated, $deleted] = array_map(
                        function ($members, $method) {
                            return $this->syncMember($members, $method);
                        }, [collect($updates ?? []), $deletions], ['update', 'delete']
                    );
                    array_map(
                        function ($userids, $method) use ($deptIds) {
                            $userIds = User::whereIn('userid', $userids)->pluck('id');
                            if ($method == 'update') {
                                DepartmentUser::whereIn('user_id', $userIds)
                                    ->whereIn('department_id', $deptIds)->delete();
                            } else {
                                User::whereIn('id', $userIds)->update([
                                    'synced' => 0, 'subscribed' => 0, 'enabled' => 0,
                                ]);
                            }
                        }, [$updated, $deleted], ['update', 'delete']
                    );
                    # step 2 - 删除部门
                    $deletedIds = array_merge(
                        $deletedIds,
                        $this->syncParty($deptIds),
                        $this->deptIds()->diff($deptIds)
                    );
                }
            });
        } catch (Exception $e) {
            throw $e;
        }
        
        return $deletedIds;
        
    }
    
    /**
     * 批量删除/更新会员
     *
     * @param $members
     * @param $method
     * @return array
     * @throws Throwable
     */
    private function syncMember(Collection $members, $method) {
        
        $data = $method == 'update' ? $members : $members->chunk(200);
        $succeeded = [];
        $token = $this->token();
        foreach ($data as $datum) {
            $result = json_decode(
                Wechat::invoke(
                    'ent', 'user', $method, [$token], $datum
                ), true
            );
            $result['errcode'] ?: (
                $method == 'update'
                    ? $succeeded[] = $datum['userid']
                    : $succeeded = array_merge($succeeded, $datum)
            );
        }
        
        return $succeeded;
        
    }
    
    /**
     * 同步企业微信部门
     *
     * @param Collection $deptIds
     * @return array|bool
     * @throws Throwable
     */
    private function syncParty($deptIds = null) {
        
        $token = $this->token();
        if (!$deptIds) {
            $d = (new Department)->find($this->departmentIds[0]);
            $parentid = $d->dType->name == '学校'
                ? $d->school->corp->departmentid
                : $d->parent_id;
            $params = array_combine(
                ['id', 'name', 'parentid', 'order'],
                [$d->id, $d->name, $parentid, $d->order]
            );
            $result = json_decode(
                Wechat::invoke(
                    'ent', 'department',
                    $this->action, [$token], $params
                ), true
            );
            # 如果在更新部门时返回"部门ID不存在"
            $result['errcode'] != 60003 ?: $result = json_decode(
                Wechat::invoke(
                    'ent', 'department',
                    'create', [$token], $params
                ), true
            );
            $this->response['statusCode'] = $result['errcode']
                ? Constant::INTERNAL_SERVER_ERROR
                : Constant::OK;
            $this->response['message'] = Constant::WXERR[$result['errcode']];
            $response = $d->update(['synced' => !$result['errcode'] ? 1 : 0]);
        } else {
            foreach ($deptIds as $id) {
                $result = json_decode(
                    Wechat::invoke(
                        'ent', 'department',
                        'delete', [$token, $id]
                    ), true
                );
                if (($result['errcode'] && $result['errcode'] == 60123) || !$result['errcode']) {
                    $deleted[] = $id;
                }
            }
            $response = $deleted ?? [];
        }
        
        return $response;
        
    }
    
    /**
     * 获取access_token
     *
     * @return mixed
     * @throws Exception
     */
    private function token() {
        
        return Wechat::token(
            'ent', $this->corp->corpid,
            Wechat::syncSecret($this->corp->id),
        );
        
    }
    
    /** @return Collection */
    private function deptIds() {
    
        $ids = collect([]);
        foreach ($this->departmentIds as $dId) {
            $ids = $ids->merge(
                collect([$dId])->merge(
                    $this->subIds($dId)
                )
            );
        }
        
        return $ids->unique();
        
    }
    
}