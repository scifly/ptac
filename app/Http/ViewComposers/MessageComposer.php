<?php

namespace App\Http\ViewComposers;

use App\Models\App;
use App\Models\CommType;
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
    protected $commtypes;
    protected $apps;

    public function __construct(User $users, MessageType $messageTypes, CommType $commTypes, App $apps) {

        $this->users = $users;
        $this->messageTypes = $messageTypes;
        $this->commtypes = $commTypes;
        $this->apps = $apps;

    }

    public function compose(View $view) {


        $view->with([
            'users' => $this->users->pluck('realname', 'id'),
            'messageTypes' => $this->messageTypes->pluck('name', 'id'),
            'commtypes' => $this->commtypes->pluck('name', 'id'),
            'apps' => $this->apps->pluck('name', 'id'),
        ]);
    }

}