<?php
namespace App\Providers;

use App\Models\{Action,
    App,
    Company,
    ConferenceParticipant,
    ConferenceQueue,
    Corp,
    Custodian,
    Department,
    Educator,
    EducatorAttendance,
    Exam,
    Grade,
    Group,
    Icon,
    Menu,
    MenuType,
    MessageType,
    PollQuestionnaire,
    PollQuestionnaireSubject,
    PollQuestionnaireSubjectChoice,
    ProcedureStep,
    ProcedureType,
    School,
    SchoolType,
    Score,
    ScoreRange,
    ScoreTotal,
    Squad,
    Student,
    StudentAttendance,
    StudentAttendanceSetting,
    Subject,
    SubjectModule,
    Tab,
    User,
    WapSite,
    WapSiteModule,
    WsmArticle};
use App\Policies\{ActionPolicy,
    AppPolicy,
    CommonPolicy,
    CompanyPolicy,
    ConferenceParticipantPolicy,
    ConferenceQueuePolicy,
    ConsumptionPolicy,
    ConsumptionStat,
    CorpPolicy,
    CustodianPolicy,
    DepartmentPolicy,
    EducatorAttendancePolicy,
    EducatorPolicy,
    ExamPolicy,
    GradePolicy,
    GroupPolicy,
    IconPolicy,
    MenuPolicy,
    MenuTypePolicy,
    MessageTypePolicy,
    MethodPolicy,
    OperatorPolicy,
    PollQuestionnairePolicy,
    PollQuestionnaireSubjectChoicePolicy,
    PollQuestionnaireSubjectPolicy,
    ProcedureStepPolicy,
    ProcedureTypePolicy,
    Route,
    SchoolPolicy,
    SchoolTypePolicy,
    ScorePolicy,
    ScoreRangePolicy,
    ScoreTotalPolicy,
    SquadPolicy,
    StudentAttendancePolicy,
    StudentAttendanceSettingPolicy,
    StudentPolicy,
    SubjectModulePolicy,
    SubjectPolicy,
    TabPolicy,
    WapSiteModulePolicy,
    WapSitePolicy,
    WsmArticlePolicy};
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
        Action::class                         => ActionPolicy::class,
        App::class                            => AppPolicy::class,
        Company::class                        => CompanyPolicy::class,
        ConferenceParticipant::class          => ConferenceParticipantPolicy::class,
        ConferenceQueue::class                => ConferenceQueuePolicy::class,
        ConsumptionStat::class                => ConsumptionPolicy::class,
        Corp::class                           => CorpPolicy::class,
        Custodian::class                      => CustodianPolicy::class,
        Department::class                     => DepartmentPolicy::class,
        Educator::class                       => EducatorPolicy::class,
        Exam::class                           => ExamPolicy::class,
        Grade::class                          => GradePolicy::class,
        Group::class                          => GroupPolicy::class,
        Icon::class                           => IconPolicy::class,
        Menu::class                           => MenuPolicy::class,
        MenuType::class                       => MenuTypePolicy::class,
        MessageType::class                    => MessageTypePolicy::class,
        PollQuestionnaire::class              => PollQuestionnairePolicy::class,
        PollQuestionnaireSubject::class       => PollQuestionnaireSubjectPolicy::class,
        PollQuestionnaireSubjectChoice::class => PollQuestionnaireSubjectChoicePolicy::class,
        ProcedureType::class                  => ProcedureTypePolicy::class,
        ProcedureStep::class                  => ProcedureStepPolicy::class,
        School::class                         => SchoolPolicy::class,
        SchoolType::class                     => SchoolTypePolicy::class,
        Score::class                          => ScorePolicy::class,
        ScoreRange::class                     => ScoreRangePolicy::class,
        ScoreTotal::class                     => ScoreTotalPolicy::class,
        Squad::class                          => SquadPolicy::class,
        StudentAttendanceSetting::class       => StudentAttendanceSettingPolicy::class,
        Student::class                        => StudentPolicy::class,
        SubjectModule::class                  => SubjectModulePolicy::class,
        Subject::class                        => SubjectPolicy::class,
        Tab::class                            => TabPolicy::class,
        StudentAttendance::class              => StudentAttendancePolicy::class,
        Student::class                        => StudentPolicy::class,
        EducatorAttendance::class             => EducatorAttendancePolicy::class,
        User::class                           => OperatorPolicy::class,
        WapSite::class                        => WapSitePolicy::class,
        WapSiteModule::class                  => WapSiteModulePolicy::class,
        WsmArticle::class                     => WsmArticlePolicy::class,
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
