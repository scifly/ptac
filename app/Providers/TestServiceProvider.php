<?php
namespace App\Providers;

use App\Models\MenuType;
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
        $this->app->bind(MenuType::class, function ($app) {
            return new MenuType();
        });
    }
}
