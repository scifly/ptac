<?php
namespace App\Jobs;

use App\Events\JobResponse;
use App\Helpers\HttpStatusCode;
use App\Helpers\JobTrait;
use App\Helpers\ModelTrait;
use App\Models\App;
use App\Models\Corp;
use App\Models\Message;
use App\Models\MessageType;
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
        
        try {
            DB::transaction(function () {
                $message = new Message;
                $response = [
                    'userId'     => $this->userId,
                    'title'      => __('messages.message.title'),
                    'statusCode' => HttpStatusCode::OK,
                    'message'    => '',
                ];
                $touser = implode('|', User::whereIn('id', $this->data['user_ids'])->pluck('userid')->toArray());
                $toparty = $this->data['dept_ids'];
                $this->data['s_user_id'] = $this->userId ?? 0;
                $msgType = $this->data['type'];
                /** @var array $content - 消息内容 */
                $content = [
                    'toparty' => implode('|', $this->data['dept_ids']),
                    'msgtype' => $msgType,
                    $msgType => $this->data[$msgType]
                ];
                if ($msgType == 'sms') {
                    list($logUsers, $mobiles) = $message->smsTargets($this->data['user_ids'], $toparty);
                    $this->data['msl_id'] = $this->mslId(count($mobiles));
                    $this->sendSms(
                        $content, $mobiles, $touser, $logUsers,$response
                    );
                    if ($this->userId) {
                        event(new JobResponse($response));
                    }
                } else {
                    /**
                     * @var Collection|User[] $wxTargets
                     * @var Collection|User[] $smsLogUsers
                     * @var Collection|User[] $wxLogUsers
                     * @var Collection|User[] $realTargetUsers
                     */
                    list($smsMobiles, $smsLogUsers, $wxTargets, $wxLogUsers, $realTargetUsers) = $message->wxTargets(
                        $this->data['user_ids'], $toparty
                    );
                    $this->data['msl_id'] = $this->mslId($smsLogUsers->count() + $wxLogUsers->count());
        
                    # step 1: 向已关注的用户（监护人、教职员工）发送微信
                    if ($realTargetUsers->where('subscribed', 1)->count()) {
                        $results = [];
                        foreach ($this->apps as $app) {
                            # 发送微信消息
                            $content = array_merge($content, ['agentid' => $app['agentid']]);
                            $this->data['sent'] = $results[$app['id']] = $this->sendMessage(
                                $this->corp, $app,
                                array_merge(
                                    $content,
                                    ['touser' => implode('|', $wxTargets->pluck('userid')->toArray())]
                                )
                            );
                            # 创建消息发送日志
                            $this->data['content'] = json_encode(array_merge($content, ['touser' => $touser]));
                            $this->data['app_id'] = $app['id'];
                            $message->log($wxLogUsers, $this->data);
                        }
                        # 创建并发送广播消息
                        $this->wxResponse(
                            $results, $realTargetUsers->count() - count($smsMobiles),$response
                        );
                        if ($this->userId) {
                            event(new JobResponse($response));
                        }
                    }
        
                    # step 2: 向未关注用户（监护人、教职员工）发送短信
                    if (!empty($smsMobiles)) {
                        $content['msgtype'] = 'sms';
                        # todo: 生成微信消息详情url
                        $content['sms'] = 'url_to_wechat_message';
                        $this->data['app_id'] = 0;
                        $this->data['title'] = MessageType::find($this->data['message_type_id'])->name . '(短信)';
                        # 发送短信并创建广播消息
                        $this->sendSms(
                            $content, $smsMobiles, $touser, $smsLogUsers, $response
                        );
                        # 发送广播消息
                        if ($this->userId) {
                            event(new JobResponse($response));
                        }
                    }
                }
            });
        } catch (Exception $e) {
            throw $e;
        }
        
        return true;
        
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
        $result = $message->sendSms($mobiles, $content['sms']);
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
