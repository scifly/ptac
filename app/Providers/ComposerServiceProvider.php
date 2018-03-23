<?php
namespace App\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class ComposerServiceProvider extends ServiceProvider {
    
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot() {

        View::composer('app.index', 'App\Http\ViewComposers\AppIndexComposer');
        View::composer('app.edit', 'App\Http\ViewComposers\AppComposer');

        View::composer(
            ['alert_type.index', 'alert_type.create_edit'],
            'App\Http\ViewComposers\AlertTypeComposer'
        );

        View::composer(
            ['company.index', 'company.create_edit'],
            'App\Http\ViewComposers\CompanyComposer'
        );

        View::composer(
            ['comm_type.index', 'comm_type.create_edit'],
            'App\Http\ViewComposers\CommTypeComposer'
        );

        View::composer('educator.index', 'App\Http\ViewComposers\EducatorIndexComposer');
        View::composer('educator.create_edit', 'App\Http\ViewComposers\EducatorComposer');
        View::composer('educator.recharge', 'App\Http\ViewComposers\EducatorComposer');
        
        View::composer(
            ['educator_attendance_setting.create_edit', 'educator_attendance_setting.index'],
            'App\Http\ViewComposers\EducatorAttendanceSettingComposer'
        );
        View::composer('educator_attendance.index', 'App\Http\ViewComposers\EducatorAttendanceIndexComposer');
        View::composer('educator_attendance.stat', 'App\Http\ViewComposers\EducatorAttendanceStatComposer');
        
        View::composer('consumption.index', 'App\Http\ViewComposers\ConsumptionIndexComposer');
        View::composer('consumption.show', 'App\Http\ViewComposers\ConsumptionStatComposer');

        View::composer('student_attendance_setting.create_edit', 'App\Http\ViewComposers\StudentAttendanceSettingComposer');
        View::composer('student_attendance_setting.index', 'App\Http\ViewComposers\StudentAttendanceSettingIndexComposer');
        View::composer('student_attendance.index', 'App\Http\ViewComposers\StudentAttendanceIndexComposer');
        View::composer('student_attendance.stat', 'App\Http\ViewComposers\StudentAttendanceStatComposer');
    
        View::composer(
            ['student.index','student.show'],
            'App\Http\ViewComposers\StudentIndexComposer');
        View::composer('student.create_edit', 'App\Http\ViewComposers\StudentComposer');
    
        View::composer('custodian.index', 'App\Http\ViewComposers\CustodianIndexComposer');
        View::composer('custodian.create_edit', 'App\Http\ViewComposers\CustodianComposer');
        View::composer('custodian.relationship', 'App\Http\ViewComposers\CustodianRelationshipComposer');
    
        View::composer('subject.create_edit', 'App\Http\ViewComposers\SubjectComposer');
        View::composer('subject.index', 'App\Http\ViewComposers\SubjectIndexComposer');
    
        View::composer('subject_module.create_edit', 'App\Http\ViewComposers\SubjectModuleComposer');
        View::composer('subject_module.index', 'App\Http\ViewComposers\SubjectModuleIndexComposer');
    
        View::composer('group.create_edit', 'App\Http\ViewComposers\GroupComposer');
        View::composer('group.create', 'App\Http\ViewComposers\GroupCreateComposer');
        View::composer('group.edit', 'App\Http\ViewComposers\GroupEditComposer');
        View::composer('group.index', 'App\Http\ViewComposers\GroupIndexComposer');
    
        View::composer('procedure.create_edit', 'App\Http\ViewComposers\ProcedureComposer');
        View::composer('procedure.index', 'App\Http\ViewComposers\ProcedureIndexComposer');
    
        View::composer('procedure_step.create_edit', 'App\Http\ViewComposers\ProcedureStepComposer');
        View::composer('procedure_step.index', 'App\Http\ViewComposers\ProcedureStepIndexComposer');
    
        View::composer(
            ['poll_questionnaire.create_edit', 'poll_questionnaire.index'],
            'App\Http\ViewComposers\PollQuestionnaireComposer'
        );
    
        View::composer('pq_subject.create_edit', 'App\Http\ViewComposers\PqSubjectComposer');
        View::composer('pq_subject.index', 'App\Http\ViewComposers\PqSubjectIndexComposer');
    
        View::composer('pq_choice.create_edit', 'App\Http\ViewComposers\PqChoiceComposer');
        View::composer('pq_choice.index', 'App\Http\ViewComposers\PqChoiceIndexComposer');
    
        View::composer('score_range.create_edit', 'App\Http\ViewComposers\ScoreRangeComposer');
        View::composer('score_range.index', 'App\Http\ViewComposers\ScoreRangeIndexComposer');
        View::composer('score_range.show_statistics', 'App\Http\ViewComposers\ScoreRangeShowStatisticsComposer');
    
        View::composer('score.create_edit', 'App\Http\ViewComposers\ScoreComposer');
        View::composer('score.index', 'App\Http\ViewComposers\ScoreIndexComposer');
        View::composer('score.analysis', 'App\Http\ViewComposers\ScoreAnalysisComposer');
    
        View::composer('event.index', 'App\Http\ViewComposers\EventIndexComposer');
        View::composer('event.show', 'App\Http\ViewComposers\EventComposer');
    
        View::composer('exam.create_edit', 'App\Http\ViewComposers\ExamComposer');
        View::composer('exam.index', 'App\Http\ViewComposers\ExamIndexComposer');
    
        View::composer(
            ['exam_type.create_edit', 'exam_type.index'],
            'App\Http\ViewComposers\ExamTypeComposer'
        );
    
        View::composer(
            ['conference_room.create_edit', 'conference_room.index'],
            'App\Http\ViewComposers\ConferenceRoomComposer'
        );
    
        View::composer('conference_queue.create_edit', 'App\Http\ViewComposers\ConferenceQueueComposer');
        View::composer('conference_queue.edit', 'App\Http\ViewComposers\ConferenceQueueEditComposer');
        View::composer('conference_queue.index', 'App\Http\ViewComposers\ConferenceQueueIndexComposer');
    
        View::composer('conference_participant.index', 'App\Http\ViewComposers\ConferenceParticipantIndexComposer');
    
        View::composer('message.create_edit', 'App\Http\ViewComposers\MessageComposer');
        View::composer('message.index', 'App\Http\ViewComposers\MessageIndexComposer');
    
        View::composer('message_type.index', 'App\Http\ViewComposers\MessageTypeIndexComposer');
        View::composer('message_type.create_edit', 'App\Http\ViewComposers\MessageTypeIndexComposer');
    
        View::composer(
            ['combo_type.create_edit', 'combo_type.index'],
            'App\Http\ViewComposers\ComboTypeComposer'
        );
    
        View::composer('wap_site.create_edit', 'App\Http\ViewComposers\WapSiteComposer');
        View::composer('wap_site.index', 'App\Http\ViewComposers\WapSiteIndexComposer');
    
        View::composer('menu.create_edit', 'App\Http\ViewComposers\MenuComposer');
        View::composer('menu.index', 'App\Http\ViewComposers\MenuIndexComposer');
        View::composer('menu.menu_tabs', 'App\Http\ViewComposers\MenuTabComposer');
    
        View::composer('icon.create_edit', 'App\Http\ViewComposers\IconComposer');
        View::composer('icon.index', 'App\Http\ViewComposers\IconIndexComposer');
    
        View::composer('tab.index', 'App\Http\ViewComposers\TabIndexComposer');
        View::composer('tab.create_edit', 'App\Http\ViewComposers\TabComposer');
    
        View::composer('wap_site_module.create_edit', 'App\Http\ViewComposers\WapSiteModuleComposer');
        View::composer('wap_site_module.index', 'App\Http\ViewComposers\WapSiteModuleIndexComposer');
    
        View::composer('wsm_article.create_edit', 'App\Http\ViewComposers\WsmArticleComposer');
        View::composer('wsm_article.index', 'App\Http\ViewComposers\WsmArticleIndexComposer');
    
        View::composer('action.create_edit', 'App\Http\ViewComposers\ActionComposer');
    
    
        View::composer('school.create_edit', 'App\Http\ViewComposers\SchoolComposer');
        View::composer('school.index', 'App\Http\ViewComposers\SchoolIndexComposer');
        View::composer('school.show', 'App\Http\ViewComposers\SchoolShowComposer');

        View::composer(['school_type.index','school_type.create_edit'],
            'App\Http\ViewComposers\SchoolTypeIndexComposer');

        View::composer('corp.index', 'App\Http\ViewComposers\CorpIndexComposer');
        View::composer('corp.create_edit', 'App\Http\ViewComposers\CorpComposer');
        
        View::composer('grade.create_edit', 'App\Http\ViewComposers\GradeComposer');
        View::composer('grade.index', 'App\Http\ViewComposers\GradeIndexComposer');

        View::composer('class.create_edit', 'App\Http\ViewComposers\SquadComposer');
        View::composer('class.index', 'App\Http\ViewComposers\SquadIndexComposer');

        View::composer('major.create_edit', 'App\Http\ViewComposers\MajorComposer');
        View::composer('major.index', 'App\Http\ViewComposers\MajorIndexComposer');

        View::composer('team.create_edit', 'App\Http\ViewComposers\TeamComposer');
        View::composer('team.index', 'App\Http\ViewComposers\TeamIndexComposer');

        View::composer('department.create_edit', 'App\Http\ViewComposers\DepartmentComposer');
        View::composer('department.index', 'App\Http\ViewComposers\DepartmentIndexComposer');
        
        View::composer(['department_type.index', 'department_type.create_edit'],
            'App\Http\ViewComposers\DepartmentTypeIndexComposer');

        View::composer(
            ['attendance_machine.create_edit', 'attendance_machine.index'],
            'App\Http\ViewComposers\AttendanceMachineComposer'
        );

        View::composer(
            ['semester.create_edit', 'semester.index'],
            'App\Http\ViewComposers\SemesterComposer'
        );

        View::composer('operator.index', 'App\Http\ViewComposers\UserIndexComposer');
        View::composer('operator.create_edit', 'App\Http\ViewComposers\UserComposer');
        
        View::composer('wechat.message_center.index', 'App\Http\ViewComposers\MessageCenterComposer');
    }
    
    /**
     * Register the application services.
     *
     * @return void
     */
    public function register() {
        
        //
    }
    
}
