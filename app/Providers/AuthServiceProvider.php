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
use App\Models\MessageType;
use App\Models\PollQuestionnaire;
use App\Models\PollQuestionnaireSubject;
use App\Models\PollQuestionnaireSubjectChoice;
use App\Models\ProcedureType;
use App\Models\School;
use App\Models\SchoolType;
use App\Models\Squad;
use App\Models\Student;
use App\Models\StudentAttendanceSetting;
use App\Models\SubjectModule;
use App\Models\Tab;
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
use App\Policies\MessageTypePolicy;
use App\Policies\MethodPolicy;
use App\Policies\AppPolicy;
use App\Policies\ConferenceParticipantPolicy;
use App\Policies\ConferenceQueuePolicy;
use App\Policies\CorpPolicy;
use App\Policies\CommonPolicy;
use App\Policies\CustodianPolicy;
use App\Policies\PollQuestionnairePolicy;
use App\Policies\PollQuestionnaireSubjectChoicePolicy;
use App\Policies\PollQuestionnaireSubjectPolicy;
use App\Policies\ProcedureTypePolicy;
use App\Policies\Route;
use App\Policies\SchoolPolicy;
use App\Policies\SchoolTypePolicy;
use App\Policies\SquadPolicy;
use App\Policies\StudentAttendanceSettingPolicy;
use App\Policies\StudentPolicy;
use App\Policies\SubjectModulePolicy;
use App\Policies\TabPolicy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Laravel\Passport\Passport;

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
        MessageType::class => MessageTypePolicy::class,
        PollQuestionnaire::class => PollQuestionnairePolicy::class,
        PollQuestionnaireSubject::class => PollQuestionnaireSubjectPolicy::class,
        PollQuestionnaireSubjectChoice::class => PollQuestionnaireSubjectChoicePolicy::class,
        ProcedureType::class => ProcedureTypePolicy::class,
        School::class => SchoolPolicy::class,
        SchoolType::class => SchoolTypePolicy::class,
        Squad::class => SquadPolicy::class,
        StudentAttendanceSetting::class => StudentAttendanceSettingPolicy::class,
        Student::class => StudentPolicy::class,
        SubjectModule::class => SubjectModulePolicy::class,
        Tab::class => TabPolicy::class,
    ];
    
    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot() {
        
        $this->registerPolicies();
        Passport::routes();
        
    }
}
