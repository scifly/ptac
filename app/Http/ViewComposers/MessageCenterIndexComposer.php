<?php
namespace App\Http\ViewComposers;

use App\Helpers\Constant;
use App\Helpers\ModelTrait;
use App\Models\Message;
use App\Models\MessageType;
use App\Models\School;
use App\Models\Student;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;

/**
 * Class MessageCenterIndexComposer
 * @package App\Http\ViewComposers
 */
class MessageCenterIndexComposer {
    
    use ModelTrait;
    
    /**
     * @param View $view
     */
    public function compose(View $view) {
        
        $user = Auth::user();
        # 当前用户能否发送消息
        $canSend = !$user->custodian && !$user->student;
        # 当前用户发送的消息
        $sent = [];
        if ($canSend) {
            $sent = Message::whereSUserId($user->id)->get()
                ->/*unique('msl_id')->*/sortByDesc('created_at')
                ->groupBy('message_type_id')->toArray();
        }
        # 消息接收者的用户id
        $rUserIds = [$user->id];
        $schoolId = session('schoolId');
        if ($user->custodian) {
            # 如果当前登录用户角色为监护人，则将其在当前学校
            # 对应的学生用户id合并至消息接收者用户id数组
            $students = $user->custodian->students->filter(
                function (Student $student) use ($schoolId) {
                    return $student->squad->grade->school_id == $schoolId;
                }
            );
            $rUserIds = array_merge(
                $rUserIds, $students->pluck('user_id')->toArray()
            );
        }
        # 当前用户收到的消息
        $received = Message::whereIn('r_user_id', $rUserIds)
            ->get()->sortByDesc('created_at')
            ->groupBy('message_type_id')->toArray();
        # 格式化已发送/已收到消息的日期时间
        $this->format($sent, 'sent');
        $this->format($received, 'received');
        # 已收到消息的未读数量
        $count = Message::whereIn('r_user_id', $rUserIds)->where('read', '0')->count();
        
        $view->with([
            'messageTypes' => MessageType::pluck('name', 'id'),
            'sent'         => $sent,
            'received'     => $received,
            'count'        => $count,
            'acronym'      => School::find($schoolId)->corp->acronym,
            'canSend'      => $canSend
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
                $message['created_at'] = $this->humanDate($message['created_at']);
                $object = json_decode($message['content']);
                $type = array_search(mb_substr($message['title'], -3, 2), Constant::INFO_TYPE);
                if (!$type) {
                    $messageType = MessageType::find($message['message_type_id']);
                    $messageTypeName = $messageType ? $messageType->name : '未知消息';
                    if (is_object($object) && property_exists(get_class($object), 'msgtype')) {
                        $type = $object->{'msgtype'};
                        $title = $messageTypeName . '(' . Constant::INFO_TYPE[$type] . ')';
                    } else {
                        $title = $messageTypeName . '(未知)';
                    }
                    Message::find($message['id'])->update(['title' => $title]);
                    $message['title'] = $title;
                }
                if (!$message['read'] && $direction == 'received') {
                    $message['title'] = '<b>' . $message['title'] . '</b>';
                    $message['created_at'] = '<b>' . $message['created_at'] . '</b>';
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