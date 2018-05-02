<?php
namespace App\Jobs;

use App\Events\ContactSyncTrigger;
use App\Facades\Wechat;
use App\Helpers\HttpStatusCode;
use App\Models\Corp;
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
 * Class ManageWechatMember
 * @package App\Jobs
 */
class ManageWechatMember implements ShouldQueue {
    
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    
    protected $data, $action;
    
    const ACTIONS = [
        'create' => '创建',
        'update' => '更新',
        'delete' => '删除',
    ];
    
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
            'userId' => $this->data['userId'],
            'title' => self::ACTIONS[$this->action] . '企业微信会员',
            'statusCode' => HttpStatusCode::OK,
            'message' => __('messages.wechat_synced')
        ];
        $result = null;
        switch ($this->action) {
            case 'create':
                $result = json_decode(Wechat::createUser($token, $this->data));
                break;
            case 'update':
                $result = json_decode(Wechat::updateUser($token, $this->data));
                break;
            case 'delete':
                $result = json_decode(Wechat::delUser($token, $this->data));
                break;
            default:
                break;
        }
        if ($result->{'errcode'} == 0 && $this->action !== 'delete') {
            $user = User::whereUserid($this->data['userid'])->first();
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
