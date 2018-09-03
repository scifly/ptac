<?php
namespace App\Jobs;

use App\Events\JobResponse;
use App\Facades\Wechat;
use App\Helpers\Constant;
use App\Helpers\HttpStatusCode;
use App\Helpers\JobTrait;
use App\Helpers\ModelTrait;
use App\Models\Corp;
use App\Models\Tag;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * 企业微信通讯录标签同步
 *
 * Class SyncDepartment
 * @package App\Jobs
 */
class SyncTag implements ShouldQueue {
    
    use Dispatchable, InteractsWithQueue, Queueable,
        SerializesModels, ModelTrait, JobTrait;
    
    protected $data, $userId, $action, $response;
    
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
        $this->response = [
            'userId'     => $userId,
            'title'      => Constant::SYNC_ACTIONS[$action],
            'statusCode' => HttpStatusCode::OK,
            'message'    => __('messages.synced'),
        ];
    }
    
    /**
     * Execute the job
     *
     * @return bool
     * @throws Exception
     */
    public function handle() {
        
        # 同步至企业微信通讯录
        $this->sync();
        # 同步至第三方合作伙伴通讯录(部门)
        $this->apiSync(
            $this->action,
            $this->data,
            $this->response,
            $this->data['tagid']
        );
        
        return true;
        
    }
    
    /**
     * 同步企业微信部门
     *
     * @throws Exception
     */
    private function sync() {
        
        $corp = Corp::find($this->data['corp_id']);
        $token = Wechat::getAccessToken($corp->corpid, $corp->contact_sync_secret, true);
        $this->response['title'] .= '企业微信通讯录标签';
        $this->response['message'] .= '企业微信通讯录';
        if ($token['errcode']) {
            $this->response['statusCode'] = HttpStatusCode::INTERNAL_SERVER_ERROR;
            $this->response['message'] = $token['errmsg'];
        } else {
            $accessToken = $token['access_token'];
            $action = $this->action . 'Tag';
            $result = json_decode(
                Wechat::$action(
                    $token['access_token'],
                    $action != 'deleteTag' ? $this->data : $this->data['tagid']
                )
            );
            # 企业微信通讯录不存在需要更新的标签，则创建该标签
            if ($result->{'errcode'} == 40068 && $action == 'updateTag') {
                $result = json_decode(
                    Wechat::createTag($accessToken, $this->data)
                );
            }
            if (!$result->{'errcode'}) {
                if ($action != 'deleteTag') {
                    # 如果成功创建/更新企业微信通讯录部门，则将本地通讯录相应部门的同步状态置为“已同步”
                    Tag::find($this->data['tagid'])->update(['synced' => 1]);
                }
            } else {
                $this->response['statusCode'] = HttpStatusCode::INTERNAL_SERVER_ERROR;
                $this->response['message'] = Constant::WXERR[$result->{'errcode'}];
            }
        }
        event(new JobResponse($this->response));
        
    }
    
}
