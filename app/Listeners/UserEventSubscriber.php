<?php
namespace App\Listeners;

use App\Jobs\ManageWechatMember;
use Illuminate\Events\Dispatcher;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Support\Facades\Log;

class UserEventSubscriber {
    
    use DispatchesJobs;
    
    /**
     * 将"创建企业号会员"任务提交至work队列
     *
     * @param $event
     */
    public function onUserCreated($event) {
        // $job = new ManageWechatMember($event->data, 'create');
        // $this->dispatch($job)->onQueue('import');
        ManageWechatMember::dispatch($event->data, 'create')->onQueue('import');
    
    }
    
    /**
     * 将"更新企业号会员"任务提交至work队列
     *
     * @param $event
     */
    public function onUserUpdated($event) {
        
        $job = new ManageWechatMember($event->data, 'update');
        $this->dispatch($job);
        
    }
    
    /**
     * 将"删除企业号会员"任务提交至work队列
     *
     * @param $event
     */
    public function onUserDeleted($event) {
        
        $job = new ManageWechatMember($event->userid, 'delete');
        $this->dispatch($job);
        
    }
    
    /**
     * Register the listeners for the subscriber
     *
     * @param Dispatcher $events
     */
    public function subscribe(Dispatcher $events) {
        
        $e = 'App\\Events\\';
        $l = 'App\\Listeners\\UserEventSubscriber@';
        $events->listen($e . 'UserCreated', $l . 'onUserCreated');
        $events->listen($e . 'UserUpdated', $l . 'onUserUpdated');
        $events->listen($e . 'UserDeleted', $l . 'onUserDeleted');
        
    }
    
}