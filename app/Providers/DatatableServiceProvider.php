<?php
namespace App\Providers;

// use App\Facades\DatatableFacade as Datatable;
use Illuminate\Support\Facades\App;
use Illuminate\Support\ServiceProvider;

class DatatableServiceProvider extends ServiceProvider {
    
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
        
        // $this->app->singleton('DatatableFacade', function () {
        //     return new Datatable;
        // });
        App::bind('datatable', function () {
            return new \App\Helpers\Datatable;
        });
        
    }
}
