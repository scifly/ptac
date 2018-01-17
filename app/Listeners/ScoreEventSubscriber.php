<?php
namespace App\Listeners;

use App\Jobs\ManageImportScore;
use Illuminate\Events\Dispatcher;

class ScoreEventSubscriber {
    
    public function onScoreImported($event) {
        ManageImportScore::dispatch($event->data)->onQueue('import');
    
    }
    public function onScoreUpdated($event) {
        ManageUpdateScore::dispatch($event->data)->onQueue('import');
    }
    /**
     * Register the listeners for the subscriber
     *
     * @param Dispatcher $events
     */
    public function subscribe(Dispatcher $events) {
        
        $e = 'App\\Events\\';
        $l = 'App\\Listeners\\ScoreEventSubscriber@';
        $events->listen($e . 'ScoreImported', $l . 'onScoreImported');
        $events->listen($e . 'ScoreUpdated', $l . 'onScoreUpdated');
    }
    
}