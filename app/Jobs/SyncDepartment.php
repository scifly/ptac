<?php
namespace App\Jobs;

use App\Facades\Wechat;
use App\Helpers\{Broadcaster, Constant, HttpStatusCode, JobTrait, ModelTrait};
use App\Models\{Corp, Department, DepartmentUser, User};
use Exception;
use Illuminate\{Bus\Queueable,
    Contracts\Queue\ShouldQueue,
    Foundation\Bus\Dispatchable,
    Queue\InteractsWithQueue,
    Queue\SerializesModels,
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
            HttpStatusCode::OK, __('messages.ok'),
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
            $this->response['statusCode'] = HttpStatusCode::INTERNAL_SERVER_ERROR;
            $this->response['message'] = $e->getMessage();
            !$this->userId ?: $this->bc->broadcast($this->response);
            throw $e;
        }
        !$this->userId ?: $this->bc->broadcast($this->response);
        
    }
    
    /**
     * @param Exception $exception
     * @throws PusherException
     */
    function failed(Exception $exception) {
        
        $this->eHandler($exception, $this->response);
        
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
                    $corpDIds = array_merge(
                        [$this->corp->department_id],
                        $d->subIds($this->corp->department_id)
                    );
                    arsort($_syncIds);
                    $deptIds = array_keys($_syncIds);
                    $uIds = DepartmentUser::whereIn('department_id', $deptIds)
                        ->pluck('user_id')->unique()->toArray();
                    $uDeptIds = DepartmentUser::whereIn('user_id', $uIds)
                        ->pluck('department_id')->unique()->toArray();
                    // $uUIds / $dUIds - 需要更新 / 删除的用户Id
                    $uUIds = DepartmentUser::whereIn('department_id', array_diff($uDeptIds, $deptIds))
                        ->pluck('user_id')->unique()->toArray();
                    /** @var User $user */
                    foreach (User::whereIn('id', $uUIds)->get() as $user) {
                        $departments = array_diff(
                            array_intersect(
                                $user->departments->pluck('id')->toArray(),
                                $corpDIds
                            ), $deptIds
                        );
                        if (in_array($this->corp->department_id, $departments)) {
                            $departments = array_merge(
                                [$this->corp->departmentid],
                                array_diff($departments, [$this->corp->department_id])
                            );
                        }
                        $mobile = $user->mobiles->where('isdefault', 1)->first()->mobile;
                        $updates[] = array_combine(Constant::MEMBER_FIELDS, [
                            $user->userid, $user->username, $user->position, $user->realname,
                            $user->english_name, $mobile, $user->email, $departments,
                        ]);
                    }
                    $deletions = User::whereIn('id', array_diff($uIds, $uUIds))
                        ->pluck('userid')->toArray();
                    
                    list($updated, $deleted) = array_map(
                        function ($members, $method) {
                            return $this->syncMember($members, $method);
                        }, [$updates ?? [], $deletions], ['update', 'delete']
                    );
                    array_map(
                        function ($userids, $method) use ($deptIds) {
                            $userIds = User::whereIn('userid', $userids)->pluck('id')->toArray();
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
                        array_diff($this->deptIds(), $deptIds)
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
     * @throws PusherException
     * @throws Throwable
     */
    private function syncMember($members, $method) {
        
        $accessToken = $this->accessToken();
        $data = $method == 'update' ? $members : array_chunk($members, 200);
        $api = $method == 'update' ? 'updateUser' : 'batchDelUser';
        $succeeded = [];
        foreach ($data as $datum) {
            $result = json_decode(
                Wechat::$api($accessToken, $datum), true
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
     * @param array $deptIds
     * @return array|bool
     * @throws Throwable
     */
    private function syncParty(array $deptIds = null) {
        
        $accessToken = $this->accessToken();
        if (!$deptIds) {
            $d = (new Department)->find($this->departmentIds[0]);
            $parentid = $d->departmentType->name == '学校'
                ? $d->school->corp->departmentid
                : $d->parent_id;
            $params = array_combine(
                ['id', 'name', 'parentid', 'order'],
                [$d->id, $d->name, $parentid, $d->order]
            );
            $result = json_decode(
                Wechat::invoke(
                    'ent', 'department', $this->action,
                    [$accessToken], $params
                ), true
            );
            # 如果在更新部门时返回"部门ID不存在"
            $result['errcode'] != 60003 ?: $result = json_decode(
                Wechat::invoke(
                    'ent', 'department', 'create',
                    [$accessToken], $params
                ), true
            );
            $this->response['statusCode'] = $result['errcode']
                ? HttpStatusCode::INTERNAL_SERVER_ERROR
                : HttpStatusCode::OK;
            $this->response['message'] = Constant::WXERR[$result['errcode']];
            $response = $d->update(['synced' => !$result['errcode'] ? 1 : 0]);
        } else {
            foreach ($deptIds as $id) {
                $result = json_decode(
                    Wechat::invoke(
                        'ent', 'department',
                        'delete', [$accessToken, $id]
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
     * @throws PusherException
     * @throws Exception
     */
    private function accessToken() {
        
        $token = Wechat::token(
            $this->corp->corpid,
            $this->corp->contact_sync_secret,
            true
        );
        if ($token['errcode']) {
            $this->response['statusCode'] = HttpStatusCode::INTERNAL_SERVER_ERROR;
            $this->response['message'] = $token['errmsg'];
            $this->bc->broadcast($this->response);
            exit;
        }
        
        return $token['access_token'];
        
    }
    
    /**
     * @return array
     */
    private function deptIds() {
    
        $d = new Department;
        $ids = [];
        foreach ($this->departmentIds as $dId) {
            $ids = array_merge(
                $ids, array_merge([$dId], $d->subIds($dId))
            );
        }
        
        return array_unique($ids);
        
    }
    
}