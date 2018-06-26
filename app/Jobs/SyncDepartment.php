<?php
namespace App\Jobs;

use App\Models\Corp;
use App\Facades\Wechat;
use App\Helpers\Constant;
use App\Models\Department;
use App\Events\JobResponse;
use App\Helpers\ModelTrait;
use Exception;
use Illuminate\Bus\Queueable;
use App\Helpers\HttpStatusCode;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

/**
 * 企业号部门管理
 *
 * Class SyncDepartment
 * @package App\Jobs
 */
class SyncDepartment implements ShouldQueue {
    
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
     * @throws Exception
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
        $corp = Corp::find($this->data['corp_id']);
        $token = Wechat::getAccessToken($corp->corpid, $corp->contact_sync_secret, true);
        if ($token['errcode']) {
            $response['statusCode'] = HttpStatusCode::INTERNAL_SERVER_ERROR;
            $response['message'] = $token['errmsg'];
            event(new JobResponse($response));
            return false;
        }
        $action = $this->action . 'Dept';
        $result = json_decode(
            Wechat::$action(
                $token['access_token'],
                $action != 'deleteDept' ? $this->data : $this->data['id']
            )
        );
        # 企业微信通讯录不存在需要更新的部门，则创建该部门
        if ($result->{'errcode'} == 60003 && $action == 'updateDept') {
            $result = json_decode(Wechat::createDept($token['access_token'], $this->data));
        }
        if ($result->{'errcode'} == 0 && $action != 'deleteDept') {
            Department::find($this->data['id'])->update(['synced' => 1]);
        }
        if ($result->{'errcode'}) {
            $response['statusCode'] = HttpStatusCode::INTERNAL_SERVER_ERROR;
            $response['message'] = Constant::WXERR[$result->{'errcode'}];
            event(new JobResponse($response));
            return false;
        }
        event(new JobResponse($response));
        
        return true;
        
    }
    
}
