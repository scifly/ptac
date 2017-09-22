<?php

namespace App\Http\ViewComposers;

use App\Models\MessageType;
use App\Models\User;
use Illuminate\Contracts\View\View;

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