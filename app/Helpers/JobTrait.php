<?php
namespace App\Helpers;

use App\Apis\Kinder;
use App\Events\JobResponse;
use App\Models\Corp;
use App\Models\Message;
use App\Models\MessageSendingLog;
use App\Models\User;
use App\Facades\Wechat;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Trait JobTrait
 * @package App\Helpers
 */
trait JobTrait {
    
    /**
     * 企业微信会员同步
     *
     * @param $data
     * @param $action - create/
     * @return array
     * @throws Exception
     */
    function syncMember($data, $action): array {
        
        $corps = Corp::whereIn('id', $data['corpIds'])->get();
        $results = [];
        foreach ($corps as $corp) {
            $results[$corp->id] = $this->operate(
                $corp->corpid, $corp->contact_sync_secret,
                $action == 'delete' ? $data['userid'] : $data,
                $action
            );
        }
        
        return $results;
        
    }
    
    /**
     * 同步卡德部门/人员
     *
     * @param string $type
     * @param array $response
     * @throws Exception
     */
    function syncKd($type, array $response) {
    
        $api = new Kinder();
        $response['title'] .= '卡德' . $type;
        $response['message'] .= '卡德';
        $name = '';
        switch ($this->action) {
            case 'create':
                $name = '新增';
                break;
            case 'update':
                $name = '编辑';
                break;
            case 'delete':
                $name = '删除';
                break;
            default:
                break;
        }
        $hasError = false;
        $result = $api->call($name . $type, $this->data);
        Log::debug(json_encode($result));
        if (!$result) {
            $hasError = true;
        } else {
            if (isset($result->{'code'})) {
                if ($result->{'code'}) {
                    $hasError = true;
                }
            } else {
                if ($result->{'result'}) {
                    $hasError = true;
                }
            }
        }
        if ($hasError) {
            $response['statusCode'] = HttpStatusCode::INTERNAL_SERVER_ERROR;
            $response['message'] = $result ? $result->{'msg'} : '接口错误';
        }
        if ($response['userId']) {
            event(new JobResponse($response));
        }
        
    }
    
    /**
     * 发送消息（微信、短信）
     *
     * @param Message $message
     * @return bool
     * @throws Throwable
     */
    function sendMessage(Message $message) {
    
        try {
            DB::transaction(function () use ($message) {
                $content = json_decode($message->content, true);
                $userIds = User::whereIn('userid', explode('|', $content['touser']))
                    ->pluck('id')->toArray();
                $departmentIds = explode('|', $content['toparty']);
                $msgType = $content['msgtype'];
    
                if ($msgType == 'sms') {
                    list($logUsers, $mobiles) = $message->smsTargets($userIds, $departmentIds);
                    $result = $message->sendSms($mobiles, $content['sms']);
                    $message->log($logUsers, $message, $result);
                    $this->inform($message, $result, $mobiles);
                } else {
                    /**
                     * @var Collection|User[] $wxTargets
                     * @var Collection|User[] $smsLogUsers
                     * @var Collection|User[] $wxLogUsers
                     * @var Collection|User[] $realTargetUsers
                     */
                    list($smsMobiles, $smsLogUsers, $wxTargets, $wxLogUsers, $realTargetUsers) =
                        $message->wxTargets($userIds, $departmentIds);
        
                    # step 1: 向已关注的用户（监护人、教职员工）发送微信
                    $subscribedTargets = $realTargetUsers->where('subscribed', 1);
                    if ($subscribedTargets->count()) {
                        $userids = $wxTargets->where('subscribed', 1)
                            ->pluck('userid')->toArray();
                        $content['touser'] = implode('|', $userids);
                        $result = $message->sendWx($message->app, $content);
                        $message->log($wxLogUsers, $message, $result);
                        $this->inform($message, $result, $subscribedTargets);
                    }
                    # step 2: 向未关注的用户（监护人、教职员工）发送短信
                    if (!empty($smsMobiles)) {
                        $urlcode = uniqid();
                        $sms = $msgType == 'text'
                            ? $content['text']['content']
                            : config('app.url') . '/sms/' . $urlcode;
                        $result = $message->sendSms($smsMobiles, $sms);
                        $message->log($smsLogUsers, $message, $result, $urlcode);
                        $this->inform($message, $result, $smsMobiles);
                    }
                }
            });
        } catch (Exception $e) {
            throw $e;
        }
        
        return true;
        
    }
    
