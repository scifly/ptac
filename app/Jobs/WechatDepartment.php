<?php
namespace App\Jobs;

use App\Helpers\Constant;
use App\Models\Corp;
use App\Facades\Wechat;
use App\Models\Department;
use App\Helpers\ModelTrait;
use Illuminate\Bus\Queueable;
use App\Helpers\HttpStatusCode;
use App\Events\DepartmentSyncTrigger;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

/**
 * 企业号部门管理
 *
 * Class WechatDepartment
 * @package App\Jobs
 */
class WechatDepartment implements ShouldQueue {
    
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, ModelTrait;
    
    protected $department, $userId, $action;
    
    /**
     * Create a new job instance.
     *
     * @param Department $department
     * @param $userId
     * @param $action
     */
    public function __construct(Department $department, $userId, $action) {
        
        $this->department = $department;
        $this->action = $action;
        $this->userId = $userId;
        
    }
    
    /**
     * Execute the job
     */
    public function handle() {
    
        $departmentId = $this->department->id;
        $response = [
            'userId' => $this->userId,
            'title' => Constant::SYNC_ACTIONS[$this->action] . '企业微信部门',
            'statusCode' => HttpStatusCode::OK,
            'message' => __('messages.wechat_synced')
        ];
        $params = $departmentId;
        if (in_array($this->action, ['create, update'])) {
            $name = $this->department->name;
            $parent_id = $this->department->departmentType->name == '学校'
                ? 1 : $this->department->parent->id;
            $order = $this->department->order;
            $params = [
                'id' => $departmentId,
                'name' => $name,
                'parentid' => $parent_id,
                'order' => $order
            ];
        }
        $corp = Corp::find($this->department->corpId($departmentId));
        $token = Wechat::getAccessToken($corp->corpid, $corp->contact_sync_secret, true);
        $result = null;
        switch ($this->action) {
            case 'create':
                $result = json_decode(Wechat::createDept($token, $params));
                break;
            case 'update':
                $result = json_decode(Wechat::updateDept($token, $params));
                break;
            case 'delete':
                $result = json_decode(Wechat::deleteDept($token, $params));
                break;
            default:
                break;
        }
        if ($result->{'errcode'} == 0 && $this->action !== 'delete') {
            Department::find($departmentId)->first()->update(['synced' => 1]);
        }
        if ($result->{'errcode'} != 0) {
            $response['statusCode'] = HttpStatusCode::INTERNAL_SERVER_ERROR;
            $response['message'] = $result->{'errcode'} . ' : '
                . Wechat::ERRCODES[intval($result->{'errcode'})];
        }
    
        event(new DepartmentSyncTrigger($response));
        
    }
    
}
