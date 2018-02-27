<?php
namespace App\Providers;

use App\Models\App;
use App\Models\ConferenceQueue;
use App\Models\Corp;
use App\Policies\ActionPolicy;
use App\Policies\AppPolicy;
use App\Policies\ConferenceQueuePolicy;
use App\Policies\CorpPolicy;
use App\Policies\CommonPolicy;
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
        Model::class => CommonPolicy::class,
        Route::class => ActionPolicy::class,
        Corp::class => CorpPolicy::class,
        App::class => AppPolicy::class,
        ConferenceQueue::class => ConferenceQueuePolicy::class
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
