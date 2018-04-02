<?php
namespace App\Http\ViewComposers;

use App\Helpers\ModelTrait;
use App\Models\App;
use App\Models\CommType;
use App\Models\MessageType;
use App\Models\User;
use Illuminate\Contracts\View\View;

class MessageComposer {
    
    use ModelTrait;
    
    public function compose(View $view) {
        
        $view->with([
            'users'        => User::pluck('realname', 'id'),
            'messageTypes' => MessageType::pluck('name', 'id'),
            'commtypes'    => CommType::pluck('name', 'id'),
            'apps'         => App::pluck('name', 'id'),
            'uris'         => $this->uris(),
        ]);
        
    }
    
}