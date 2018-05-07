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

class SendMessage implements ShouldQueue {
    
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, ModelTrait, JobTrait;

    protected $data, $userIds, $deptIds, $userId, $corp, $apps, $message;
    
    /**
     * SendMessage constructor.
     *
     * @param array $data
     * @param array $userIds
     * @param array $deptIds
     * @param $userId
     * @param Corp|null $corp
     * @param array $apps
     * @param Message $message
     */
    public function __construct(
        array $data, array $userIds, array $deptIds, $userId,
        Corp $corp = null, array $apps = [], Message $message
    ) {
        
        $this->data = $data;
        $this->userIds = $userIds;
        $this->deptIds = $deptIds;
        $this->userId = $userId;
        $this->corp = $corp;
        $this->apps = $apps;
        $this->message = $message;
        
    }
    
    /**
     * @throws Exception
     * @throws Throwable
     */
    public function handle() {
    
        $response = [
            'userId' => $this->userId,
            'title' => __('messages.message.title'),
            'statusCode' => HttpStatusCode::OK,
            'message' => __('messages.sent')
        ];
        list($users, $mobiles) = $this->message->targets(
            $this->userIds, $this->deptIds
        );
        # 创建发送日志
        $msl = [
            'read_count'      => 0,
            'received_count'  => 0,
            'recipient_count' => count($users),
        ];
        $mslId = MessageSendingLog::create($msl)->id;
        
        # 发送消息
        $title = 'n/a';
        $content = null;
        if ($this->data['type'] == 'sms') {
            # 发送短信消息
            $content = $this->data['content']['sms'];
            $content['media_ids'] = 0;
            # 发送消息
            $result = $this->message->sendSms(
                $mobiles, $content
            );
            # 创建广播消息
            if ($result > 0) {
                $response['statusCode'] = HttpStatusCode::INTERNAL_SERVER_ERROR;
                $response['message'] = __('messages.message.sms_send_failed');
            }
            # 创建用户消息发送日志
            $this->message->log(
                $users, $mslId, $title, $content,
                $result > 0, $result > 0
            );
        } else {
            # 发送微信消息
            $results = [];
            foreach ($this->apps as $app) {
                $message = [
                    'touser'  => implode('|', User::whereIn('id', $this->userIds)->pluck('userid')->toArray()),
                    'toparty' => implode('|', $this->deptIds),
                    'agentid' => $app['agentid'],
                    'msgtype' => $this->data['type']
                ];
                $content = $this->data['type'];
                $content['media_ids'] = 0;
                switch ($this->data['type']) {
                    case 'text':
                        $content = $this->data['content']['text'];
                        $message['text'] = ['content' => $content];
                        break;
                    case 'image' :
                        $mediaId = $this->data['content']['media_id'];
                        $message['image'] = ['media_id' => $mediaId];
                        $content['media_ids'] = $mediaId;
                        break;
                    case 'voice' :
                        $mediaId = $this->data['content']['media_id'];
                        $message['voice'] = ['media_id' => $mediaId];
                        $content['media_ids'] = $mediaId;
                        break;
                    case 'mpnews' :
                        $title = $this->data['content']['articles']['title'];
                        $message['mpnews'] = ['articles' => $this->data['content']['articles']];
                        break;
                    case 'video' :
                        $title = $this->data['content']['video']['title'];
                        $message['video'] = $this->data['content']['video'];
                        break;
                    case 'file':
                        $mediaId = $this->data['content']['media_id'];
                        $message['file'] = ['media_id' => $mediaId];
                        $content['media_ids'] = $mediaId;
                        break;
                    default:
                        break;
                }
                # 发送消息
                $result = $this->sendMessage($this->corp, $app, $message);
                # 创建用户消息发送日志
                $this->message->log(
                    $users, $mslId, $title, $content,
                    $result['errcode'], 0, $app
                );
                $results[$app['id']] = $result;
            }
            # 创建广播消息
            if (sizeof($results) == 1) {
                if ($results[key($results)]['errcode']) {
                    $response['statusCode'] = HttpStatusCode::INTERNAL_SERVER_ERROR;
                    $response['message'] = $results[key($results)]['errmsg'];
                }
            } else {
                $errors = 0;
                foreach ($results as $appId => $result) {
                    $errors += $result['errcode'] ? 1 : 0;
                }
                if ($errors > 0) {
                    $message = '';
                    $response['statusCode'] = $errors < sizeof($results)
                        ? HttpStatusCode::ACCEPTED
                        : HttpStatusCode::INTERNAL_SERVER_ERROR;
                    foreach ($results as $appId => $result) {
                        $message .= App::find($appId)->name . ': '
                            . (!$result['errcode'] ? __('messages.message.sent') : $result['errmsg']) . "\n";
                    }
                    $response['message'] = $message;
                }
            }
        }
        # 发送广播消息
        event(new JobResponse($response));
    
        return true;
        
    }
    
}
