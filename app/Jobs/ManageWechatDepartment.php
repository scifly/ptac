<?php

namespace App\Jobs;

use App\Facades\Wechat;
use App\Models\Corp;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Mockery\Exception;

class ManageWechatDepartment implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $department, $action;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($department, $action)
    {
        $this->department = $department;
        $this->action = $action;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $corp = new Corp();
        $corps = $corp::whereName('万浪软件')->first();
        $corpId = $corps->corpid;
        $secret = $corps->corpsecret;

        $name = $this->department->name;

        if ($this->department->departmentType->name == '学校') {
            $parent_id = 1;
        } else {
            $parent_id = $this->department->parent->id;
        }
        $order = $this->department->order;
        $departmentId = $this->department->id;
        $path = substr(dirname(__FILE__),0, stripos(dirname(__FILE__), 'app/Jobs'));
        $tokenFile = $path . 'public/token.txt';

        $token = Wechat::getAccessToken($tokenFile,$corpId, $secret);
        switch ($this->action){
            case 'create':
                Wechat::createDept($token, $name, $parent_id, $order, $departmentId);
                break;

            case 'update':
                 Wechat::updateDept($token, $departmentId, $name, $parent_id, $order);
                break;
            default:
                Wechat::delDept($token, $departmentId);
                break;

        }

    }

    public function failed(Exception $e) {



    }

}
