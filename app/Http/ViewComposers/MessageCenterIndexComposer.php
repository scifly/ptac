<?php
namespace App\Http\ViewComposers;

use App\Helpers\Constant;
use App\Helpers\ModelTrait;
use App\Models\Message;
use App\Models\MessageType;
use App\Models\School;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

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
        $this->format($sent, 'sent');
        $this->format($received, 'received');
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
    private function format(array &$data, $direction) {
        
        foreach ($data as $type => &$messages) {
            foreach ($messages as &$message) {
                Carbon::setLocale('zh');
                $dt = Carbon::createFromFormat('Y-m-d H:i:s', $message['created_at']);
                $message['created_at'] = $dt->diffForHumans();
                $object = json_decode(json_decode($message['content']));
                $type = array_search(mb_substr($message['title'], -3, 2), Constant::INFO_TYPES);
                if (!$type) {
                    if (property_exists(get_class($object), 'msgtype')) {
                        $type = $object->{'msgtype'};
                        $title = MessageType::find($message->message_type_id)->name
                            . '(' . Constant::INFO_TYPES[$type] . ')';
                    } else {
                        $title = '(未知消息)';
                    }
                    Message::find($message['id'])->update(['title' => $title]);
                    $message['title'] = $title;
                }
                if ($direction == 'sent') {
                    $recipient = User::find($message['r_user_id']);
                    $message['recipient'] = $recipient ? $recipient->realname : '(未知)';
                } else {
                    $sender = User::find($message['s_user_id']);
                    $message['sender'] = $sender ? $sender->realname : '(未知)';
                }
            }
        }
        
    }
    
}