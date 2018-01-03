<?php

namespace App\Http\ViewComposers;

use App\Helpers\ModelTrait;
use App\Models\MessageType;
use Illuminate\Contracts\View\View;

class MessageCenterComposer {

    use ModelTrait;

    public function compose(View $view) {

        $view->with([
            'messageTypes' => MessageType::pluck('name', 'id'),
            'uris' => $this->uris()
        ]);

    }

}