    /**
     * 批量导入
     *
     * @param $job
     * @param $title
     * @return bool
     * @throws Throwable
     */
    function import($job, $title) {
    
        $response = [
            'userId'     => $job->userId,
            'title'      => __($title),
            'statusCode' => HttpStatusCode::OK,
            'message'    => __('messages.import_succeeded'),
        ];
        list($inserts, $updates, $illegals) = $job->{'validate'}($job->data);
        if (empty($updates) && empty($inserts)) {
            # 数据格式不正确，中止任务
            $response['statusCode'] = HttpStatusCode::NOT_ACCEPTABLE;
            $response['message'] = __('messages.invalid_data_format');
        } else {
            try {
                DB::transaction(function () use ($job, $inserts, $updates, $illegals, $title, &$response) {
                    event(new JobResponse([
                        'userId' => $job->userId,
                        'title' => __($title),
                        'statusCode' => HttpStatusCode::ACCEPTED,
                        'message' => !count($illegals)
                            ? sprintf(
                                __('messages.import_request_submitted'),
                                count($inserts), count($updates)
                            )
                            : sprintf(
                                __('messages.import_request_submitted') .
                                __('messages.import_illegals'),
                                count($inserts), count($updates), count($illegals)
                            )
                    ]));
                    # 插入数据
                    $job->{'insert'}($inserts);
                    # 更新数据
                    $job->{'update'}($updates);
                    # 生成错误数据excel文件
                    if (!empty($illegals)) {
                        $job->{'excel'}($illegals, 'illegals', '错误数据', false);
                        $response['url'] = 'uploads/' . date('Y/m/d/') . 'illegals.xlsx';
                    }
                });
            } catch (Exception $e) {
                $response['statusCode'] = $e->getCode();
                $response['message'] = $e->getMessage();
                Log::error(
                    get_class($e) .
                    '(code: ' . $e->getCode() . '): ' .
                    $e->getMessage() . ' at ' .
                    $e->getFile() . ' on line ' .
                    $e->getLine()
                );
            }
        }
        event(new JobResponse($response));
    
        return true;
        
    }
    
    /**
     * 同步会员信息
     *
     * @param string $corpid
     * @param string $secret
     * @param mixed $data
     * @param string $action
     * @return bool|mixed
     * @throws Exception
     */
    private function operate($corpid, $secret, $data, $action) {
        
        $token = Wechat::getAccessToken($corpid, $secret, true);
        if ($token['errcode']) { return $token; }
        $accessToken = $token['access_token'];
        if ($action != 'delete') { unset($data['corpIds']); }
        $action .= 'User';
        $result = json_decode(Wechat::$action($accessToken, $data));
        # 企业微信通讯录不存在指定的会员，则创建该会员
        if ($result->{'errcode'} == 60111 && $action == 'updateUser') {
            $result = json_decode(Wechat::createUser($accessToken, $data));
        }
        if (!$result->{'errcode'} && $action != 'deleteUser') {
            User::whereUserid($data['userid'])->first()->update(['synced' => 1]);
            if ($action == 'updateUser') {
                $member = json_decode(Wechat::getUser($accessToken, $data['userid']));
                if (!$member->{'errcode'} && $member->{'status'} == 1) {
                    User::whereUserid($data['userid'])->first()->update([
                        'avatar_url' => $member->{'avatar'},
                        'subscribed' => 1
                    ]);
                }
            }
        }
        $response = [
            'errcode' => $result->{'errcode'},
            'errmsg' => Constant::WXERR[$result->{'errcode'}]
        ];
        
        return $response;
        
    }
    
    /**
     * 返回指定用户对象所属的学校id
     *
     * @param User $user
     * @return int
     */
    function school_id(User $user) {
    
        return $user->educator
            ? $user->educator->school_id
            : $user->student->squad->grade->school_id;
        
    }
    
    /**
     * 创建消息发送日志并返回批次id
     *
     * @param $recipients
     * @return int|mixed
     */
    function mslId($recipients) {
    
        $msl = [
            'read_count'      => 0,
            'received_count'  => 0,
            'recipient_count' => $recipients,
        ];
        return MessageSendingLog::create($msl)->id;
        
    }
    
    /**
     * 生成并发送广播消息
     *
     * @param Message $message
     * @param mixed $result
     * @param mixed $targets - 发送对象数量
     */
    private function inform(Message $message, $result, $targets) {
        
        $userId = $message->s_user_id;
        if ($userId) {
            $response = [
                'userId'     => $userId,
                'title'      => __('messages.message.title'),
                'statusCode' => HttpStatusCode::OK,
            ];
            $msgTpl = 'messages.message.sent';
            if (isset($result['errcode'])) {
                $response['title'] = $response['title'] . '(微信)';
                if ($result['errcode']) {
                    $response['statusCode'] = HttpStatusCode::INTERNAL_SERVER_ERROR;
                    $response['message'] = $result['errmsg'];
                } else {
                    $total = $targets->count();
                    $failed = $message->failedUserIds($result['invaliduser'], $result['invalidparty']);
                    $succeeded = $total - count($failed);
                    if (!$succeeded) {
                        $response['statusCode'] = HttpStatusCode::INTERNAL_SERVER_ERROR;
                    }
                    if ($succeeded > 0 && $succeeded < $total) {
                        $response['statusCode'] = HttpStatusCode::ACCEPTED;
                    }
                    $response['message'] = sprintf(
                        __($msgTpl), $total, $succeeded, count($failed));
                }
            } else {
                $response['title'] = $response['title'] . '(短信)';
                if ($result > 0) {
                    $response['statusCode'] = HttpStatusCode::INTERNAL_SERVER_ERROR;
                    $response['message'] = sprintf(
                        __($msgTpl), count($targets), 0, count($targets)
                    );
                } else {
                    $response['message'] = sprintf(
                        __($msgTpl), count($targets), count($targets), 0);
                }
            }
            event(new JobResponse($response));
        }
        
    }
    
}