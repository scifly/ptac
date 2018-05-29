<?php
namespace App\Http\ViewComposers;

use App\Helpers\ModelTrait;
use App\Models\Message;
use App\Models\MessageType;
use App\Models\School;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;

class MessageCenterIndexComposer {
    
    use ModelTrait;
    
    public function compose(View $view) {
    
        $user = Auth::user();
        $sent = Message::whereSUserId($user->id)->get()
            ->unique('msl_id')->sortByDesc('created_at')
            ->groupBy('message_type_id')->toArray();
        $received = Message::whereRUserId($user->id)
            ->get()->sortByDesc('created_at')
            ->groupBy('message_type_id')->toArray();
        $this->formatDateTime($sent, 'sent');
        $this->formatDateTime($received, 'received');
        $count = Message::whereRUserId($user->id)
            ->where('read', '0')->count();
        $school = School::find(session('schoolId'));
    
        $view->with([
            'messageTypes' => MessageType::pluck('name', 'id'),
            'sent' => $sent,
            'received' => $received,
            'count' => $count,
            'acronym' => $school->corp->acronym,
            'canSend' => !$user->custodian ? true : false
        ]);
        
    }
    
    /**
     * 格式化日期
     *
     * @param array $data
     * @param $direction
     */
    private function formatDateTime(array &$data, $direction) {
        
        foreach ($data as $type => &$messages) {
            foreach ($messages as $message) {
                Carbon::setLocale('zh');
                $dt = Carbon::createFromFormat('Y-m-d H:i:s', $message['created_at']);
                $message['created_at'] = $dt->diffForHumans();
                if ($direction == 'sent') {
                    $message['recipient'] = User::find($message['r_user_id'])->realname;
                } else {
                    $message['sender'] = User::find($message['s_user_id'])->realname;
                }
            }
        }
        
    }
    
}