<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider {
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [];
    
    protected $subscribe = [
        'App\Listeners\DepartmentEventSubscriber',
        'App\Listeners\CompanyEventSubscriber',
        'App\Listeners\CorpEventSubscriber',
        'App\Listeners\SchoolEventSubscriber',
        'App\Listeners\GradeEventSubscriber',
        'App\Listeners\ClassEventSubscriber',
        'App\Listeners\MenuEventSubScriber',
        'App\Listeners\UserEventSubScriber',
    ];
    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot() {
        parent::boot();
        
        //
    }
}
