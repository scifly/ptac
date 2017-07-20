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
        View::composer('company.create_edit', 'App\Http\ViewComposers\CompanyComposer');

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
