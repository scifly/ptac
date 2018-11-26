<?php
namespace App\Http\ViewComposers;

use App\Helpers\ModelTrait;
use App\Models\Department;
use App\Models\Message;
use App\Models\MessageType;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

/**
 * Class MessageCenterComposer
 * @package App\Http\ViewComposers
 */
class MessageCenterComposer {
    
    use ModelTrait;
    
    protected $message;
    
    /**
     * MessageCenterComposer constructor.
     * @param Message $message
     */
    function __construct(Message $message) {
        
        $this->message = $message;
        
    }
    
    /**
     * @param View $view
     */
    public function compose(View $view) {
        
        $user = User::find(Auth::id());
        $chosenTargetsHtml = '';
        $detail = $selectedDepartmentIds = $selectedUserIds = null;
        $title = $text = $url = $btntxt = $mediaId = $accept = null;
        $filename = $filepath = $mpnewsList = $timing = null;
        if (Request::route('id')) {
            $messageId = Request::route('id');
            $detail = $this->message->detail($messageId);
            $timing = $this->message->find($messageId)->event_id;
            $type = $detail['type'];
            $message = json_decode($detail[$type]);
            $content = $message->{$type};
            switch ($type) {
                case 'text':
                    $text = $content->{'content'};
                    break;
                case 'image':
                    list($mediaId, $filename, $filepath) = $this->fileAttrs($content);
                    $accept = 'image/*';
                    break;
                case 'voice':
                    list($mediaId, $filename, $filepath) = $this->fileAttrs($content);
                    $accept = 'audio/*';
                    break;
                case 'video':
                    $title = $content->{'title'};
                    $text = $content->{'description'};
                    list($mediaId, $filename, $filepath) = $this->fileAttrs($content);
                    $accept = 'video/mp4';
                    break;
                case 'file':
                    list($mediaId, $filename, $filepath) = $this->fileAttrs($content);
                    $accept = '*';
                    break;
                case 'textcard':
                    $title = $content->{'title'};
                    $text = $content->{'description'};
                    $url = $content->{'url'};
                    $btntxt = $content->{'btntxt'};
                    break;
                case 'mpnews':
                    $articles = $content->{'articles'};
                    $tpl = <<<HTML
                        <li id="mpnews-%s" class="weui-uploader__file" style="background-image: %s"
                            data-media-id="%s" data-author="%s" data-content="%s" data-digest="%s"
                            data-filename="%s" data-url="%s" data-image="%s" data-title="%s"></li>
HTML;
                    for ($i = 0; $i < sizeof($articles); $i++) {
                        $article = $articles[$i];
                        $mpnewsList .= sprintf(
                            $tpl, $i,
                            'url(/' . $article->{'image_url'} . ')',
                            $article->{'thumb_media_id'},
                            $article->{'author'},
                            $article->{'content'},
                            $article->{'digest'},
                            $article->{'filename'},
                            $article->{'content_source_url'},
                            $article->{'image_url'},
                            $article->{'title'}
                        );
                    }
                    break;
                case 'sms':
                    $text = $content;
                    break;
                default:
                    break;
            }
            $selectedDepartmentIds = !empty($message->{'toparty'})
                ? explode('|', $message->{'toparty'}) : [];
            $touser = !empty($message->{'touser'})
                ? explode('|', $message->{'touser'}) : [];
            $selectedUserIds = User::whereIn('userid', $touser)->pluck('id')->toArray();
            list($departmentHtml, $userHtml) = array_map(
                function ($ids, $type) {
                    /** @noinspection HtmlUnknownTarget */
                    $tpl = '<a id="%s" class="chosen-results-item" data-uid="%s" data-type="%s">' .
                        '<img src="%s" style="%s" /></a>';
                    $html = '';
                    $imgName = $type == 'department' ? 'department.png' : 'personal.png';
                    $imgStyle = $type == 'department' ? '' : 'border-radius: 50%;';
                    foreach ($ids as $id) {
                        $html .= sprintf(
                            $tpl, $type . '-' . $id,
                            $id, $type, '/img/' . $imgName,
                            $imgStyle
                        );
                    }
                    
                    return $html;
                },
                [$selectedDepartmentIds, $selectedUserIds], ['department', 'user']
            );
            $chosenTargetsHtml = $departmentHtml . $userHtml;
        }
        # 对当前用户可见的所有部门id
        $departmentIds = $this->departmentIds($user->id, session('schoolId'));
        $view->with([
            'departments'           => Department::whereIn('id', $departmentIds)->get(),
            'messageTypes'          => MessageType::pluck('name', 'id'),
            'msgTypes'              => [
                'text'     => '文本',
                'image'    => '图片',
                'voice'    => '语音',
                'video'    => '视频',
                'file'     => '文件',
                'textcard' => '卡片',
                'mpnews'   => '图文',
                'sms'      => '短信',
            ],
            'selectedMsgTypeId'     => $detail ? $detail['type'] : null,
            'selectedDepartmentIds' => $selectedDepartmentIds,
            'selectedUserIds'       => $selectedUserIds,
            'chosenTargetsHtml'     => $chosenTargetsHtml,
            'title'                 => $title,
            'content'               => $text,
            'url'                   => $url,
            'btntxt'                => $btntxt,
            'mediaId'               => $mediaId,
            'filepath'              => $filepath,
            'accept'                => $accept,
            'filename'              => $filename,
            'mpnewsList'            => $mpnewsList,
            'timing'                => $timing
        ]);
        
    }
    
    /**
     * 获取文件类消息的mediaId及filename属性值
     *
     * @param $msg
     * @return array
     */
    private function fileAttrs($msg) {
        
        $mediaId = $msg->{'media_id'};
        $filepath = $msg->{'path'};
        $paths = explode('/', $msg->{'path'});
        $filename = $paths[sizeof($paths) - 1];
        
        return [$mediaId, $filename, $filepath];
        
    }
    
}