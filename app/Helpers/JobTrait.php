<?php
namespace App\Helpers;

use App\Events\JobResponse;
use App\Models\Corp;
use App\Models\User;
use App\Facades\Wechat;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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
     * 发送微信消息
     *
     * @param Corp $corp - 用于发送消息的企业对象
     * @param array $app - 应用详情
     * @param array $message - 消息详情
     * @return array|bool|mixed
     * @throws Exception
     */
    function sendMessage(Corp $corp, array $app, array $message) {
        
        $token = Wechat::getAccessToken($corp->corpid, $app['secret']);
        if ($token['errcode']) { return $token; }
        $result = json_decode(Wechat::sendMessage($token['access_token'], $message));
        
        return [
            'errcode' => $result->{'errcode'},
            'errmsg' => Constant::WXERR[$result->{'errcode'}],
            'invaliduser' => isset($result->{'invaliduser'}) ? $result->{'invaliduser'} : '',
            'invalidparty' => isset($result->{'invalidparty'}) ? $result->{'invalidparty'} : '',
        ];
        
    }
    
    /**
     * 批量导入
     *
     * @param $job
     * @param $title
     * @return bool
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
        # 企业微信通讯录不存在需要更新的会员，则创建该会员
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
    
}