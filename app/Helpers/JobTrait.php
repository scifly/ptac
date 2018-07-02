<?php
namespace App\Helpers;

use App\Models\Corp;
use App\Models\User;
use App\Facades\Wechat;
use Exception;

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