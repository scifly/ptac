<?php
namespace App\Jobs;

use App\Facades\Wechat;
use App\Models\App;
use App\Models\Corp;
use App\Models\School;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Mockery\Exception;

/**
 * 企业号部门管理
 *
 * Class ManageWechatDepartment
 * @package App\Jobs
 */
class ManageWechatDepartment implements ShouldQueue {
    
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    
    protected $department, $action, $school;
    
    /**
     * Create a new job instance.
     *
     * @param $department
     * @param $action
     */
    public function __construct($department, $action) {
        $this->department = $department;
        $this->action = $action;
    }
    
    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle() {

        $schoolId = School::id();
        if ($schoolId != 0) {
            $school = School::find($schoolId);
            $corpMenuId = $school->menu->parent_id;
            $corp = Corp::whereMenuId($corpMenuId)->first();
            $corpId = $corp->corpid;
        } else {
            $corp = Corp::find(1);
            $corpId = $corp->corpid;
        }
        $contactSync = App::whereAgentid('999')->first();
        $secret = $contactSync->secret;
        $name = $this->department->name;
        $parent_id = $this->department->departmentType->name == '学校'
            ? 1 : $this->department->parent->id;
        $order = $this->department->order;
        $departmentId = $this->department->id;
        $token = Wechat::getAccessToken($corpId, $secret);
        switch ($this->action) {
            case 'create':
                Wechat::createDept(
                    $token, $name, $parent_id, $order, $departmentId
                );
                break;
            case 'update':
                Wechat::updateDept(
                    $token, $departmentId, $name, $parent_id, $order
                );
                break;
            default:
                Wechat::delDept($token, $departmentId);
                break;
        }
        
    }

    
}
