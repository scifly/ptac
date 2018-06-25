<?php
namespace App\Providers;

use Illuminate\Support\Facades\App;
use Illuminate\Support\ServiceProvider;

class GeneralClassServiceProvider extends ServiceProvider {
    
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot() {
        //
    }
    
    /**
     * Register services.
     *
     * @return void
     */
    public function register() {
        
        App::bind('generalclass', function () {
            return new \App\Helpers\GeneralClass;
        });
        
    }
    
}
