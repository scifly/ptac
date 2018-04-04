<?php
namespace App\Jobs;

use App\Events\ContactSyncTrigger;
use App\Facades\Wechat;
use App\Models\Corp;
use App\Models\School;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * 企业号会员管理
 *
 * Class ManageWechatMember
 * @package App\Jobs
 */
class ManageWechatMember implements ShouldQueue {
    
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    
    protected $data, $action;
    
    /**
     * Create a new job instance.
     *
     * @param mixed $data
     * @param $action
     */
    public function __construct($data, $action) {
        
        $this->data = $data;
        $this->action = $action;
        
    }
    
    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle() {
        
        $user = User::whereUserid($this->data['userid'])->first();
        switch ($user->group->name) {
            case '运营':
                $corps = Corp::all();
                foreach ($corps as $corp) {
                    $this->sync($corp->corpid, $corp->contact_sync_secret);
                }
                break;
            case '企业':
                $departmentIds = $user->departments->toArray();
                $corp = Corp::whereDepartmentId($departmentIds[0])->first();
                $this->sync($corp->corpid, $corp->contact_sync_secret);
                break;
            case '学校':
                $departmentIds = $user->departments->toArray();
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
     * @param $corpId
     * @param $secret
     */
    private function sync($corpId, $secret): void {
        
        $token = Wechat::getAccessToken($corpId, $secret, true);
        $data = [];
        switch ($this->action) {
            case 'create':
                $result = json_decode(Wechat::createUser($token, $this->data));
                if ($result->{'errcode'} == 0) {
                    $user = User::whereUserid($this->data['userid'])->first();
                    $user->update(['synced' => 1]);
                    $data['message'] = '已同步到企业微信后台';
                } else {
                    $data['message'] = $result->{'errmsg'};
                }
                break;
            case 'update':
                Wechat::updateUser($token, $this->data);
                break;
            default:
                Wechat::delUser($token, $this->data);
                break;
        }
        event(new ContactSyncTrigger($data));
    
    }
    
}
