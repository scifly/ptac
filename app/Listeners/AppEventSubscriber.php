<?php
namespace App\Listeners;

use App\Jobs\ManageWechatApp;
use App\Jobs\ManageWechatAppMenu;
use App\Models\App;
use Illuminate\Events\Dispatcher;

class AppEventSubscriber {
    
    protected $app;
    
    function __construct(App $app) {
        
        $this->app = $app;
        
    }
    
    public function onAppUpdated($event) {
        
        $app = $event->app;
        ManageWechatApp::dispatch($app);
    
    }
    
    public function onAppMenuUpdated($event) {
        
        $app = $event->app;
        ManageWechatAppMenu::dispatch($app);
    }
    
    /**
     * Register the listeners for the subscriber
     *
     * @param Dispatcher $events
     */
    public function subscribe(Dispatcher $events) {
        
        $e = 'App\\Events\\';
        $l = 'App\\Listeners\\AppEventSubscriber@';
        $events->listen($e . 'AppUpdated', $l . 'onAppUpdated');
        $events->listen($e . 'AppMenuUpdated', $l . 'onAppMenuUpdated');
        
    }
    
}