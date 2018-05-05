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
            'App\Listeners\EventListener',
        ],
    ];

    protected $subscribe = [
        'App\Listeners\StudentEventSubscriber',
        'App\Listeners\EducatorEventSubscriber',
        'App\Listeners\MessageEventSubscriber',
        'App\Listeners\AttendanceEventSubscriber',
        'App\Listeners\WapSiteEventSubscriber',
        'App\Listeners\ScoreEventSubscriber',
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
