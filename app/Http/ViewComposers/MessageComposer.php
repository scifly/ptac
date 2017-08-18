<?php

namespace App\Http\ViewComposers;

use App\Models\Corp;
use App\Models\Educator;
use App\Models\MessageType;
use App\Models\School;
use App\Models\SchoolType;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;

class MessageComposer {

    protected $users;
    protected $messageTypes;

    public function __construct(User $users, MessageType $messageTypes) {

        $this->users = $users;
        $this->messageTypes = $messageTypes;

    }

    public function compose(View $view) {


        $view->with([
            'users' => $this->users->pluck('realname', 'id'),
            'messageTypes' => $this->messageTypes->pluck('name', 'id'),
        ]);
    }

}