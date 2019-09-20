<?php
namespace App\Providers;

use App\Models\{Action,
    App,
    Bed,
    Card,
    Company,
    Conference,
    Corp,
    Custodian,
    Department,
    Educator,
    Evaluate,
    Exam,
    Face,
    Grade,
    Group,
    Icon,
    Menu,
    MenuType,
    MessageType,
    Order,
    Participant,
    PollTopic,
    Room,
    RoomType,
    School,
    SchoolType,
    Score,
    ScoreRange,
    ScoreTotal,
    Squad,
    Student,
    Subject,
    SubjectModule,
    Tab,
    User,
    WapSite,
    WapSiteModule,
    WsmArticle};
use App\Policies\{ActionPolicy,
    AppPolicy,
    BedPolicy,
    CardPolicy,
    CommonPolicy,
    CompanyPolicy,
    ConferencePolicy,
    ConsumptionPolicy,
    ConsumptionStat,
    CorpPolicy,
    CustodianPolicy,
    DepartmentPolicy,
    EducatorPolicy,
    EvaluatePolicy,
    ExamPolicy,
    FacePolicy,
    GradePolicy,
    GroupPolicy,
    IconPolicy,
    MenuPolicy,
    MenuTypePolicy,
    MessageTypePolicy,
    MethodPolicy,
    OperatorPolicy,
    OrderPolicy,
    ParticipantPolicy,
    PollTopicPolicy,
    RoomPolicy,
    RoomTypePolicy,
    Route,
    SchoolPolicy,
    SchoolTypePolicy,
    ScorePolicy,
    ScoreRangePolicy,
    ScoreTotalPolicy,
    SquadPolicy,
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
        Model::class           => CommonPolicy::class,
        Route::class           => MethodPolicy::class,
        Action::class          => ActionPolicy::class,
        App::class             => AppPolicy::class,
        Bed::class             => BedPolicy::class,
        Card::class            => CardPolicy::class,
        Company::class         => CompanyPolicy::class,
        Conference::class      => ConferencePolicy::class,
        ConsumptionStat::class => ConsumptionPolicy::class,
        Corp::class            => CorpPolicy::class,
        Custodian::class       => CustodianPolicy::class,
        Department::class      => DepartmentPolicy::class,
        Educator::class        => EducatorPolicy::class,
        Evaluate::class        => EvaluatePolicy::class,
        Exam::class            => ExamPolicy::class,
        Face::class            => FacePolicy::class,
        Grade::class           => GradePolicy::class,
        Group::class           => GroupPolicy::class,
        Icon::class            => IconPolicy::class,
        Menu::class            => MenuPolicy::class,
        MenuType::class        => MenuTypePolicy::class,
        MessageType::class     => MessageTypePolicy::class,
        Order::class           => OrderPolicy::class,
        Participant::class     => ParticipantPolicy::class,
        PollTopic::class       => PollTopicPolicy::class,
        Room::class            => RoomPolicy::class,
        RoomType::class        => RoomTypePolicy::class,
        School::class          => SchoolPolicy::class,
        SchoolType::class      => SchoolTypePolicy::class,
        Score::class           => ScorePolicy::class,
        ScoreRange::class      => ScoreRangePolicy::class,
        ScoreTotal::class      => ScoreTotalPolicy::class,
        Squad::class           => SquadPolicy::class,
        Student::class         => StudentPolicy::class,
        SubjectModule::class   => SubjectModulePolicy::class,
        Subject::class         => SubjectPolicy::class,
        Tab::class             => TabPolicy::class,
        User::class            => OperatorPolicy::class,
        WapSite::class         => WapSitePolicy::class,
        WapSiteModule::class   => WapSiteModulePolicy::class,
        WsmArticle::class      => WsmArticlePolicy::class,
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
