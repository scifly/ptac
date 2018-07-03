<?php
namespace App\Providers;

use App\Models\Action;
use App\Models\ActionType;
use App\Models\AlertType;
use App\Models\App;
use App\Models\AttachmentType;
use App\Models\CommType;
use App\Models\Company;
use App\Models\ConferenceParticipant;
use App\Models\ConferenceQueue;
use App\Models\Corp;
use App\Models\Custodian;
use App\Models\Department;
use App\Models\DepartmentType;
use App\Models\Educator;
use App\Models\EducatorAttendance;
use App\Models\Exam;
use App\Models\Grade;
use App\Models\Group;
use App\Models\Icon;
use App\Models\IconType;
use App\Models\MediaType;
use App\Models\Menu;
use App\Models\MenuType;
use App\Models\MessageType;
use App\Models\PollQuestionnaire;
use App\Models\PollQuestionnaireSubject;
use App\Models\PollQuestionnaireSubjectChoice;
use App\Models\ProcedureStep;
use App\Models\ProcedureType;
use App\Models\School;
use App\Models\SchoolType;
use App\Models\Score;
use App\Models\ScoreRange;
use App\Models\ScoreTotal;
use App\Models\Squad;
use App\Models\Student;
use App\Models\StudentAttendance;
use App\Models\StudentAttendanceSetting;
use App\Models\Subject;
use App\Models\SubjectModule;
use App\Models\Tab;
use App\Models\User;
use App\Models\WapSite;
use App\Models\WapSiteModule;
use App\Models\WsmArticle;
use App\Policies\ActionPolicy;
use App\Policies\ActionTypePolicy;
use App\Policies\AlertTypePolicy;
use App\Policies\AttachmentTypePolicy;
use App\Policies\CommTypePolicy;
use App\Policies\CompanyPolicy;
use App\Policies\ConsumptionPolicy;
use App\Policies\ConsumptionStat;
use App\Policies\DepartmentPolicy;
use App\Policies\DepartmentTypePolicy;
use App\Policies\EducatorAttendancePolicy;
use App\Policies\EducatorPolicy;
use App\Policies\ExamPolicy;
use App\Policies\GradePolicy;
use App\Policies\GroupPolicy;
use App\Policies\IconPolicy;
use App\Policies\IconTypePolicy;
use App\Policies\MediaTypePolicy;
use App\Policies\MenuPolicy;
use App\Policies\MenuTypePolicy;
use App\Policies\MessageTypePolicy;
use App\Policies\MethodPolicy;
use App\Policies\AppPolicy;
use App\Policies\ConferenceParticipantPolicy;
use App\Policies\ConferenceQueuePolicy;
use App\Policies\CorpPolicy;
use App\Policies\CommonPolicy;
use App\Policies\CustodianPolicy;
use App\Policies\OperatorPolicy;
use App\Policies\PollQuestionnairePolicy;
use App\Policies\PollQuestionnaireSubjectChoicePolicy;
use App\Policies\PollQuestionnaireSubjectPolicy;
use App\Policies\ProcedureStepPolicy;
use App\Policies\ProcedureTypePolicy;
use App\Policies\Route;
use App\Policies\SchoolPolicy;
use App\Policies\SchoolTypePolicy;
use App\Policies\ScorePolicy;
use App\Policies\ScoreRangePolicy;
use App\Policies\ScoreTotalPolicy;
use App\Policies\SquadPolicy;
use App\Policies\StudentAttendancePolicy;
use App\Policies\StudentAttendanceSettingPolicy;
use App\Policies\StudentPolicy;
use App\Policies\SubjectModulePolicy;
use App\Policies\SubjectPolicy;
use App\Policies\TabPolicy;
use App\Policies\WapSiteModulePolicy;
use App\Policies\WapSitePolicy;
use App\Policies\WsmArticlePolicy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Laravel\Passport\Passport;

/**
 * Class AuthServiceProvider
 * @package App\Providers
 */
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
        ActionType::class => ActionTypePolicy::class,
        AlertType::class => AlertTypePolicy::class,
        AttachmentType::class => AttachmentTypePolicy::class,
        App::class => AppPolicy::class,
        CommType::class => CommTypePolicy::class,
        Company::class => CompanyPolicy::class,
        ConferenceParticipant::class => ConferenceParticipantPolicy::class,
        ConferenceQueue::class => ConferenceQueuePolicy::class,
        ConsumptionStat::class => ConsumptionPolicy::class,
        Corp::class => CorpPolicy::class,
        Custodian::class => CustodianPolicy::class,
        Department::class => DepartmentPolicy::class,
        DepartmentType::class => DepartmentTypePolicy::class,
        Educator::class => EducatorPolicy::class,
        Exam::class => ExamPolicy::class,
        Grade::class => GradePolicy::class,
        Group::class => GroupPolicy::class,
        Icon::class => IconPolicy::class,
        IconType::class => IconTypePolicy::class,
        Menu::class => MenuPolicy::class,
        MenuType::class => MenuTypePolicy::class,
        MediaType::class => MediaTypePolicy::class,
        MessageType::class => MessageTypePolicy::class,
        PollQuestionnaire::class => PollQuestionnairePolicy::class,
        PollQuestionnaireSubject::class => PollQuestionnaireSubjectPolicy::class,
        PollQuestionnaireSubjectChoice::class => PollQuestionnaireSubjectChoicePolicy::class,
        ProcedureType::class => ProcedureTypePolicy::class,
        ProcedureStep::class => ProcedureStepPolicy::class,
        School::class => SchoolPolicy::class,
        SchoolType::class => SchoolTypePolicy::class,
        Score::class => ScorePolicy::class,
        ScoreRange::class => ScoreRangePolicy::class,
        ScoreTotal::class => ScoreTotalPolicy::class,
        Squad::class => SquadPolicy::class,
        StudentAttendanceSetting::class => StudentAttendanceSettingPolicy::class,
        Student::class => StudentPolicy::class,
        SubjectModule::class => SubjectModulePolicy::class,
        Subject::class => SubjectPolicy::class,
        Tab::class => TabPolicy::class,
        StudentAttendance::class => StudentAttendancePolicy::class,
        Student::class => StudentPolicy::class,
        EducatorAttendance::class => EducatorAttendancePolicy::class,
        User::class => OperatorPolicy::class,
        WapSite::class => WapSitePolicy::class,
        WapSiteModule::class => WapSiteModulePolicy::class,
        WsmArticle::class => WsmArticlePolicy::class,
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
