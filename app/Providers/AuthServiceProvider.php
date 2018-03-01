<?php
namespace App\Providers;

use App\Models\Action;
use App\Models\AlertType;
use App\Models\App;
use App\Models\CommType;
use App\Models\ConferenceParticipant;
use App\Models\ConferenceQueue;
use App\Models\Corp;
use App\Models\Custodian;
use App\Policies\ActionPolicy;
use App\Policies\AlertTypePolicy;
use App\Policies\MethodPolicy;
use App\Policies\AppPolicy;
use App\Policies\ConferenceParticipantPolicy;
use App\Policies\ConferenceQueuePolicy;
use App\Policies\CorpPolicy;
use App\Policies\CommonPolicy;
use App\Policies\CustodianPolicy;
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
        Action::class => ActionPolicy::class,
        AlertType::class => AlertTypePolicy::class,
        CommType::class => CommonPolicy::class,
        Custodian::class => CustodianPolicy::class,
        Route::class => MethodPolicy::class,
        Corp::class => CorpPolicy::class,
        App::class => AppPolicy::class,
        ConferenceParticipant::class => ConferenceParticipantPolicy::class,
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
