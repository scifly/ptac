<?php
namespace App\Jobs;

use App\Helpers\JobTrait;
use App\Helpers\ModelTrait;
use App\Models\ApiMessage;
use App\Models\App;
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
    
    protected $targets, $schoolId, $content;
    
    /**
     * SendMessage constructor.
     *
     * @param $mobiles
     * @param $schoolId
     * @param $content
     */
    public function __construct($mobiles, $schoolId, $content) {
    
        $this->targets = $mobiles;
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
                $school = School::find($this->schoolId);
                $departmentIds = array_merge(
                    [$school->department_id],
                    (new Department)->subDepartmentIds($school->department_id)
                );
                $userIds = array_unique(
                    DepartmentUser::whereIn('department_id', $departmentIds)->pluck('user_id')->toArray()
                );
                # 所有手机号码
                $targets = array_unique(explode(',', $this->targets));
                # 在指定学校通讯录内的手机号码
                $contacts = Mobile::whereIn('mobile', $targets)
                    ->whereIn('user_id', $userIds)
                    ->pluck('user_id', 'mobile')
                    ->toArray();
                # 创建发送日志
                $msl = [
                    'read_count'      => 0,
                    'received_count'  => 0,
                    'recipient_count' => count($targets),
                ];
                $mslId = MessageSendingLog::create($msl)->id;
                
                
                $message = new Message;
                $apiMessage = new ApiMessage;
                # 发送短信
                $mobiles = array_diff($targets, array_keys($contacts));
                $result = $message->sendSms($mobiles, $this->content . $school->signature);
                $apiMessage->log(
                    $mobiles, null, $mslId, 1, $this->content, 0, $result > 0 ? 0 : 1
                );
                
                # 发送微信
                $app = App::whereName('消息中心')->where('corp_id', $school->corp_id)->first()->toArray();
                $users = User::whereIn('id', array_values($contacts))->get();
                $userids = $users->pluck('userid')->toArray();
                $content = [
                    'touser' => implode('|', $userids),
                    'toparty' => '',
                    'agentid' => $app['agentid'],
                    'msgtype' => 'text',
                    'text' => ['content' => $this->content]
                ];
                $result = $this->sendMessage($school->corp, $app, $content);
                $message->log(
                    $users, null, $mslId, 'Third Party', json_encode($content),
                    $result, 0, 1, $app['id']
                );
            });
        } catch (Exception $e) {
            throw $e;
        }
    
    }
    
}
