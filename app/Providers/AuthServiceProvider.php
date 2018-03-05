<?php
namespace App\Providers;

use App\Models\Action;
use App\Models\AlertType;
use App\Models\App;
use App\Models\CommType;
use App\Models\Company;
use App\Models\ConferenceParticipant;
use App\Models\ConferenceQueue;
use App\Models\Corp;
use App\Models\Custodian;
use App\Models\Department;
use App\Models\DepartmentType;
use App\Models\Exam;
use App\Models\Grade;
use App\Models\Group;
use App\Models\Icon;
use App\Models\IconType;
use App\Models\Menu;
use App\Policies\ActionPolicy;
use App\Policies\AlertTypePolicy;
use App\Policies\CommTypePolicy;
use App\Policies\CompanyPolicy;
use App\Policies\DepartmentPolicy;
use App\Policies\DepartmentTypePolicy;
use App\Policies\ExamPolicy;
use App\Policies\GradePolicy;
use App\Policies\GroupPolicy;
use App\Policies\IconPolicy;
use App\Policies\IconTypePolicy;
use App\Policies\MenuPolicy;
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
        Route::class => MethodPolicy::class,
        
        Action::class => ActionPolicy::class,
        AlertType::class => AlertTypePolicy::class,
        App::class => AppPolicy::class,
        CommType::class => CommTypePolicy::class,
        Company::class => CompanyPolicy::class,
        ConferenceParticipant::class => ConferenceParticipantPolicy::class,
        ConferenceQueue::class => ConferenceQueuePolicy::class,
        Corp::class => CorpPolicy::class,
        Custodian::class => CustodianPolicy::class,
        Department::class => DepartmentPolicy::class,
        DepartmentType::class => DepartmentTypePolicy::class,
        Exam::class => ExamPolicy::class,
        Grade::class => GradePolicy::class,
        Group::class => GroupPolicy::class,
        Icon::class => IconPolicy::class,
        IconType::class => IconTypePolicy::class,
        Menu::class => MenuPolicy::class,
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
