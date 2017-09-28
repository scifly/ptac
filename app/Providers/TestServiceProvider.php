<?php
namespace App\Providers;

use App\Services\Test;
use Illuminate\Support\ServiceProvider;

class TestServiceProvider extends ServiceProvider {

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
        $this->app->bind(Test::class, function ($app) {
            return new Test('hithere');
        });
    }
}
