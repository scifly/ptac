<?php
namespace App\Jobs;

use App\Helpers\{JobTrait, ModelTrait};
use App\Models\{ApiMessage, MediaType, Message, MessageLog, MessageType, School, User};
use Exception;
use Illuminate\{Bus\Queueable,
    Contracts\Queue\ShouldQueue,
    Foundation\Bus\Dispatchable,
    Queue\InteractsWithQueue,
    Queue\SerializesModels,
    Support\Facades\DB};
use Throwable;

/**
 * Class SendMessage
 * @package App\Jobs
 */
class SendMessageApi implements ShouldQueue {
    
    use Dispatchable, InteractsWithQueue, Queueable,
        SerializesModels, ModelTrait, JobTrait;
    
    protected $mobiles, $schoolId, $content, $partner;
    
    /**
     * SendMessage constructor.
     *
     * @param $mobiles
     * @param $schoolId
     * @param $content
     * @param User $partner
     */
    function __construct($mobiles, $schoolId, $content, User $partner) {
        
        $this->mobiles = $mobiles;
        $this->schoolId = $schoolId;
        $this->content = $content;
        $this->partner = $partner;
        
    }
    
    /**
     * @throws Exception
     * @throws Throwable
     */
    function handle() {
        
        try {
            DB::transaction(function () {
                $message = new Message;
                $apiMessage = new ApiMessage;
                $messageType = MessageType::whereUserId($this->partner->id)->first();
                $school = School::find($this->schoolId);
                # 所有手机号码
                $targets = collect(explode(',', $this->mobiles))->unique();
                # 在指定学校通讯录内的手机号码
                $contacts = User::whereIn('mobile', $targets)->pluck('id', 'mobile');
                # 创建发送日志
                $msl = (new MessageLog)->store([
                    'views'      => 0,
                    'deliveries' => 0,
                    'recipients' => 0,
                ]);
                # 发送短信
                $mobiles = $targets->diff($contacts->keys());
                $result = $message->sendSms(
                    $mobiles, $this->content, $this->partner->id
                );
                $data = [
                    'message_log_id'  => $msl->id,
                    'message_type_id' => $messageType->id,
                    's_user_id'       => $this->partner->id,
                    'content'         => $this->content,
                    'read'            => 0,
                    'sent'            => $result > 0 ? 1 : 0,
                ];
                $apiMessage->log($mobiles, $data);
                # 发送微信
                $app = $school->app ?? $this->corpApp($school->corp_id);
                $userids = User::whereIn('id', $contacts->values())->pluck('userid');
                $content = [
                    'touser'  => $userids->join('|'),
                    'toparty' => '',
                    'agentid' => $app->category == 1 ? $app['agentid'] : 0,
                    'msgtype' => 'text',
                    'text'    => ['content' => $this->content],
                ];
                $this->send(
                    $message->create(
                        array_combine($message->getFillable(), [
                            $messageType->id, MediaType::whereName('text')->first()->id,
                            $app->id, $msl->id, $messageType->name . '(文本)',
                            json_encode($content), 0, 0, 'http://', 0,
                            $this->partner->id, 0, 0, $result, null,
                        ])
                    )
                );
            });
        } catch (Exception $e) {
            throw $e;
        }
        
        return true;
        
    }
    
    /**
     * 任务异常处理
     *
     * @param Exception $e
     * @throws Exception
     */
    function failed(Exception $e) {
        
        $this->eHandler($this, $e);
        
    }
    
}
