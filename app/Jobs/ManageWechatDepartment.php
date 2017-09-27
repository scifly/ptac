<?php
namespace App\Jobs;

use App\Facades\Wechat;
use App\Models\Corp;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Mockery\Exception;

/**
 * 企业号部门管理
 *
 * Class ManageWechatDepartment
 * @package App\Jobs
 */
class ManageWechatDepartment implements ShouldQueue {
    
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    
    protected $department, $action;
    
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
        
        $corp = new Corp();
        $corps = $corp::whereName('万浪软件')->first();
        $corpId = $corps->corpid;
        $secret = $corps->corpsecret;
        $name = $this->department->name;
        $parent_id = $this->department->departmentType->name == '学校'
            ? 1 : $this->department->parent->id;
        $order = $this->department->order;
        $departmentId = $this->department->id;
        $dir = dirname(__FILE__);
        $path = substr($dir, 0, stripos($dir, 'app/Jobs'));
        $tokenFile = $path . 'public/token.txt';
        $token = Wechat::getAccessToken($tokenFile, $corpId, $secret);
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
    
    public function failed(Exception $e) {
    
    }
    
}
