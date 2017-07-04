<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Facades\Wechat as Wechat;

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
        $this->app->singleton('Wechat', function() {
            return new Wechat;
        });
    }
}
