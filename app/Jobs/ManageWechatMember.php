<?php
namespace App\Jobs;

use App\Facades\Wechat;
use App\Models\App;
use App\Models\Corp;
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
        
        $corp = new Corp();
        $corp = $corp::whereName('万浪软件')->first();
        $corpId = $corp->corpid;
        // $app = App::whereCorpId($corp->id)->where('name', '企业通讯录')->first();
        $contactSync = App::whereAgentid('999')->first();
        $secret = $contactSync->secret;
        // $secret = 'IoiSOIsOGrdps03Lx_h5V3cCvMl3ibu-FyqqAsy-qLM';
        // $agentId = $app->agentid;
        $token = Wechat::getAccessToken($corpId, $secret);
        switch ($this->action) {
            case 'create':
                Wechat::createUser($token, $this->data);
                break;
            case 'update':
                Wechat::updateUser($token, $this->data);
                break;
            default:
                Wechat::delUser($token, $this->data);
                break;
        }
        
    }
    
}
