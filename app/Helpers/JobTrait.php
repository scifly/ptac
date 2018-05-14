<?php
namespace App\Helpers;

use App\Models\Corp;
use App\Models\User;
use App\Models\School;
use App\Facades\Wechat;

trait JobTrait {
    
    /**
     * 企业微信会员同步
     *
     * @param User $member - 需同步的会员对象
     * @param $action - create/
     * @return array
     */
    function syncMember(User $member, $action): array {
        
        $user = User::whereUserid($member->userid)->first();
        switch ($user->group->name) {
            case '运营':
                $corps = Corp::all();
                break;
            case '企业':
                $departmentIds = $user->departments->pluck('id')->toArray();
                $corps = Collect([Corp::whereDepartmentId($departmentIds[0])->first()]);
                break;
            case '学校':
                $departmentIds = $user->departments->pluck('id')->toArray();
                $corps = Collect([Corp::find(
                    School::whereDepartmentId($departmentIds[0])->first()->corp_id
                )]);
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
            default: # 教职员工或其他角色:
                $corps = Collect([Corp::find($user->educator->school->corp_id)]);
                break;
        }
        $results = [];
        foreach ($corps as $corp) {
            $results[$corp->id] = $this->operate(
                $corp->corpid, $corp->contact_sync_secret, $member, $action
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
            'invaliduser' => $result->{'invaliduser'} ?? '',
            'invalidparty' => $result->{'invalidparty'} ?? '',
            'invalidtag' => $result->{'invalidtag'} ?? ''
        ];
        
    }
    
    /**
     * 同步会员信息
     *
     * @param $corpid
     * @param $secret
     * @param User $member
     * @param $action
     * @return bool|mixed
     */
    private function operate($corpid, $secret, User $member, $action) {
        
        $token = Wechat::getAccessToken($corpid, $secret, true);
        if ($token['errcode']) { return $token; }
        $accessToken = $token['access_token'];
        $params = $member->userid;
        if (in_array($action, ['create', 'update'])) {
            $params = [
                'userid' => $member->userid,
                'name' => $member->realname,
                'english_name' => $member->english_name,
                'position' => $member->group->name,
                'mobile' => head(
                    $member->mobiles
                        ->where('isdefault', 1)
                        ->pluck('mobile')->toArray()
                ),
                'email' => $member->email,
                'department' => in_array($member->group->name, ['运营', '企业'])
                    ? [1] : $member->departments->pluck('id')->toArray(),
                'gender' => $member->gender,
                'enable' => $member->enabled
            ];
        }
        $action .= 'User';
        $result = json_decode(Wechat::$$action($accessToken, $params));
        if (!$result->{'errcode'} && $action !== 'delete') {
            $user = User::whereUserid($member['userid'])->first();
            $user->update(['synced' => 1]);
        }
        
        return [
            'errcode' => $result->{'errcode'},
            'errmsg' => Wechat::ERRMSGS[$result->{'errcoce'}]
        ];
    }
    
}

