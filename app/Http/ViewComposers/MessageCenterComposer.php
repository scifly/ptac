<?php

namespace App\Http\ViewComposers;

use App\Helpers\ControllerTrait;
use App\Models\App;
use App\Models\CommType;
use App\Models\MessageType;
use App\Models\User;
use Illuminate\Contracts\View\View;

class MessageCenterComposer {
    use ControllerTrait;
    protected $messageTypes;

    public function __construct(MessageType $messageTypes) {

        $this->messageTypes = $messageTypes;
    }

    public function compose(View $view) {
        // print_r($this->messageTypes->pluck('name', 'id'));
        // die;
        $view->with([
            'messageTypes' => $this->messageTypes->pluck('name', 'id'),
        ]);
    }

}