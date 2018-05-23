<?php
namespace App\Jobs;

use App\Models\Corp;
use App\Facades\Wechat;
use App\Helpers\Constant;
use App\Models\Department;
use App\Events\JobResponse;
use App\Helpers\ModelTrait;
use Illuminate\Bus\Queueable;
use App\Helpers\HttpStatusCode;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;

/**
 * 企业号部门管理
 *
 * Class WechatDepartment
 * @package App\Jobs
 */
class WechatDepartment implements ShouldQueue {
    
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, ModelTrait;
    
    protected $data, $userId, $action;
    
    /**
     * Create a new job instance.
     *
     * @param array $data
     * @param $userId
     * @param $action
     */
    public function __construct(array $data, $userId, $action) {
        
        $this->data = $data;
        $this->action = $action;
        $this->userId = $userId;
        
    }
    
    /**
     * Execute the job
     *
     * @return bool
     */
    public function handle() {
    
        $response = [
            'userId' => $this->userId,
            'title' => sprintf(
                __('messages.department.department_sync'),
                Constant::SYNC_ACTIONS[$this->action]
            ),
            'statusCode' => HttpStatusCode::OK,
            'message' => __('messages.wechat_synced')
        ];
        $params = $this->data['id'];
        if (in_array($this->action, ['create', 'update'])) {
            $params = [
                'id' => $this->data['id'],
                'name' => $this->data['name'],
                'parentid' => $this->data['parent_id'],
                'order' => $this->data['order']
            ];
        }
        $corp = Corp::find($this->data['corp_id']);
        $token = Wechat::getAccessToken($corp->corpid, $corp->contact_sync_secret, true);
        if ($token['errcode']) {
            $response['statusCode'] = HttpStatusCode::INTERNAL_SERVER_ERROR;
            $response['message'] = $token['errmsg'];
            event(new JobResponse($response));
            return false;
        }
        $action = $this->action . 'Dept';
        $result = json_decode(Wechat::$action($token['access_token'], $params));
        # 企业微信通讯录不存在需要更新的部门，则创建该部门
        if ($result->{'errcode'} == 60003 && $action == 'updateDept') {
            $result = json_decode(Wechat::createDept($token['access_token'], $params));
        }
        if ($result->{'errcode'} == 0 && $this->action != 'deleteDept') {
            Department::find($this->data['id'])->update(['synced' => 1]);
        }
        if ($result->{'errcode'}) {
            $response['statusCode'] = HttpStatusCode::INTERNAL_SERVER_ERROR;
            $response['message'] = Wechat::ERRMSGS[$result->{'errcode'}];
            event(new JobResponse($response));
            return false;
        }
        event(new JobResponse($response));
        
        return true;
        
    }
    
}
