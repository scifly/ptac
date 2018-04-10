<?php
namespace App\Listeners;

use App\Jobs\ManageWechatMember;
use Illuminate\Events\Dispatcher;
use Illuminate\Foundation\Bus\DispatchesJobs;

class UserEventSubscriber {
    
    use DispatchesJobs;
    
    /**
     * 将"创建企业号会员"任务提交至work队列
     *
     * @param $event
     * @return bool
     */
    public function onUserCreated($event) {
        // $job = new ManageWechatMember($event->data, 'create');
        // $this->dispatch($job)->onQueue('import');
        ManageWechatMember::dispatch($event->data, 'create')->onQueue('import');
        
        return true;
    
    }
    
    /**
     * 将"更新企业号会员"任务提交至work队列
     *
     * @param $event
     * @return bool
     */
    public function onUserUpdated($event) {
        
        $job = new ManageWechatMember($event->data, 'update');
        $this->dispatch($job);
        
        return true;
        
    }
    
    /**
     * 将"删除企业号会员"任务提交至work队列
     *
     * @param $event
     * @return bool
     */
    public function onUserDeleted($event) {
        
        $job = new ManageWechatMember($event->data, 'delete');
        $this->dispatch($job);
        
        return true;
        
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