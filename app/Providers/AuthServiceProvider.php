<?php
namespace App\Providers;

use App\Policies\ActionPolicy;
use App\Policies\CreatePolicy;
use App\Policies\SchoolPolicy;
use App\Policies\Route;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider {
    
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        Route::class => ActionPolicy::class,
        Model::class => SchoolPolicy::class,
    ];
    
    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot() {
        $this->registerPolicies();
        //
    }
}
