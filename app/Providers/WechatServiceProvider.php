<?php
namespace App\Providers;

use App\Facades\Wechat as Wechat;
use Illuminate\Support\ServiceProvider;

class WechatServiceProvider extends ServiceProvider {

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot() {
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register() {
        $this->app->singleton('Wechat', function () {
            return new Wechat;
        });
    }
}
