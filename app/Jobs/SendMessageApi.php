<?php
namespace App\Jobs;

use App\Helpers\{JobTrait, ModelTrait};
use App\Models\{ApiMessage,
    App,
    CommType,
    Department,
    DepartmentUser,
    Message,
    MessageSendingLog,
    MessageType,
    Mobile,
    School,
    User};
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
                $department = new Department;
                
                $messageType = MessageType::whereUserId($this->partner->id)->first();
                $school = School::find($this->schoolId);
                $departmentIds = array_merge(
                    [$school->department_id],
                    $department->subIds($school->department_id)
                );
                $userIds = array_unique(
                    DepartmentUser::whereIn('department_id', $departmentIds)->pluck('user_id')->toArray()
                );
                # 所有手机号码
                $targets = array_unique(explode(',', $this->mobiles));
                # 在指定学校通讯录内的手机号码
                $contacts = Mobile::whereIn('mobile', $targets)
                    ->whereIn('user_id', $userIds)
                    ->pluck('user_id', 'mobile')
                    ->toArray();
                
                # 创建发送日志
                $msl = (new MessageSendingLog)->store([
                    'read_count'     => 0,
                    'received_count' => 0,
                    'recipients'     => 0,
                ]);
                
                # 发送短信
                $mobiles = array_diff($targets, array_keys($contacts));
                $result = $message->sendSms(
                    $mobiles, $this->content . $school->signature
                );
                $data = [
                    'msl_id' => $msl->id,
                    'message_type_id' => $messageType->id,
                    's_user_id' => 0,
                    'content' => $this->content,
                    'read' => 0,
                    'sent' => $result > 0 ? 0 : 1
                ];
                $apiMessage->log($mobiles, $data);
                
                # 发送微信
                $app = App::whereName('消息中心')->where('corp_id', $school->corp_id)->first();
                $users = User::whereIn('id', array_values($contacts))->get();
                $userids = $users->pluck('userid')->toArray();
                $content = [
                    'touser' => implode('|', $userids),
                    'toparty' => '',
                    'agentid' => $app['agentid'],
                    'msgtype' => 'text',
                    'text' => ['content' => $this->content]
                ];
                $data = [
                    'comm_type_id'    => CommType::whereName('微信')->first()->id,
                    'app_id'          => $app->id,
                    'msl_id'          => $msl->id,
                    'title'           => $messageType->name . '(文本)',
                    'content'         => json_encode($content),
                    'serviceid'       => 0,
                    'message_id'      => 0,
                    'url'             => 'http://',
                    'media_ids'       => 0,
                    's_user_id'       => $this->partner->id,
                    'r_user_id'       => 0,
                    'message_type_id' => $messageType->id,
                    'read'            => 0,
                    'sent'            => $result,
                ];
                $this->send($message->create($data));
            });
        } catch (Exception $e) {
            throw $e;
        }
        
        return true;
        
    }
    
    /**
     * 任务异常处理
     *
     * @param Exception $exception
     */
    function failed(Exception $exception) {
    
    
    }
    
}
