<?php

namespace App\Listeners;

use App\Jobs\ManageSendMessage;
use Illuminate\Events\Dispatcher;

class MessageEventSubscriber
{

    public function onSendMessage($event)
    {
        ManageSendMessage::dispatch($event->data);

    }


    /**
     * Register the listeners for the subscriber
     *
     * @param Dispatcher $events
     */
    public function subscribe(Dispatcher $events)
    {

        $e = 'App\\Events\\';
        $l = 'App\\Listeners\\StudentEventSubscriber@';
        $events->listen($e . 'MessageCreated', $l . 'onSendMessage');

    }

}