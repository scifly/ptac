<?php
namespace App\Http\ViewComposers;

use App\Helpers\ModelTrait;
use App\Models\Message;
use App\Models\MessageType;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class MessageCenterIndexComposer {
    
    use ModelTrait;
    
    public function compose(View $view) {
    
        $user = Auth::user();
        $sent = Message::whereSUserId($user->id)->get()
            ->unique('msl_id')->sortByDesc('created_at')
            ->groupBy('message_type_id');
        $received = Message::whereRUserId($user->id)
            ->get()->sortByDesc('created_at')
            ->groupBy('message_type_id');
        $count = Message::whereRUserId($user->id)
            ->where('read', '0')->count();
    
        $view->with([
            'messageTypes' => MessageType::pluck('name', 'id'),
            'sent' => $sent,
            'received' => $received,
            'count' => $count,
            'isEducator' => $user->educator ? true : false
        ]);
        
    }
    
}