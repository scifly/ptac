<?php
namespace App\Jobs;

use App\Models\Corp;
use App\Helpers\JobTrait;
use App\Helpers\Constant;
use App\Events\JobResponse;
use Exception;
use Illuminate\Bus\Queueable;
use App\Helpers\HttpStatusCode;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldQueue;

/**
 * 企业号会员管理
 *
 * Class SyncMember
 * @package App\Jobs
 */
class SyncMember implements ShouldQueue {
    
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, JobTrait;
    
    protected $data, $userId, $action;
    
    /**
     * Create a new job instance.
     *
     * @param array $data
     * @param $userId - 后台登录用户的id
     * @param $action - create/update/delete操作
     */
    public function __construct($data, $userId, $action) {
        
        $this->data = $data;
        $this->userId = $userId;
        $this->action = $action;
        
    }
    
    /**
     * Execute the job.
     *
     * @return bool
     * @throws Exception
     */
    public function handle() {
    
        $response = [
            'userId' => $this->userId,
            'title' => Constant::SYNC_ACTIONS[$this->action] . '企业微信会员',
            'statusCode' => HttpStatusCode::OK,
            'message' => __('messages.wechat_synced')
        ];
        $results = $this->syncMember($this->data, $this->action);
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
