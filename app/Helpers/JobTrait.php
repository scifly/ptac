<?php
namespace App\Helpers;

use App\Models\Corp;
use App\Models\User;
use App\Models\School;
use App\Facades\Wechat;
use Illuminate\Support\Facades\Log;

trait JobTrait {
    
    /**
     * 企业微信会员同步
     *
     * @param $data
     * @param $action - create/
     * @return array
     */
    function syncMember($data, $action): array {
        
        $user = User::whereUserid($action == 'delete'? $data : $data['userid'])->first();
        switch ($user->group->name) {
            case '运营':
                $corps = Corp::all();
                break;
            case '企业':
                $departmentIds = $user->departments->pluck('id')->toArray();
                $corps = Collect([Corp::whereDepartmentId($departmentIds[0])->first()]);
                break;
            case '学生':
                $corps = Collect([Corp::find($user->student->squad->grade->school->corp_id)]);
                break;
            case '监护人':
                $students = $user->custodian->students;
                $corpIds = [];
                foreach ($students as $student) {
                    $corpIds[] = $student->squad->grade->school->corp_id;
                }
                $corps = Corp::whereIn('id', array_unique($corpIds))->get();
                break;
            default: # 学校、教职员工或其他角色:
                $corps = Collect([Corp::find($user->educator->school->corp_id)]);
                break;
        }
        $results = [];
        foreach ($corps as $corp) {
            $results[$corp->id] = $this->operate(
                $corp->corpid, $corp->contact_sync_secret, $data, $action
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
     */
    function sendMessage(Corp $corp, array $app, array $message) {
        
        $token = Wechat::getAccessToken($corp->corpid, $app['secret']);
        if ($token['errcode']) { return $token; }
        $result = json_decode(Wechat::sendMessage($token['access_token'], $message));
        
        return [
            'errcode' => $result->{'errcode'},
            'errmsg' => Wechat::ERRMSGS[$result->{'errcode'}],
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
     */
    private function operate($corpid, $secret, $data, $action) {
        
        $token = Wechat::getAccessToken($corpid, $secret, true);
        if ($token['errcode']) { return $token; }
        $accessToken = $token['access_token'];
        $action .= 'User';
        $result = json_decode(Wechat::$action($accessToken, $data));
        if (!$result->{'errcode'} && $action != 'deleteUser') {
            User::whereUserid($data['userid'])->first()->update(['synced' => 1]);
        }
        Log::debug(json_encode([
            'errcode' => $result->{'errcode'},
            'errmsg' => Wechat::ERRMSGS[$result->{'errcode'}]
        ]));
        return [
            'errcode' => $result->{'errcode'},
            'errmsg' => Wechat::ERRMSGS[$result->{'errcode'}]
        ];
    }
    
}