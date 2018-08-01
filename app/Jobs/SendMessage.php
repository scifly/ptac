<?php
namespace App\Jobs;

use App\Events\JobResponse;
use App\Helpers\HttpStatusCode;
use App\Helpers\JobTrait;
use App\Helpers\ModelTrait;
use App\Models\App;
use App\Models\Corp;
use App\Models\Message;
use App\Models\Mobile;
use App\Models\User;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Collection;
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
    
    protected $data, $userId, $corp, $apps, $schoolId;
    
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
     * 消息发送任务
     *
     * @throws Exception
     * @throws Throwable
     */
    public function handle() {
        
        $message = new Message();
        $response = [
            'userId'     => $this->userId,
            'title'      => __('messages.message.title'),
            'statusCode' => HttpStatusCode::OK,
            'message'    => '',
        ];
        /** @var Collection|User[] $users - 需要记录消息发送日志的用户（学生、教职员工） */
        /** @var array $targets - 监护人、教职员工列表 */
        /** @var array $mobiles - 监护人、教职员工手机号码列表 */
        list($users, $targets, $mobiles) = $message->targets(
            $this->data['user_ids'], $this->data['dept_ids']
        );
        $this->data['s_user_id'] = $this->userId ?? 0;
        # 学校id
        $this->schoolId = $this->school_id($users->first());
        # 创建发送日志
        $this->data['msl_id'] = $this->mslId(count($mobiles));
        # 需记录消息发送日志的userid列表
        $touser = implode(
            '|', User::whereIn('id', $this->data['user_ids'])->pluck('userid')->toArray()
        );
        $msgType = $this->data['type'];
        /** @var array $content - 消息内容 */
        $content = [
            'toparty' => implode('|', $this->data['dept_ids']),
            'msgtype' => $msgType,
            $msgType => $this->data[$msgType]
        ];
        if ($msgType == 'sms') {
            $this->sendSms(
                $content, $mobiles, $touser, $users,$response
            );
        } else {
            # step 1: 向未关注用户（监护人、教职员工）发送短信
            $users = $targets[0];
            $mobiles = Mobile::whereIn('user_id', $users->pluck('id')->toArray())
                ->where(['isdefault' => 1, 'enabled' => 1])->pluck('mobile')->toArray();
            $this->data['type'] = 'sms';
            $this->data['sms'] = 'url_to_wechat_message'; # todo: 生成微信消息详情url
            $this->sendSms(
                $content, $mobiles, $touser, $this->logUsers($users), $response
            );

            # step 2: 向已关注的用户（监护人、教职员工）发送微信
            $results = [];
            $users = $targets[1];
            foreach ($this->apps as $app) {
                $content = array_merge($content, ['agentid' => $app['agentid']]);
                # 实际接收微信消息的用户（监护人、教职员工）列表
                $userids = $users->pluck('userid')->toArray();
                $this->data['sent'] = $results[$app['id']] = $this->sendMessage(
                    $this->corp, $app, array_merge($content, [
                        'touser' => implode('|', $userids)
                    ])
                );
                $this->data['content'] = json_encode(
                    array_merge($content, ['touser' => $touser])
                );
                $this->data['app_id'] = $app['id'];
                $message->log($this->logUsers($users), $this->data);
            }
            # 创建广播消息
            $this->wxResponse($results, $users->count(),$response);
        }
        # 发送广播消息
        if ($this->userId) {
            event(new JobResponse($response));
        }
        
    }
    
    /**
     *
     * @param $content
     * @param $mobiles
     * @param $touser
     * @param $users
     * @param $response
     * @throws Throwable
     */
    private function sendSms($content, $mobiles, $touser, $users, &$response) {
    
        $message = new Message;
        # 发送短信消息 & 创建用户消息发送日志
        $result = $message->sendSms($mobiles, $this->data['sms']);
        $this->data['sent'] = $this->data['read'] = $result <= 0;
        $this->data['content'] = json_encode(
            array_merge($content, ['touser' => $touser, 'agentid' => 0])
        );
        $message->log($users, $this->data);
        # 创建广播消息
        if ($result > 0) {
            $response['statusCode'] = HttpStatusCode::INTERNAL_SERVER_ERROR;
            $response['message'] = __('messages.message.sms_send_failed');
        } else {
            $response['message'] = sprintf(
                __('messages.message.sent'),
                count($mobiles), count($mobiles), 0
            );
        }
        
    }
    
    /**
     * 生成微信发送结果
     *
     * @param array $results
     * @param $total
     * @param $response
     */
    private function wxResponse(array $results, $total, &$response) {
    
        if (sizeof($results) == 1) {
            $result = $results[key($results)];
            if ($result['errcode']) {
                $response['statusCode'] = HttpStatusCode::INTERNAL_SERVER_ERROR;
                $response['message'] = $result['errmsg'];
            } else {
                $failed = count((new Message)->failedUserIds(
                    $result['invaliduser'], $result['invalidparty'])
                );
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
    
}
