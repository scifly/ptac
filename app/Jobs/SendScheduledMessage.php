<?php
namespace App\Jobs;

use App\Helpers\Constant;
use App\Helpers\JobTrait;
use App\Models\App;
use App\Models\CommType;
use App\Models\DepartmentUser;
use App\Models\Event;
use App\Models\Message;
use App\Models\MessageType;
use App\Models\School;
use App\Models\User;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Throwable;

/**
 * Class SendScheduledMessage
 * @package App\Jobs
 */
class SendScheduledMessage implements ShouldQueue {
    
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
     * @throws Throwable
     */
    public function handle() {

        try {
            DB::transaction(function () {
                $events = Event::whereEnabled(1)
                    ->where('start', '<=', date(now()))
                    ->take(500)
                    ->get();
                foreach ($events as $event) {
                    $sent = $this->send($event->id);
                    if ($sent) {
                        $event->update(['enabled' => 0]);
                    }
                }
            });
        } catch (Exception $e) {
            throw $e;
        }
        
    }
    
    /**
     * 发送需定时发送的消息
     *
     * @param $eventId
     * @return bool
     * @throws Throwable
     */
    private function send($eventId) {
    
        try {
            DB::transaction(function () use ($eventId) {
                $message = Message::whereEventId($eventId)->first();
                $msgContent = json_decode($message->content);
                $userids = explode('|', $msgContent->{'touser'});
                $touser = User::whereIn('userid', $userids)->pluck('id')->toArray();
                $toparty = explode('|', $msgContent->{'toparty'});
                $msgType = $msgContent->{'msgtype'};
                # 用于创建消息记录的数据
                $data = [
                    'comm_type_id' => $msgType == 'sms'
                        ? CommType::whereName('短信')->first()->id
                        : CommType::whereName('应用')->first()->id,
                    'title' => MessageType::find($message->message_type_id)->name
                        . '(' . Constant::INFO_TYPE[$msgType] . ')',
                    'serviceid' => 0,
                    'message_id' => $message->id,
                    'url' => 'http://',
                    'media_ids' => 0,
                    's_user_id' => $message->s_user_id,
                    'message_type_id' => $message->message_type_id,
                ];
                # $content - 消息的实际内容
                # 发送消息时，touser字段内容为监护人、教职员工的userid列表，以|符号分隔
                # 保存消息时，touser字段内容为学生、教职员工的userid列表，以|符号分隔
                $content = [
                    'toparty' => $msgContent->{'toparty'},
                    'msgtype' => $msgType,
                    $msgType => $msgContent->{$msgType}
                ];
                if ($msgType == 'sms') {
                    list($logUsers, $mobiles) = $message->smsTargets($touser, $toparty);
                    $data['msl_id'] = $this->mslId(count($mobiles));
                    $this->sendSms($logUsers, $mobiles, $content, $msgContent, $data);
                } else {
                    /**
                     * @var Collection|User[] $wxTargets
                     * @var Collection|User[] $smsLogUsers
                     * @var Collection|User[] $wxLogUsers
                     * @var Collection|User[] $realTargetUsers
                     */
                    list($smsMobiles, $smsLogUsers, $wxTargets, $wxLogUsers, $realTargetUsers) = $message->wxTargets(
                        $touser, $toparty
                    );
                    $data['msl_id'] = $this->mslId($smsLogUsers->count() + $wxLogUsers->count());
                    # step 1: 向已关注的用户发送微信
                    if ($realTargetUsers->where('subscribed', 1)->count()) {
                        if ($wxTargets->isEmpty()) {
                            $departmentUerIds = DepartmentUser::whereIn('department_id', $toparty)->pluck('user_id');
                            $users = User::whereIn('id', $departmentUerIds)->get();
                            $schoolId = $this->school_id($users->first());
                        } else {
                            $schoolId = $this->school_id($wxTargets->first());
                        }
                        $corpId = School::find($schoolId)->corp_id;
                        $app = App::whereName('消息中心')->where('corp_id', $corpId)->first();
                        $content = array_merge($content, ['agentid' => $app->agentid]);
                        $userids = $wxTargets->pluck('userid')->toArray();
                        $data['app_id'] = $app->id;
                        $data['sent'] = $this->sendMessage(
                            $app->corp, $app->toArray(),
                            array_merge($content, ['touser' => implode('|', $userids)])
                        );
                        $data['content'] = json_encode(
                            array_merge(
                                $content,
                                ['touser' => $msgContent->{'touser'}]
                            )
                        );
                        $message->log($wxLogUsers, $data);
                    }
                    
                    # step 2: 向未关注的用户发送短信
                    if (!empty($smsMobiles)) {
                        $content['msgtype'] = 'sms';
                        # todo: 生成微信消息详情url
                        $content['sms'] = $msgContent->{'sms'} = 'url_to_wechat_message';
                        $data['app_id'] = 0;
                        $data['title'] = MessageType::find($data['message_type_id'])->name . '(短信)';
                        $this->sendSms($smsLogUsers, $smsMobiles, $content, $msgContent, $data);
                    }
    
                }
            });
        } catch (Exception $e) {
            throw $e;
        }
        
        return true;
        
    }
    
    /**
     * 发送需定时发送的短信
     *
     * @param $users
     * @param $mobiles
     * @param array $content
     * @param $msgContent
     * @param $data
     * @throws Throwable
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
