<?php
namespace App\Jobs;

use App\Facades\Wechat;
use App\Models\App;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ManageWechatAppMenu implements ShouldQueue {

    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    
    protected $app;
    
    /**
     * Create a new job instance.
     *
     * @param $app
     */
    public function __construct(App $app) { $this->app = $app; }
    
    /**
     * Execute the job
     *
     * @return mixed
     */
    public function handle() {
        
        $accessToken = Wechat::getAccessToken($this->app->corp_id, $this->app->secret, $this->app->agentid);
        return Wechat::createMenu($accessToken, $this->app->agentid, json_decode($this->app->menu));
        
    }
    
}
