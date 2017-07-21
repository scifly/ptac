<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class ComposerServiceProvider extends ServiceProvider
{

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {

        View::composer('school.create_edit', 'App\Http\ViewComposers\SchoolComposer');
        View::composer('company.create_edit', 'App\Http\ViewComposers\CompanyComposer');
        View::composer('grade.create_edit', 'App\Http\ViewComposers\GradeComposer');
        View::composer('class.create_edit', 'App\Http\ViewComposers\SquadComposer');
        View::composer('subject.create_edit', 'App\Http\ViewComposers\SubjectComposer');


    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {

        //

    }

}
