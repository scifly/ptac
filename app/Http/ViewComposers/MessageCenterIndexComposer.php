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
    
        # 搜索
        if (Request::method() == 'POST') {
            $keyword = Request::get('keyword');
            $type = Request::get('type');
            if (!empty($keyword)) {
                switch ($type) {
                    case 'sent':
                        $sent = Message::whereSUserId($user->id)
                            ->where('content', 'like', '%' . $keyword . '%')
                            ->orWhere('title', 'like', '%' . $keyword . '%')
                            ->get();
                        if (sizeof($sent) != 0) {
                            foreach ($sent as $s) {
                                $s['user'] = User::find($s['r_user_id'])->realname;
                            }
                        }
                        return response([
                            'sent' => $sent,
                            'type' => $type,
                        ]);
                    case 'received':
                        $received = Message::whereRUserId($user->id)
                            ->where('content', 'like', '%' . $keyword . '%')
                            ->orWhere('title', 'like', '%' . $keyword . '%')
                            ->get();
                        if (sizeof($received) != 0) {
                            foreach ($received as $r) {
                                $r['user'] = User::find($r['user'])->realname;
                            }
                        }
                        return response([
                            'received' => $received,
                            'type'     => $type,
                        ]);
                    default:
                        break;
                }
            }
        }
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