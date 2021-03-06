<?php
namespace App\Providers;

use App\Helpers\Datatable;
use Illuminate\Support\Facades\App;
use Illuminate\Support\ServiceProvider;

/**
 * Class DatatableServiceProvider
 * @package App\Providers
 */
class DatatableServiceProvider extends ServiceProvider {
    
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
        
        App::bind('datatable', function () {
            return new Datatable;
        });
        
    }
}
