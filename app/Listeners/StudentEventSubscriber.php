<?php
namespace App\Listeners;

use App\Jobs\ManageImportStudent;
use App\Jobs\ManageUpdateStudent;
use Illuminate\Events\Dispatcher;
use Illuminate\Support\Facades\Log;

class StudentEventSubscriber {
    
    public function onStudentImported($event) {
        ManageImportStudent::dispatch($event->data)->onQueue('import');
    
    }
    
    public function onStudentUpdated($event) {
        ManageUpdateStudent::dispatch($event->data)->onQueue('import');
    }

    /**
     * Register the listeners for the subscriber
     *
     * @param Dispatcher $events
     */
    public function subscribe(Dispatcher $events) {
        
        $e = 'App\\Events\\';
        $l = 'App\\Listeners\\StudentEventSubscriber@';
        $events->listen($e . 'StudentImported', $l . 'onStudentImported');
        $events->listen($e . 'StudentUpdated', $l . 'onStudentUpdated');
        
    }
    
}