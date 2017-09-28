<?php
namespace App\Listeners;

use App\Jobs\ManageWechatMember;
use Illuminate\Events\Dispatcher;
use Illuminate\Foundation\Bus\DispatchesJobs;

class UserEventSubscriber {

    use DispatchesJobs;

    /**
     * @param $event
     */
    public function onUserCreated($event) {

        $job = new ManageWechatMember($event->user, 'create');
        $this->dispatch($job);

    }

    public function onUserUpdated($event) {

        $job = new ManageWechatMember($event->user, 'update');
        $this->dispatch($job);

    }

    public function onUserDeleted($event) {

        $job = new ManageWechatMember($event->userId, 'delete');
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