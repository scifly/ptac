<?php
namespace App\Jobs;

use App\Facades\Wechat;
use App\Models\App;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ManageWechatApp implements ShouldQueue {

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
        
        $app = [
            'agentid' => $this->app->agentid,
            'report_location_flag' => $this->app->report_location_flag,
            'name' => $this->app->name,
            'description' => $this->app->description,
            'redirect_domain' => $this->app->redirect_domain,
            'isreportenter' => $this->app->isreportenter,
            'home_url' => $this->app->home_url,
        ];
        $accessToken = Wechat::getAccessToken($this->app->corp_id, $this->app->secret, $this->app->agentid);
        return Wechat::configApp($accessToken, $app);
        
    }
    
}
