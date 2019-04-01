<?php
namespace App\Providers;

use App\Helpers\Wechat;
use Illuminate\Support\Facades\App;
use Illuminate\Support\ServiceProvider;

/**
 * Class WechatServiceProvider
 * @package App\Providers
 */
class WechatServiceProvider extends ServiceProvider {
    
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot() { }
    
    /**
     * Register the application services.
     *
     * @return void
     */
    public function register() {

        App::bind('wechat', function () {
            return new Wechat;
        });
        
    }
}
