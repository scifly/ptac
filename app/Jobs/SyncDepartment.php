<?php
namespace App\Jobs;

use App\Facades\Wechat;
use App\Helpers\{Broadcaster, Constant, HttpStatusCode, JobTrait, ModelTrait};
use App\Models\{Corp, Department, DepartmentTag, DepartmentUser, User};
use Exception;
use Illuminate\{Bus\Queueable,
    Contracts\Queue\ShouldQueue,
    Foundation\Bus\Dispatchable,
    Queue\InteractsWithQueue,
    Queue\SerializesModels,
    Support\Facades\DB,
    Support\Facades\Log};
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
    
    protected $departmentId, $action, $userId;
    protected $corp, $bc, $response;
    
    /**
     * Create a new job instance.
     *
     * @param $departmentId
     * @param $action
     * @param $userId
     * @throws PusherException
     */
    function __construct($departmentId, $action, $userId) {
        
        $this->departmentId = $departmentId;
        $this->action = $action;
        $this->userId = $userId;
        $this->bc = new Broadcaster;
        $this->response = array_combine(Constant::BROADCAST_FIELDS, [
            $userId, Constant::SYNC_ACTIONS[$action] . '企业微信部门',
            HttpStatusCode::OK, __('messages.synced'),
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
                    $ids = $this->remove();
                    # 删除部门&用户绑定关系 / 部门&标签绑定关系 / 指定部门及其子部门
                    array_map(
                        function ($obj, $field) use ($ids) {
                            $obj->{'whereIn'}($field, $ids)->delete();
                        },
                        [new DepartmentUser, new DepartmentTag, $d],
                        ['department_id', 'department_id', 'id']
                    );
                } else {
                    $this->corp = Corp::find($d->corpId($this->departmentId));
                    $this->createUpdate();
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
                $ids = array_merge(
                    [$this->departmentId],
                    $d->subIds($this->departmentId)
                );
                foreach ($ids as $id) {
                    if ($d->needSync($d->find($id))) {
                        $level = 0;
                        $syncIds[$id] = $d->level($id, $level);
                    }
                }
                arsort($syncIds);
                $deptIds = array_keys($syncIds);
                $this->corp = Corp::find($d->corpId($deptIds[0]));
                foreach ($deptIds as $id) {
                    foreach ($d->find($id)->users as $user) {
                        $depts = $user->depts($user->id);
                        if ($depts->count() > 1) {
                            $mobile = $user->mobiles->where('isdefault', 1)->first()->mobile;
                            $userDeptIds = array_map(
                                function (Department $dept) {
                                    return in_array($dept->departmentType->name, ['运营', '企业'])
                                        ? $this->corp->departmentid : $dept->id;
                                }, $depts);
                            $updates[] = array_combine(Constant::MEMBER_FIELDS, [
                                $user->userid, $user->username, $user->position, $user->realname,
                                $user->english_name, $mobile, $user->email, array_diff($userDeptIds, [$id])
                            ]);
                        } else {
                            $deletes[] = $user->userid;
                        }
                    }
                    # 更新/删除指定部门中的企业微信会员
                    list($updated, $deleted) = array_map(
                        function ($members, $method) { return $this->updateDelMember($members, $method); },
                        [$updates ?? [], $deletes ?? []], ['update', 'delete']
                    );
                    array_map(
                        function ($userids, $method) use ($id) {
                            $userIds = User::whereIn('userid', $userids)->pluck('id')->toArray();
                            if ($method == 'update') {
                                # 更新部门&用户绑定关系
                                DepartmentUser::whereIn('user_id', $userIds)
                                    ->where('department_id', $id)->delete();
                            } else {
                                # 禁用已删除的企业微信会员对应的本地用户
                                User::whereIn('id', $userIds)->update(['enabled' => 0]);
                            }
                        }, [$updated, $deleted], ['update', 'delete']
                    );
                }
                Log::info('depts', $deptIds);
                # 删除企业微信部门并返回已删除的企业微信部门id
                $deletedIds = $this->delDept($deptIds);
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
    private function updateDelMember($members, $method) {
    
        $accessToken = $this->accessToken();
        $data = $method == 'update' ? $members : array_chunk($members, 200);
        $api = $method == 'update' ? 'updateUser' : 'batchDelUser';
        $succeeded = [];
        foreach ($data as $datum) {
            $result = Wechat::$api($accessToken, $datum);
            if (!$result['errcode']) {
                if ($method == 'update') {
                    $succeeded[] = $datum['userid'];
                } else {
                    $succeeded = array_merge($succeeded, $datum);
                }
            }
        }
        
        return $succeeded;
    
    }
    
    /**
     * 同步企业微信部门
     *
     * @param array $deptIds
     * @return array
     * @throws Throwable
     */
    private function delDept(array $deptIds) {
    
        $accessToken = $this->accessToken();
        foreach ($deptIds as $id) {
            $result = json_decode(
                Wechat::deleteDept($accessToken, $id), true
            );
            if (($result['errcode'] && $result['errcode'] == 60003) || !$result['errcode']) {
                $deleted[] = $id;
            }
        }
        
        return $deleted ?? [];
        
    }
    
    /**
     * 创建或更新企业微信部门
     *
     * @throws PusherException
     */
    private function createUpdate() {
    
        $d = new Department;
        $accessToken = $this->accessToken();
        $action = $this->action == 'create' ? 'createDept' : 'updateDept';
        $department = $d->find($this->departmentId);
        $parentid = $department->departmentType->name == '学校'
            ? $department->school->corp->departmentid
            : $department->parent_id;
        $params = array_combine(
            ['id', 'name', 'parentid', 'order'],
            [$department->id, $department->name, $parentid, $department->order]
        );
        $result = json_decode(Wechat::$action($accessToken, $params), true);
        $errcode = $result['errcode'];
        if ($errcode) {
            # 如果在更新部门时返回"部门ID不存在"
            if ($errcode == 60003) {
                $result = json_decode(Wechat::createDept($accessToken, $params), true);
                $errcode = $result['errcode'];
            }
            if ($errcode) {
                $this->response['statusCode'] = HttpStatusCode::INTERNAL_SERVER_ERROR;
                $this->response['message'] = Constant::WXERR[$errcode];
            }
        }
        $department->update(['synced' => !$errcode ? 1 : 0]);
        
    }
    
    /**
     * 获取access_token
     *
     * @return mixed
     * @throws PusherException
     */
    private function accessToken() {
    
        $token = Wechat::getAccessToken(
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
    
}