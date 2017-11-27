<?php
namespace App\Listeners;

use App\Jobs\ManageImportEducator;
use Illuminate\Events\Dispatcher;

class EducatorEventSubscriber {
    
    public function onEducatorImported($event) {
        ManageImportEducator::dispatch($event->data)->onQueue('import');
    
    }

    /**
     * Register the listeners for the subscriber
     *
     * @param Dispatcher $events
     */
    public function subscribe(Dispatcher $events) {
        
        $e = 'App\\Events\\';
        $l = 'App\\Listeners\\EducatorEventSubscriber@';
        $events->listen($e . 'EducatorImported', $l . 'onEducatorImported');
        
    }
    
}