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

        $this->dispatch(
            new ManageWechatMember($event->data, 'create')
        );
    
    }
    
    /**
     * 将"更新企业号会员"任务提交至work队列
     *
     * @param $event
     */
    public function onUserUpdated($event) {
        
        $this->dispatch(
            new ManageWechatMember($event->data, 'update')
        );
        
    }
    
    /**
     * 将"删除企业号会员"任务提交至work队列
     *
     * @param $event
     */
    public function onUserDeleted($event) {
        
        $this->dispatch(
            new ManageWechatMember($event->data, 'delete')
        );
        
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