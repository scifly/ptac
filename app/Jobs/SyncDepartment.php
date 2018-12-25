<?php
namespace App\Jobs;

use App\Facades\Wechat;
use App\Helpers\{Broadcaster, Constant, HttpStatusCode, JobTrait, ModelTrait};
use App\Models\{Corp, Department};
use Exception;
use Illuminate\{Bus\Queueable,
    Contracts\Queue\ShouldQueue,
    Foundation\Bus\Dispatchable,
    Queue\InteractsWithQueue,
    Queue\SerializesModels};
use Pusher\PusherException;

/**
 * 企业号部门管理
 *
 * Class SyncDepartment
 * @package App\Jobs
 */
class SyncDepartment implements ShouldQueue {
    
    use Dispatchable, InteractsWithQueue, Queueable,
        SerializesModels, ModelTrait, JobTrait;
    
    protected $data, $userId, $action, $response, $broadcaster;
    
    /**
     * Create a new job instance.
     *
     * @param array $data
     * @param $userId
     * @param $action
     * @throws PusherException
     */
    function __construct(array $data, $userId, $action) {
        
        $this->data = $data;
        $this->action = $action;
        $this->userId = $userId;
        $this->response = array_combine(Constant::BROADCAST_FIELDS, [
            $userId, Constant::SYNC_ACTIONS[$action],
            HttpStatusCode::OK, __('messages.synced'),
        ]);
        $this->broadcaster = new Broadcaster();
        
    }
    
    /**
     * Execute the job
     *
     * @return bool
     * @throws Exception
     */
    function handle() {
        
        # 同步至企业微信通讯录
        $this->sync();
        # 同步至第三方合作伙伴通讯录(部门)
        $this->apiSync(
            $this->action,
            $this->data,
            $this->response,
            $this->data['id']
        );
        
        return true;
        
    }
    
    /**
     * @param Exception $exception
     * @throws PusherException
     */
    function failed(Exception $exception) {
        
        $this->eHandler($exception, $this->response);
        
    }
    
    /**
     * 同步企业微信部门
     *
     * @throws PusherException
     */
    private function sync() {
        
        $corp = Corp::find($this->data['corp_id']);
        $token = Wechat::getAccessToken($corp->corpid, $corp->contact_sync_secret, true);
        $this->response['title'] .= '企业微信部门';
        $this->response['message'] .= '企业微信';
        if ($token['errcode']) {
            $this->response['statusCode'] = HttpStatusCode::INTERNAL_SERVER_ERROR;
            $this->response['message'] = $token['errmsg'];
        } else {
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
            if (!$result->{'errcode'}) {
                if ($action != 'deleteDept') {
                    # 如果成功创建/更新企业微信通讯录部门，则将本地通讯录相应部门的同步状态置为“已同步”
                    Department::find($this->data['id'])->update(['synced' => 1]);
                }
            } else {
                $this->response['statusCode'] = HttpStatusCode::INTERNAL_SERVER_ERROR;
                $this->response['message'] = Constant::WXERR[$result->{'errcode'}];
            }
        }
        !$this->userId ?: $this->broadcaster->broadcast($this->response);
        
    }
    
}
