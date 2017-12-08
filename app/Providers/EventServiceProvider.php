<?php
namespace App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider {
    
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        'App\Events\Event' => [
            'App\Listeners\EventListener'
        ]
    ];
    
    protected $subscribe = [
        'App\Listeners\DepartmentEventSubscriber',
        'App\Listeners\CompanyEventSubscriber',
        'App\Listeners\CorpEventSubscriber',
        'App\Listeners\SchoolEventSubscriber',
        'App\Listeners\GradeEventSubscriber',
        'App\Listeners\ClassEventSubscriber',
        'App\Listeners\MenuEventSubscriber',
        'App\Listeners\UserEventSubscriber',
        'App\Listeners\AppEventSubscriber',
        'App\Listeners\StudentEventSubscriber',
        'App\Listeners\EducatorEventSubscriber',
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
