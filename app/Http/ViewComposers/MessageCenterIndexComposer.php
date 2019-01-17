<?php
namespace App\Http\ViewComposers;

use App\Helpers\ModelTrait;
use App\Models\MediaType;
use App\Models\Message;
use App\Models\MessageSendingLog;
use App\Models\MessageType;
use App\Models\School;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;

/**
 * Class MessageCenterIndexComposer
 * @package App\Http\ViewComposers
 */
class MessageCenterIndexComposer {
    
    use ModelTrait;
    
    const TPL = '<div class="weui-media-box weui-media-box_text">
        <a href="#" class="weui-cell_access">
            <p style="font-weight: %s">%s</p>
        </a>
        <ul class="weui-media-box__info">
            <li class="weui-media-box__info__meta" style="color: gray;">%s</li>
            <li class="weui-media-box__info__meta">%s</li>
            <li class="weui-media-box__info__meta weui-media-box__info__meta_extra">%s</li>
        </ul>
    </div>';
    
    /**
     * @param View $view
     */
    public function compose(View $view) {
        
        $user = Auth::user();
        $messages = Message::where(['s_user_id' => $user->id, 'r_user_id' => 0])
            ->orWhere('r_user_id', $user->id)
            ->get()->sortByDesc('created_at');
        $msgList = '';
        
        /** @var Message $message */
        foreach ($messages as $message) {
            if ($message->s_user_id == $user->id && !$message->r_user_id) {
                $direction = '发件';
                $color = $message->sent ? 'primary' : ($message->event_id ? 'warning' : 'error');
                $status = $message->sent ? '已发' : ($message->event_id ? '定时' : '草稿');
                $stat = '接收者';
                $msl = $message->messageSendinglog;
                $value = ($msl ? $msl->recipient_count : 0) . '人';
            } else {
                $direction = '收件';
                $color = $message->read ? 'primary' : 'error';
                $status = $message->read ? '已读' : '未读';
                $stat = '发送者';
                $value = $message->sender;
            }
            $msgList .= sprintf(
                self::TPL,
                $message->read ? 'normal' : 'bold',
                '[' . $message->mediaType->remark . ']' . $message->title,
                $message->messageType->name,
                sprintf(
                    '%s : <span class="color-%s">%s</span>, %s : %s',
                    $direction, $color, $status, $stat, $value
                ),
                $this->humanDate($message->created_at)
            );
        }
        $sent = !($canSend = !in_array($user->role(), ['监护人', '学生']))
            ? [] : $this->query(['s_user_id' => $user->id, 'r_user_id' => 0]);
        # 当前用户收到的消息/未读消息数量
        $where = ['r_user_id' => $user->id];
        $received = $this->query($where);
        # 格式化已发送/已收到消息的日期时间
        $this->format($sent, 'sent');
        $this->format($received, 'received');
        # 未读消息数量
        $where['read'] = 0;
        $count = Message::where($where)->count();
        $messageTypes = array_merge([0 => '全部'], MessageType::pluck('name', 'id')->toArray());
        $mediaTypes = array_merge([0 => '全部'], MediaType::pluck('remark', 'id')->toArray());
        $view->with([
            'messages'     => $msgList,
            'messageTypes' => $messageTypes,
            'mediaTypes'   => $mediaTypes,
            'sent'         => $sent,
            'received'     => $received,
            'count'        => $count,
            'acronym'      => School::find(session('schoolId'))->corp->acronym,
            'canSend'      => $canSend,
        ]);
        
    }
    
    /**
     * 格式化日期
     *
     * @param array $data
     * @param $direction - 已发/已收
     */
    private function format(array &$data, $direction) {
        
        foreach ($data as $type => &$messages) {
            foreach ($messages as &$message) {
                $message['created_at'] = $this->humanDate($message['created_at']);
                if ($direction == 'sent') {
                    $recipient = User::find($message['r_user_id']);
                    $msl = MessageSendingLog::find($message['msl_id']);
                    $message['recipient'] = $recipient
                        ? $recipient->realname
                        : ($msl ? $msl->recipient_count : '0') . ' 人';
                    $message['color'] = $message['sent'] ? 'green' : ($message['event_id'] ? 'orange' : 'red');
                    $message['status'] = $message['sent'] ? '已发送' : ($message['event_id'] ? '定时' : '草稿');
                } else {
                    $sender = User::find($message['s_user_id']);
                    $message['sender'] = $sender ? $sender->realname : '(未知)';
                    if (!$message['read']) {
                        $message['title'] = '<b>' . $message['title'] . '</b>';
                        $message['created_at'] = '<b>' . $message['created_at'] . '</b>';
                    }
                }
            }
        }
        
    }
    
    /**
     * 返回消息数组
     *
     * @param array $where
     * @return array
     */
    private function query(array $where) {
        
        return Message::where($where)->get()->sortByDesc('created_at')
            ->groupBy('message_type_id')->toArray();
        
    }
    
}