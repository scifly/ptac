<?php
namespace App\Jobs;

use App\Helpers\Constant;
use App\Helpers\JobTrait;
use App\Models\App;
use App\Models\CommType;
use App\Models\Event;
use App\Models\Message;
use App\Models\MessageType;
use App\Models\Mobile;
use App\Models\School;
use App\Models\User;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * Class TestJob
 * @package App\Jobs
 */
class TestJob implements ShouldQueue {
    
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, JobTrait;
    
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct() {
        //
    }
    
    /**
     * Execute the job.
     *
     * @return void
     * @throws Exception
     */
    public function handle() {
        
        $events = Event::whereEnabled(1)->get();
        foreach ($events as $event) {
            if (date(now()) >= $event->start) {
                # send message immediately
                $this->message($event->id);
            }
        }
    
    }
    
    /**
     * 发送需定时发送的消息
     *
     * @param $eventId
     * @throws Exception
     */
    private function message($eventId) {
    
        $message = Message::whereEventId($eventId)->first();
        $msgContent = json_decode($message->content);
        $userids = explode('|', $msgContent->{'touser'});
        /** @var Collection|User[] $users - 需要记录消息发送日志的用户（学生、教职员工） */
        /** @var array $targets - 监护人、教职员工列表 */
        /** @var array $mobiles - 监护人、教职员工手机号码列表 */
        list($users, $targets, $mobiles) = $message->targets(
            User::whereIn('userid', $userids)->pluck('id')->toArray(),
            explode('|', $msgContent->{'toparty'})
        );
        $msgType = $msgContent->{'msgType'};
        $data = [
            'comm_type_id' => $msgType == 'sms'
                ? CommType::whereName('短信')->first()->id
                : CommType::whereName('应用')->first()->id,
            'msl_id' => $this->mslId(count($mobiles)),
            'title' => MessageType::find($message->message_type_id)->name
                . '(' . Constant::INFO_TYPES[$msgType] . ')',
            'serviceid' => 0,
            'message_id' => $message->id,
            'url' => 'http://',
            'media_ids' => 0,
            's_user_id' => $message->s_user_id,
            'message_type_id' => $message->message_type_id,
        ];
        $content = [
            'toparty' => $msgContent->{'toparty'},
            'msgtype' => $msgType,
            $msgType => $msgContent->{$msgType}
        ];
        if ($msgType == 'sms') {
            $this->sendSms($users, $mobiles, $content, $msgContent, $data);
        } else {
            if ($targets[0]->isNotEmpty()) {
                $users = $targets[0];
                $mobiles = Mobile::whereIn('user_id', $users->pluck('id')->toArray())
                    ->where(['isdefault' => 1, 'enabled' => 1])->pluck('mobile')->toArray();
                $data['type'] = 'sms';
                $data['app_id'] = 0;
                $data['sms'] = 'url_to_wechat_message'; # todo:
                $this->sendSms($users, $mobiles, $content, $msgContent, $data);
            }
            if ($targets[1]->isNotEmpty()) {
                $users = $targets[1];
                $app = App::whereName('消息中心')
                    ->where('corp_id', School::find($this->school_id($users->first()))->corp_id)
                    ->first();
                $data['app_id'] = $app->id;
                $content = array_merge($content, ['agentid' => $app->agentid]);
                $userids = $users->pluck('userid')->toArray();
                $data['sent'] = $this->sendMessage(
                    $app->corp, $app->toArray(), array_merge($content, [
                        'touser' => implode('|', $userids)
                    ])
                );
                $data['content'] = json_encode(
                    array_merge($content, ['touser' => $msgContent->{'touser'}])
                );
                $message->log($this->logUsers($users), $data);
            }
        }
        
        
    }
    
    /**
     * 发送需定时发送的短信
     *
     * @param $users
     * @param $mobiles
     * @param array $content
     * @param $msgContent
     * @param $data
     * @throws Exception
     */
    private function sendSms($users, $mobiles, $content, $msgContent, $data) {
        
        $message = new Message;
        $result = $message->sendSms($mobiles, $msgContent->{'sms'});
        $data['sent'] = $data['read'] = $result <= 0;
        $data['content'] = json_encode(
            array_merge($content, ['touser' => $msgContent->{'touser'}, 'agentid' => 0])
        );
        $message->log($users, $data);
        
    }
    
}