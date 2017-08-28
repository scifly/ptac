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
        View::composer('school.create_edit', 'App\Http\ViewComposers\SchoolComposer');
        View::composer('corp.create_edit', 'App\Http\ViewComposers\CorpComposer');
        View::composer('grade.create_edit', 'App\Http\ViewComposers\GradeComposer');
        View::composer('class.create_edit', 'App\Http\ViewComposers\SquadComposer');
        View::composer('educator.create_edit', 'App\Http\ViewComposers\EducatorComposer');
        View::composer('educator_class.create_edit', 'App\Http\ViewComposers\EducatorClassComposer');
        View::composer('custodian_student.create_edit', 'App\Http\ViewComposers\CustodianStudentComposer');
        View::composer('score.create_edit', 'App\Http\ViewComposers\ScoreComposer');
        View::composer('subject.create_edit', 'App\Http\ViewComposers\SubjectComposer');
        View::composer('subject_module.create_edit', 'App\Http\ViewComposers\SubjectModuleComposer');
        View::composer('educator.create_edit', 'App\Http\ViewComposers\EducatorComposer');
        View::composer('attendance_machine.create_edit', 'App\Http\ViewComposers\AttendanceMachineComposer');
        View::composer('semester.create_edit', 'App\Http\ViewComposers\SemesterComposer');
        View::composer('procedure.create_edit', 'App\Http\ViewComposers\ProcedureComposer');
        View::composer('procedure_step.create_edit', 'App\Http\ViewComposers\ProcedureStepComposer');
        View::composer('student.create_edit', 'App\Http\ViewComposers\StudentComposer');
        View::composer('score_range.create_edit', 'App\Http\ViewComposers\ScoreRangeComposer');
        View::composer('user.create_edit', 'App\Http\ViewComposers\UserComposer');
        View::composer('wap_site.create_edit', 'App\Http\ViewComposers\WapSiteComposer');
        View::composer('score_total.create_edit', 'App\Http\ViewComposers\ScoreComposer');
        View::composer('menu.create_edit', 'App\Http\ViewComposers\MenuComposer');
        View::composer('icon.create_edit', 'App\Http\ViewComposers\IconComposer');
        View::composer('tab.create_edit', 'App\Http\ViewComposers\TabComposer');
        View::composer('wap_site_module.create_edit', 'App\Http\ViewComposers\WapSiteModuleComposer');
        View::composer('wsm_article.create_edit', 'App\Http\ViewComposers\WsmArticleComposer');
        View::composer('action.create_edit', 'App\Http\ViewComposers\ActionComposer');
        View::composer('message.create_edit', 'App\Http\ViewComposers\MessageComposer');
        View::composer('event.index', 'App\Http\ViewComposers\EventComposer');
        View::composer('event.show', 'App\Http\ViewComposers\EventComposer');
        View::composer('exam.create_edit', 'App\Http\ViewComposers\ExamComposer');
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
