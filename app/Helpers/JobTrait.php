<?php
namespace App\Helpers;

use App\Models\{Message, Mobile, School, User};
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
     * @return array
     * @throws Throwable
     */
    function send(Message $message, array $response = []) {
        
        $results = [];
        $fields = ['message', 'result', 'targets'];
        try {
            DB::transaction(function () use ($message, $results, $fields) {
                $content = json_decode($message->content, true);
                $userIds = User::whereIn('userid', explode('|', $content['touser']))
                    ->pluck('id')->toArray();
                $departmentIds = explode('|', $content['toparty']);
                $msgType = $content['msgtype'];
                if ($msgType == 'sms') {
                    $mobiles = $message->smsTargets($userIds, $departmentIds);
                    $sms = $content['sms'];
                } else {
                    /**
                     * @var Collection|User[] $wxTargets
                     * @var Collection|User[] $realTargetUsers
                     */
                    list($mobiles, $wxTargets, $realTargetUsers) = $message->wxTargets($userIds, $departmentIds);
                    # step 1: 向已关注的用户（监护人、教职员工）发送微信
                    if ($realTargetUsers->where('subscribed', 1)->count()) {
                        $userids = $wxTargets->where('subscribed', 1)->pluck('userid')->toArray();
                        # 微信消息发送的会员对象每次不得超过1000名
                        $groups = array_chunk($userids, 1000);
                        foreach ($groups as $group) {
                            $content['touser'] = implode('|', $group);
                            $result = $message->sendWx($message->app, $content);
                            $message->log($users = User::whereIn('userid', $group)->get(), $message, $result);
                            $results[] = array_combine($fields, [$message, $result, $users]);
                        }
                    }
                    # step 2: 向未关注的用户（监护人、教职员工）发送短信
                    if (!empty($mobiles)) {
                        $urlcode = uniqid();
                        $sms = $msgType == 'text' ? $content['text']['content']
                            : config('app.url') . '/sms/' . $urlcode;
                    }
                }
                if (!empty($mobiles)) {
                    $chunks = array_chunk($mobiles, 2000);
                    foreach ($chunks as $chunk) {
                        $result = $message->sendSms($chunk, $sms ?? '');
                        $userIds = Mobile::whereIn('mobile', $chunk)->pluck('user_id');
                        $users = User::whereIn('id', $userIds)->get();
                        $message->log($users, $message, $result, $urlcode ?? null);
                        $results[] = array_combine($fields, [$message, $result, $chunk]);
                    }
                }
            });
        } catch (Exception $e) {
            $this->eHandler($e, $response);
            throw $e;
        }
        
        return $results;
        
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
                        sprintf($tpl, $nInserts, $nUpdates, $nIllegals),
                    ]));
                    # 插入、更新记录
                    list($inserts, $updates) = $records;
                    array_map(
                        function ($records, $action) use ($job) {
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
     * 返回消息发送结果
     *
     * @param array $results
     * @return array
     */
    function inform(array $results) {
        
        $total = $success = $failure = 0;
        $sms = $wx = ['total' => 0, 'success' => 0, 'failure' => 0];
        foreach ($results as $result) {
            $targets = count($result['targets']);
            $total += $targets;
            if (is_array($result['result'])) {
                $wx['total'] += $targets;
                /** @var Message $message */
                $message = $result['message'];
                $failed = count(
                    $message->failedUserIds(
                        $result['result']['invaliduser'],
                        $result['result']['invalidparty']
                    )
                );
                $succeeded = $targets - $failed;
                $wx['success'] += $succeeded;
                $wx['failure'] += $failed;
                $success += $succeeded;
                $failure += $failed;
            } else {
                $sms['total'] += $targets;
                if ($result['result'] > 0) {
                    $sms['success'] += $targets;
                    $success += $targets;
                } else {
                    $sms['failure'] += $targets;
                    $failure += $targets;
                }
            }
        }
        !$failure ?: $code = $failure == $total
            ? HttpStatusCode::INTERNAL_SERVER_ERROR
            : HttpStatusCode::ACCEPTED;
        $msg = sprintf(
            __('messages.message.sent'),
            $total, $success, $failure,
            $wx['total'], $wx['success'], $wx['failure'],
            $sms['total'], $sms['success'], $sms['failure']
        );
        
        return [$code ?? HttpStatusCode::OK, $msg];
        
    }
    
}