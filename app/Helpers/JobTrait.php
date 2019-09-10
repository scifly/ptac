<?php
namespace App\Helpers;

use App\Models\{Message, Openid, School, User};
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
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
     * @return array
     * @throws Throwable
     */
    function send(Message $message) {
    
        try {
            DB::transaction(function () use ($message, &$results) {
                $targetSize = ['ent' => 1000, 'sms' => 2000];
                $logs = [];
                [$platform, $sms, $wechat] = $message->targets($message);
                
                # 发送微信
                $size = $platform == 1 ? $targetSize['ent'] : 1;
                [$content, $tousers] = $wechat;
                /** @var Collection $tousers */
                foreach ($tousers->chunk($size) as $group) {
                    if ($platform == 1) {
                        $users = User::whereIn('ent_attrs->userid', $group);
                    } else {
                        $userIds = Openid::whereIn('openid', $group[0])->pluck('user_id');
                        $users = User::whereIn('id', $userIds);
                    }
                    $logs[] = [$message->sendWx($message, $content), $users->get()];
                }
                # 发送短信
                [$content, $mobiles] = $sms;
                $size = $targetSize['sms'];
                /** @var Collection $mobiles */
                foreach ($mobiles->pluck('mobile')->chunk($size) as $group) {
                    $logs[] = [
                        $message->sendSms($group, $content, $message->s_user_id),
                        User::whereIn('mobile', $group)->get()
                    ];
                }
                # 记录发送日志
                foreach ($logs as $log) {
                    [$result, $users] = $log;
                    $this->log($results, $result, $message, $users);
                }
            });
        } catch (Exception $e) {
            $this->eHandler($this, $e);
            throw $e;
        }
        
        return $results;
        
    }
    
    /**
     * 批量导入
     *
     * @param $job
     * @throws Throwable
     */
    function import($job) {
        
        try {
            DB::transaction(function () use ($job) {
                [$inserts, $updates, $illegals] = $job->{'validate'}($job->data);
                # 生成错误数据excel文件
                if (!empty($illegals)) {
                    $job->{'excel'}($illegals, 'illegals', '错误数据', false);
                    $job->{'response'}['url'] = $this->filePath('illegals') . '.xlsx';
                }
                throw_if(
                    empty($updates) && empty($inserts),
                    new Exception(__('messages.invalid_data_format'))
                );
                [$nInserts, $nUpdates, $nIllegals] = array_map(
                    'count', [$inserts, $updates, $illegals]
                );
                $tpl = join([
                    __('messages.import_request_submitted'),
                    (!$nIllegals ? '' : __('messages.import_illegals')),
                ]);
                $job->{'broadcaster'}->broadcast(
                    array_combine(Constant::BROADCAST_FIELDS, [
                        $job->{'userId'}, $job->{'response'}['title'], HttpStatusCode::ACCEPTED,
                        sprintf($tpl, $nInserts, $nUpdates, $nIllegals),
                    ])
                );
                # 插入、更新记录
                array_map(
                    function ($records, $action) use ($job) {
                        empty($records) ?: $job->{$action}($records);
                    }, [$inserts, $updates], ['insert', 'update']
                );
            });
        } catch (Exception $e) {
            throw $e;
        }
        
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
        
        foreach ($data['schoolIds'] as $schoolId) {
            if ($userIds = School::find($schoolId)->user_ids) {
                foreach (explode(',', $userIds) as $userId) {
                    $className = 'App\\Apis\\' . ucfirst(
                            json_decode(User::find($userId)->api_attrs, true)['classname']
                        );
                    $api = new $className(
                        $departmentId ? '部门' : '人员',
                        $action, $data, $response
                    );
                    $api->{'sync'}();
                }
            }
        }
        
    }
    
    /**
     * 任务失败处理器
     *
     * @param $job
     * @param Exception $e
     * @throws Exception
     */
    function eHandler($job, Exception $e) {
        
        $job->{'response'}['statusCode'] = HttpStatusCode::INTERNAL_SERVER_ERROR;
        $job->{'response'}['message'] = $e->getMessage();
        $job->{'broadcaster'}->broadcast($job->{'response'});
        throw $e;
        
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
    
    /**
     * @param $results
     * @param $result
     * @param Message $message
     * @param $users
     * @throws Throwable
     */
    private function log(&$results, $result, Message $message, $users) {
        
        $message->log($users, $message, $result);
        $results[] = array_combine(
            ['message', 'result', 'targets'],
            [$message, $result, $users]
        );
        
    }
    
}