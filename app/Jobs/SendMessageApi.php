<?php
namespace App\Jobs;

use App\Helpers\JobTrait;
use App\Helpers\ModelTrait;
use App\Models\ApiMessage;
use App\Models\App;
use App\Models\CommType;
use App\Models\Department;
use App\Models\DepartmentUser;
use App\Models\Message;
use App\Models\MessageSendingLog;
use App\Models\Mobile;
use App\Models\School;
use App\Models\User;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Throwable;

/**
 * Class SendMessage
 * @package App\Jobs
 */
class SendMessageApi implements ShouldQueue {
    
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, ModelTrait, JobTrait;
    
    protected $mobiles, $schoolId, $content;
    
    /**
     * SendMessage constructor.
     *
     * @param $mobiles
     * @param $schoolId
     * @param $content
     */
    public function __construct($mobiles, $schoolId, $content) {
    
        $this->mobiles = $mobiles;
        $this->schoolId = $schoolId;
        $this->content = $content;
    
    }
    
    /**
     * @throws Exception
     * @throws Throwable
     */
    public function handle() {
    
        try {
            DB::transaction(function () {
                $message = new Message;
                $apiMessage = new ApiMessage;
                $department = new Department;
                
                $school = School::find($this->schoolId);
                $departmentIds = array_merge(
                    [$school->department_id],
                    $department->subDepartmentIds($school->department_id)
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
                    # todo: 此处需更换为接口访问用户对应的message_type_id
                    'message_type_id' => 1,
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
                    # todo: 此处需更换为接口访问用户的realname . (文本)
                    'title'           => '接口消息(文本)',
                    'content'         => json_encode($content),
                    'serviceid'       => 0,
                    'message_id'      => 0,
                    'url'             => 'http://',
                    'media_ids'       => 0,
                    # todo: 此处需更换为接口访问用户的id
                    's_user_id'       => 0,
                    'r_user_id'       => 0,
                    # todo: 此处需更换为接口访问用户对应的message_type_id
                    'message_type_id' => 1,
                    'read'            => 0,
                    'sent'            => $result,
                ];
                $this->sendMessage($message->create($data));
            });
        } catch (Exception $e) {
            throw $e;
        }
        
        return true;
    
    }
    
}
