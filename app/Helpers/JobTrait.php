<?php
namespace App\Helpers;

use App\Models\{Message, MessageSendingLog, School, User};
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\{DB};
use Pusher\PusherException;
use Throwable;

/**
 * Trait JobTrait
 * @package App\Helpers
 */
trait JobTrait {
    
    /**
     * 发送消息（微信、短信）
     *
     * @param Message $message
     * @param array $response
     * @return bool
     * @throws Throwable
     */
    function send(Message $message, array $response = []) {
        
        try {
            DB::transaction(function () use ($message, $response) {
                $content = json_decode($message->content, true);
                $userIds = User::whereIn('userid', explode('|', $content['touser']))
                    ->pluck('id')->toArray();
                $departmentIds = explode('|', $content['toparty']);
                $msgType = $content['msgtype'];
                if ($msgType == 'sms') {
                    list($logUsers, $mobiles) = $message->smsTargets($userIds, $departmentIds);
                    $result = $message->sendSms($mobiles, $content['sms']);
                    $message->log($logUsers, $message, $result);
                    $this->inform($message, $result, $mobiles, $response);
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
                        $this->inform($message, $result, $subscribedTargets, $response);
                    }
                    # step 2: 向未关注的用户（监护人、教职员工）发送短信
                    if (!empty($smsMobiles)) {
                        $urlcode = uniqid();
                        $sms = $msgType == 'text'
                            ? $content['text']['content']
                            : config('app.url') . '/sms/' . $urlcode;
                        $result = $message->sendSms($smsMobiles, $sms);
                        $message->log($smsLogUsers, $message, $result, $urlcode);
                        $this->inform($message, $result, $smsMobiles, $response);
                    }
                }
            });
        } catch (Exception $e) {
            $this->eHandler($e, $response);
            throw $e;
        }
        
        return true;
        
    }
    
    /**
     * 批量导入
     *
     * @param $job
     * @param array $response
     * @return bool
     * @throws PusherException
     * @throws Throwable
     */
    function import($job, array $response) {
        
        $broadcaster = new Broadcaster();
        list($inserts, $updates, $illegals) = $job->{'validate'}($job->data);
        # 生成错误数据excel文件
        if (!empty($illegals)) {
            try {
                $job->{'excel'}($illegals, 'illegals', '错误数据', false);
                $response['url'] = 'uploads/' . date('Y/m/d/') . 'illegals.xlsx';
            } catch (Exception $e) {
                $response['statusCode'] = HttpStatusCode::INTERNAL_SERVER_ERROR;
                $response['message'] = $e->getMessage();
                $broadcaster->broadcast($response);
                throw $e;
            }
        }
        if (empty($updates) && empty($inserts)) {
            # 数据格式不正确，中止任务
            $response['statusCode'] = HttpStatusCode::NOT_ACCEPTABLE;
            $response['message'] = __('messages.invalid_data_format');
        } else {
            $records = [$inserts, $updates, $illegals];
            try {
                DB::transaction(function () use ($job, $records, &$response, $broadcaster) {
                    # 发送广播消息
                    list($nInserts, $nUpdates, $nIllegals) = array_map('count', $records);
                    $tpl = __('messages.import_request_submitted') .
                        (!$nIllegals ? '' : __('messages.import_illegals'));
                    $broadcaster->broadcast(array_combine(Constant::BROADCAST_FIELDS, [
                        $response['title'], $job->{'userId'}, HttpStatusCode::ACCEPTED,
                        sprintf($tpl, $nInserts, $nUpdates, $nIllegals)
                    ]));
                    # 插入、更新记录
                    list($inserts, $updates) = $records;
                    array_map(
                        function ($records, $action) use($job) {
                            empty($records) ?: $job->{$action}($records);
                        }, [$inserts, $updates], ['insert', 'update']
                    );
                });
            } catch (Exception $e) {
                $this->eHandler($e, $response);
                throw $e;
            }
        }
        $broadcaster->broadcast($response);
        
        return true;
        
    }
    
    /**
     * 同步至第三方合作伙伴通讯录
     *
     * @param $action
     * @param $data
     * @param $response
     * @param null $departmentId - 同步部门
     */
    function apiSync($action, $data, $response, $departmentId = null) {
        
        $type = $departmentId ? '部门' : '人员';
        foreach ($data['schoolIds'] as $schoolId) {
            $userIds = School::find($schoolId)->user_ids;
            if ($userIds) {
                foreach (explode(',', $userIds) as $userId) {
                    $className = 'App\\Apis\\' . ucfirst(User::find($userId)->position);
                    $api = new $className($type, $action, $data, $response);
                    $api->{'sync'}();
                }
            }
        }
        
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
     * 任务失败处理器
     *
     * @param array $response
     * @param Exception $exception
     * @throws PusherException
     */
    function eHandler(Exception $exception, array $response = []) {
        
        if (!empty($response)) {
            $response['statusCode'] = HttpStatusCode::INTERNAL_SERVER_ERROR;
            $response['message'] = $exception->getMessage();
            (new Broadcaster)->broadcast($response);
        }
        
    }
    
    /**
     * 生成并发送广播消息
     *
     * @param Message $message
     * @param mixed $result
     * @param mixed $targets - 发送对象数量
     * @param array $response
     * @throws PusherException
     */
    private function inform(Message $message, $result, $targets, array $response = []) {
        
        if ($message->s_user_id && !empty($response)) {
            $msgTpl = 'messages.message.sent';
            $response['title'] .= isset($result['errcode']) ? '(微信)' : '(短信)';
            if (isset($result['errcode'])) {
                if ($result['errcode']) {
                    $response['statusCode'] = HttpStatusCode::INTERNAL_SERVER_ERROR;
                    $response['message'] = $result['errmsg'];
                } else {
                    $total = $targets->count();
                    $failed = $message->failedUserIds($result['invaliduser'], $result['invalidparty']);
                    $succeeded = $total - count($failed);
                    if (!$succeeded) {
                        $response['statusCode'] = HttpStatusCode::INTERNAL_SERVER_ERROR;
                    } else if ($succeeded > 0 && $succeeded < $total) {
                        $response['statusCode'] = HttpStatusCode::ACCEPTED;
                    }
                    $response['message'] = sprintf(
                        __($msgTpl), $total, $succeeded, count($failed));
                }
            } else {
                $result == 0 ?: $response['statusCode'] = HttpStatusCode::INTERNAL_SERVER_ERROR;
                $response['message'] = sprintf(
                    __($msgTpl), count($targets),
                    $result == 0 ? 0 : count($targets),
                    $result > 0 ? count($targets) : 0
                );
            }
            (new Broadcaster)->broadcast($response);
        }
        
    }
    
}