<?php
namespace App\Jobs;

use App\Events\JobResponse;
use App\Facades\Wechat;
use App\Helpers\Constant;
use App\Helpers\HttpStatusCode;
use App\Models\App;
use App\Models\Corp;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SyncApp implements ShouldQueue {

    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    
    protected $app, $userId;
    
    /**
     * Create a new job instance.
     *
     * @param App $app
     * @param $userId
     */
    public function __construct(App $app, $userId) {
        
        $this->app = $app;
        $this->userId = $userId;
        
    }
    
    /**
     * Execute the job
     *
     * @return mixed
     * @throws Exception
     */
    public function handle() {
        
        $response = [
            'userId' => $this->userId,
            'title' => __('messages.app.title'),
            'message' => __('messages.app.app_configured'),
            'statusCode' => HttpStatusCode::OK,
        ];
        $app = [
            'agentid' => $this->app->agentid,
            'report_location_flag' => $this->app->report_location_flag,
            'name' => $this->app->name,
            'description' => $this->app->description,
            'redirect_domain' => $this->app->redirect_domain,
            'isreportenter' => $this->app->isreportenter,
            'home_url' => $this->app->home_url,
        ];
        $token = Wechat::getAccessToken(
            Corp::find($this->app->corp_id)->corpid,
            $this->app->secret
        );
        if ($token['errcode']) {
            $response['message'] = $token['errmsg'];
            $response['statusCode'] = HttpStatusCode::INTERNAL_SERVER_ERROR;
            event(new JobResponse($response));
            return false;
        }
        
        $result = json_decode(
            Wechat::configApp($token['access_token'], $app)
        );
        if ($result->{'errcode'}) {
            $response['statusCode'] = HttpStatusCode::INTERNAL_SERVER_ERROR;
            $response['message'] = Constant::WXERR[$result->{'errcode'}];
        }
        event(new JobResponse($response));
        
        return true;
        
    }
    
}
