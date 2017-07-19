<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class ComposerServiceProvider extends ServiceProvider {
    
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot() {
        
        View::composer('school.create_edit', 'App\Http\ViewComposers\SchoolComposer');
        
    }
    
    /**
     * Register the application services.
     *
     * @return void
     */
    public function register() {
        
        //
        
    }
    
}
