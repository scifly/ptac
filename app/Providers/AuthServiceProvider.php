<?php
namespace App\Providers;

use App\Models\App;
use App\Models\Corp;
use App\Policies\ActionPolicy;
use App\Policies\AppPolicy;
use App\Policies\CorpPolicy;
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
        Model::class => SchoolPolicy::class,
        Route::class => ActionPolicy::class,
        Corp::class => CorpPolicy::class,
        App::class => AppPolicy::class,
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
