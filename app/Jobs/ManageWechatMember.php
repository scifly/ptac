<?php
namespace App\Jobs;

use App\Facades\Wechat;
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
        Log::debug('create_user');
        $corp = new Corp();
        $corps = $corp::whereName('万浪软件')->first();
        $corpId = $corps->corpid;
        Log::debug('ID:'.$corps->corpid);
        // $secret = $corps->corpsecret;
        # 通讯录同步助手 secret
        $secret = 'IoiSOIsOGrdps03Lx_h5V3cCvMl3ibu-FyqqAsy-qLM';
        $agentid = '2';
        // $dir = dirname(__FILE__);
        // $path = substr($dir, 0, stripos($dir, 'app/Jobs'));
        // $tokenFile = $path . 'public/token.txt';
        $token = Wechat::getAccessToken($corpId, $secret, $agentid);
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
