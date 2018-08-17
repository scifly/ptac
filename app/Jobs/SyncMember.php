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
    
    protected $data, $userId, $action, $response;
    
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
        $this->response = [
            'userId' => $userId,
            'title' => Constant::SYNC_ACTIONS[$this->action],
            'statusCode' => HttpStatusCode::OK,
            'message' => __('messages.synced')
        ];
        
    }
    
    /**
     * Execute the job.
     *
     * @return bool
     * @throws Exception
     */
    public function handle() {
    
        $this->syncWx();
        $this->syncKd('人员', $this->response);
        
        return true;
        
    }
    
    /**
     * 同步企业微信会员
     *
     * @throws Exception
     */
    private function syncWx() {
    
        $results = $this->syncMember($this->data, $this->action);
        $this->response['title'] .= '企业微信会员';
        if (sizeof($results) == 1) {
            if ($results[key($results)]['errcode']) {
                $this->response['message'] = $results[key($results)]['errmsg'];
                $this->response['statusCode'] = HttpStatusCode::INTERNAL_SERVER_ERROR;
            }
        } else {
            $errors = 0;
            foreach ($results as $corpId => $result) {
                $errors += $result['errcode'] ? 1 : 0;
            }
            if ($errors > 0) {
                $message = '';
                $this->response['statusCode'] = $errors < sizeof($results)
                    ? HttpStatusCode::ACCEPTED
                    : HttpStatusCode::INTERNAL_SERVER_ERROR;
                foreach ($results as $corpId => $result) {
                    $corpName = Corp::find($corpId)->name;
                    $corpMsg = !$result['errcode']
                        ? __('messages.synced') . '企业微信' . $corpName
                        : $result['errmsg'];
                    $message .= $corpName . ': ' .$corpMsg . "\n";
                }
                $this->response['message'] = $message;
            }
        }
        if ($this->userId) {
            event(new JobResponse($this->response));
        }
        
    }
    
}
