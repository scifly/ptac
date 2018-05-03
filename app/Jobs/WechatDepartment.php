<?php
namespace App\Jobs;

use App\Facades\Wechat;
use App\Helpers\ModelTrait;
use App\Models\Corp;
use App\Models\Department;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * 企业号部门管理
 *
 * Class WechatDepartment
 * @package App\Jobs
 */
class WechatDepartment implements ShouldQueue {
    
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, ModelTrait;
    
    protected $department, $action;
    
    /**
     * Create a new job instance.
     *
     * @param $department
     * @param $action
     */
    public function __construct(Department $department, $action) {
        
        $this->department = $department;
        $this->action = $action;
        
    }
    
    /**
     * Execute the job
     *
     * @return mixed|null
     */
    public function handle() {
    
        $departmentId = $this->department->id;
        $name = $this->department->name;
        $parent_id = $this->department->departmentType->name == '学校'
            ? 1 : $this->department->parent->id;
        $order = $this->department->order;
        $corp = Corp::find($this->department->corpId($departmentId));
        $token = Wechat::getAccessToken(
            $corp->corpid,
            $corp->contact_sync_secret
        );
        $result = null;
        switch ($this->action) {
            case 'create':
                $result = json_decode(
                    Wechat::createDept($token, $name, $parent_id, $order, $departmentId)
                );
                break;
            case 'update':
                $result = json_decode(
                    Wechat::updateDept($token, $departmentId, $name, $parent_id, $order)
                );
                break;
            default:
                $result = json_decode(
                    Wechat::delDept($token, $departmentId)
                );
                break;
        }
        
        return $result;
        
    }
    
}
