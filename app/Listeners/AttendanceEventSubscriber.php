<?php
namespace App\Listeners;

use App\Jobs\ManageStudentAttendance;
use Illuminate\Events\Dispatcher;

/**
 * Class AttendanceEventSubscriber
 * @package App\Listeners
 */
class AttendanceEventSubscriber {
    
    /**
     * @param $event
     */
    public function onStudentAttendanceCreate($event) {
    
        ManageStudentAttendance::dispatch($event->data)->onQueue('import');
    
    }

    /**
     * Register the listeners for the subscriber
     *
     * @param Dispatcher $events
     */
    public function subscribe(Dispatcher $events) {
        
        $e = 'App\\Events\\';
        $l = 'App\\Listeners\\AttendanceEventSubscriber@';
        $events->listen($e . 'StudentAttendanceCreate', $l . 'onStudentAttendanceCreate');
    }
    
}