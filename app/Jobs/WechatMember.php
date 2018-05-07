<?php
namespace App\Jobs;

use App\Events\JobResponse;
use App\Helpers\Constant;
use App\Helpers\HttpStatusCode;
use App\Helpers\JobTrait;
use App\Models\Corp;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * 企业号会员管理
 *
 * Class WechatMember
 * @package App\Jobs
 */
class WechatMember implements ShouldQueue {
    
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, JobTrait;
    
    protected $member, $userId, $action;
    
    /**
     * Create a new job instance.
     *
     * @param User $member
     * @param $userId - 后台登录用户的id
     * @param $action - create/update/delete操作
     */
    public function __construct(User $member, $userId, $action) {
        
        $this->member = $member;
        $this->userId = $userId;
        $this->action = $action;
        
    }
    
    /**
     * Execute the job.
     *
     * @return bool
     */
    public function handle() {
    
        $response = [
            'userId' => $this->userId,
            'title' => Constant::SYNC_ACTIONS[$this->action] . '企业微信会员',
            'statusCode' => HttpStatusCode::OK,
            'message' => __('messages.wechat_synced')
        ];
        $results = $this->syncMember($this->member, $this->action);
        if (sizeof($results) == 1) {
            if ($results[key($results)]['errcode']) {
                $response['message'] = $results[key($results)]['errmsg'];
                $response['statusCode'] = HttpStatusCode::INTERNAL_SERVER_ERROR;
            }
        } else {
            $errors = 0;
            foreach ($results as $corpId => $result) {
                 $errors += $result['errcode'] ? 1 : 0;
            }
            if ($errors > 0) {
                $message = '';
                $response['statusCode'] = $errors < sizeof($results)
                    ? HttpStatusCode::ACCEPTED
                    : HttpStatusCode::INTERNAL_SERVER_ERROR;
                foreach ($results as $corpId => $result) {
                    $message .= Corp::find($corpId)->name . ': '
                        . (!$result['errcode'] ? __('messages.wechat_synced') : $result['errmsg']) . "\n";
                }
                $response['message'] = $message;
            }
        }
        event(new JobResponse($response));
        
        return true;
        
    }
    
}
