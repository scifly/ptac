<?php
namespace App\Jobs;

use App\Events\ContactSyncTrigger;
use App\Facades\Wechat;
use App\Helpers\Constant;
use App\Helpers\HttpStatusCode;
use App\Models\Corp;
use App\Models\Mobile;
use App\Models\School;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * 企业号会员管理
 *
 * Class WechatMember
 * @package App\Jobs
 */
class WechatMember implements ShouldQueue {
    
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    
    protected $member, $userId, $action;
    
    /**
     * Create a new job instance.
     *
     * @param User $member
     * @param $userId - 后台登录用户的id
     * @param $action - create/update/delete操作
     */
    public function __construct(User $member, $userId, $action) {
        
        $this->member = $member;
        $this->userId = $userId;
        $this->action = $action;
        
    }
    
    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle() {
        
        $user = User::whereUserid($this->member->userid)->first();
        switch ($user->group->name) {
            case '运营':
                $corps = Corp::all();
                foreach ($corps as $corp) {
                    $this->sync($corp->corpid, $corp->contact_sync_secret);
                }
                break;
            case '企业':
                $departmentIds = $user->departments->pluck('id')->toArray();
                $corp = Corp::whereDepartmentId($departmentIds[0])->first();
                $this->sync($corp->corpid, $corp->contact_sync_secret);
                break;
            case '学校':
                $departmentIds = $user->departments->pluck('id')->toArray();
                $corp = Corp::find(
                    School::whereDepartmentId($departmentIds[0])->first()->corp_id
                );
                $this->sync($corp->corpid, $corp->contact_sync_secret);
                break;
            case '学生':
                $corp = Corp::find($user->student->squad->grade->school->corp_id);
                $this->sync($corp->corpid, $corp->contact_sync_secret);
                break;
            case '监护人':
                $students = $user->custodian->students;
                $corpIds = [];
                foreach ($students as $student) {
                    $corpIds[] = $student->squad->grade->school->corp_id;
                }
                $corpIds = array_unique($corpIds);
                foreach ($corpIds as $corpId) {
                    $corp = Corp::find($corpId);
                    $this->sync($corp->corpid, $corp->contact_sync_secret);
                }
                break;
            default: # 教职员工或其他角色:
                $corp = Corp::find($user->educator->school->corp_id);
                $this->sync($corp->corpid, $corp->contact_sync_secret);
                break;
        }
        
    }
    
    /**
     * 同步企业微信会员
     *
     * @param $corpid
     * @param $secret
     */
    private function sync($corpid, $secret): void {
        
        $token = Wechat::getAccessToken($corpid, $secret, true);
        $response = [
            'userId' => $this->userId,
            'title' => Constant::SYNC_ACTIONS[$this->action] . '企业微信会员',
            'statusCode' => HttpStatusCode::OK,
            'message' => __('messages.wechat_synced')
        ];
        $params = $this->member->userid;
        if (in_array($this->action, ['create', 'update'])) {
            $params = [
                'userid' => $this->member->userid,
                'name' => $this->member->realname,
                'mobile' => head($this->member->mobiles->where('isdefault', 1)->pluck('mobile')->toArray()),
                
            ];
        }
        $result = null;
        
        switch ($this->action) {
            case 'create':
                $result = json_decode(Wechat::createUser($token, $this->member));
                break;
            case 'update':
                $result = json_decode(Wechat::updateUser($token, $this->member));
                break;
            case 'delete':
                $result = json_decode(Wechat::deleteUser($token, $this->member['userid']));
                break;
            default:
                break;
        }
        if ($result->{'errcode'} == 0 && $this->action !== 'delete') {
            $user = User::whereUserid($this->member['userid'])->first();
            $user->update(['synced' => 1]);
        }
        
        if ($result->{'errcode'} != 0) {
            $response['statusCode'] = HttpStatusCode::INTERNAL_SERVER_ERROR;
            $response['message'] = $result->{'errcode'} . ' : '
                . Wechat::ERRCODES[intval($result->{'errcode'})];
        }
        Log::debug('message: ' . $response['message']);
        
        event(new ContactSyncTrigger($response));
    
    }
    
}
