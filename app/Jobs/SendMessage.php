<?php
namespace App\Jobs;

use App\Events\JobResponse;
use App\Helpers\HttpStatusCode;
use App\Helpers\JobTrait;
use App\Helpers\ModelTrait;
use App\Models\App;
use App\Models\Corp;
use App\Models\Message;
use App\Models\MessageSendingLog;
use App\Models\User;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Throwable;

/**
 * Class SendMessage
 * @package App\Jobs
 */
class SendMessage implements ShouldQueue {
    
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, ModelTrait, JobTrait;
    
    protected $data, $userId, $corp, $apps;
    
    /**
     * SendMessage constructor.
     *
     * @param array $data
     * @param $userId
     * @param Corp|null $corp
     * @param array $apps
     */
    public function __construct(
        array $data,
        $userId,
        Corp $corp = null,
        array $apps = []
    ) {
        
        $this->data = $data;
        $this->userId = $userId;
        $this->corp = $corp;
        $this->apps = $apps;
        
    }
    
    /**
     * @throws Exception
     * @throws Throwable
     */
    public function handle() {
        
        $message = new Message();
        $response = [
            'userId'     => $this->userId,
            'title'      => __('messages.message.title'),
            'statusCode' => HttpStatusCode::OK,
            'message'    => __('messages.message.sent'),
        ];
        list($users, $mobiles) = $message->targets(
            $this->data['user_ids'], $this->data['dept_ids']
        );
        # 创建发送日志
        $msl = [
            'read_count'      => 0,
            'received_count'  => 0,
            'recipient_count' => count($users),
        ];
        $mslId = MessageSendingLog::create($msl)->id;
        # 发送消息
        if ($this->data['type'] == 'sms') {
            # 发送短信消息
            $result = $message->sendSms(
                $mobiles, $this->data['sms']
            );
            # 创建广播消息
            if ($result > 0) {
                $response['statusCode'] = HttpStatusCode::INTERNAL_SERVER_ERROR;
                $response['message'] = __('messages.message.sms_send_failed');
            }
            # 创建用户消息发送日志
            $message->log(
                $users, $this->userId, $mslId, $this->data['title'], $this->data['sms'],
                $result <= 0, $result <= 0, $this->data['message_type_id']
            );
        } else {
            # 发送微信消息
            $results = [];
            foreach ($this->apps as $app) {
                $userids = User::whereIn('id', $this->data['user_ids'])
                    ->where('subscribed', 1)    # 仅发送消息给已关注的用户
                    ->pluck('userid')->toArray();
                $content = [
                    'touser'            => implode('|', $userids),
                    'toparty'           => implode('|', $this->data['dept_ids']),
                    'agentid'           => $app['agentid'],
                    'msgtype'           => $this->data['type'],
                    $this->data['type'] => $this->data[$this->data['type']],
                ];
                # 发送消息
                $result = $this->sendMessage($this->corp, $app, $content);
                # 创建用户消息发送日志
                $message->log(
                    $users, $this->userId, $mslId, $this->data['title'], $content,
                    $result, 0, $this->data['message_type_id'], $app['id']
                );
                $results[$app['id']] = $result;
            }
            # 创建广播消息
            if (sizeof($results) == 1) {
                $result = $results[key($results)];
                if ($result['errcode']) {
                    $response['statusCode'] = HttpStatusCode::INTERNAL_SERVER_ERROR;
                    $response['message'] = $result['errmsg'];
                } else {
                    $total = count($users);
                    $failed = count($message->failedUserIds($result['invaliduser'], $result['invalidparty']));
                    $succeeded = $total - $failed;
                    if (!$succeeded) {
                        $response['statusCode'] = HttpStatusCode::INTERNAL_SERVER_ERROR;
                    }
                    if ($succeeded > 0 && $succeeded < $total) {
                        $response['statusCode'] = HttpStatusCode::ACCEPTED;
                    }
                    $response['message'] = sprintf(
                        __('messages.message.sent'),
                        $total, $succeeded, $failed
                    );
                }
            } else {
                $errors = 0;
                foreach ($results as $appId => $result) {
                    $errors += $result['errcode'] ? 1 : 0;
                }
                if ($errors > 0) {
                    $content = '';
                    $response['statusCode'] = $errors < sizeof($results)
                        ? HttpStatusCode::ACCEPTED
                        : HttpStatusCode::INTERNAL_SERVER_ERROR;
                    foreach ($results as $appId => $result) {
                        $content .= App::find($appId)->name . ': '
                            . (!$result['errcode'] ? __('messages.message.sent') : $result['errmsg']) . "\n";
                    }
                    $response['message'] = $content;
                }
            }
        }
        # 发送广播消息
        if ($this->userId) {
            event(new JobResponse($response));
        }
        
    }
    
}
