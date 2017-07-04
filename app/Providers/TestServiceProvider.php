<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\Test;

class TestServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(Test::class, function($app) {
            return new Test('hithere');
        });
    }
}
