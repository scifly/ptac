<?php
namespace App\Http\ViewComposers;

use App\Helpers\ModelTrait;
use App\Models\Department;
use App\Models\Message;
use App\Models\MessageType;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
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
        
        $user = Auth::user();
        $chosenTargetsHtml = '';
        $content = $selectedDepartmentIds = $selectedUserIds = [];
        $title = $text = $url = $btntxt = $mediaId = $accept = $filename = $mpnewsList = null;
        if (Request::route('id')) {
            $content = $this->message->detail(
                Request::route('id')
            );
            $type = $content['type'];
            switch ($type) {
                case 'text':
                    $text = $content[$type]->{'content'} ?? '';
                    break;
                case 'image':
                    list($mediaId, $filename) = $this->fileAttrs($content, $type);
                    $accept = 'image/*';
                    break;
                case 'voice':
                    list($mediaId, $filename) = $this->fileAttrs($content, $type);
                    $accept = 'audio/*';
                    break;
                case 'video':
                    $title = $content[$type]->{'title'};
                    $text = $content[$type]->{'description'} ?? '';
                    list($mediaId, $filename) = $this->fileAttrs($content, $type);
                    $accept = 'video/mp4';
                    break;
                case 'file':
                    list($mediaId, $filename) = $this->fileAttrs($content, $type);
                    $accept = '*';
                    break;
                case 'textcard':
                    $title = $content[$type]->{'title'};
                    $text = $content[$type]->{'description'} ?? '';
                    $url = $content['textcard']->{'url'};
                    $btntxt = $content['textcard']->{'btntxt'};
                    break;
                case 'mpnews':
                    $articles = $content['mpnews']->{'articles'};
                    $tpl = <<<HTML
                        <li id="mpnews-%s" class="weui-uploader__file" style="background-image: (%s)"
                            data-media-id="%s" data-author="%s" data-content="%s" data-digest="%s"
                            data-filename="%s" data-url="%s" data-image="%s" data-title="%s"></li>
HTML;
                    for ($i = 0; $i < sizeof($articles); $i++) {
                        $article = $articles[$i];
                        $mpnewsList .= sprintf(
                            $tpl, $i,
                            $article->{'image_url'},
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
                    $text = $content[$type];
                    break;
                
            }
            $message = $content[$type];
            $selectedDepartmentIds = explode('|', $message->{'toparty'});
            $touser = explode('|', $message->{'touser'});
            $selectedUserIds = User::whereIn('userid', $touser)->pluck('id')->toArray();
            list($departmentHtml, $userHtml) = array_map(
                function ($ids, $type) {
                    /** @noinspection HtmlUnknownTarget */
                    $tpl = <<<HTML
                        <a id="%s" class="chosen-results-item js-chosen-results-item %s"
                           data-list="%s" data-uid="%s" data-type="%s">
                            <img src="%s" style="%s" />
                        </a>
HTML;
                    $html = '';
                    $imgName = $type == 'department' ? 'department.png' : 'personal.png';
                    $imgStyle = $type == 'department' ? '' : 'border-radius: 50%;';
                    foreach ($ids as $id) {
                        $html .= sprintf(
                            $tpl, $type . '-' . $id, $type, $id, $id,
                            $type, '/img/' . $imgName, $imgStyle
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
        $data = [
            'departments'  => Department::whereIn('id', $departmentIds)->get(),
            'messageTypes' => MessageType::pluck('name', 'id'),
            'msgTypes'     => [
                'text'     => '文本',
                'image'    => '图片',
                'voice'    => '语音',
                'video'    => '视频',
                'file'     => '文件',
                'textcard' => '卡片',
                'mpnews'   => '图文',
                'sms'      => '短信',
            ]
        ];
        if (Request::route('id')) {
            $view->with(array_merge($data, [
                'selectedMsgTypeId' => $content['type'],
                'selectedDepartmentIds' => $selectedDepartmentIds,
                'selectedUserIds' => $selectedUserIds,
                'chosenTargetsHtml' => $chosenTargetsHtml,
                'title' => $title,
                'content' => $text,
                'url' => $url,
                'btntxt' => $btntxt,
                'mediaId' => $mediaId,
                'accept' => $accept,
                'filename' => $filename,
                'mpnewsList' => $mpnewsList
            ]));
        } else {
            $view->with($data);
        }
        
    }
    
    /**
     * 获取文件类消息的mediaId及filename属性值
     *
     * @param $content
     * @param $type
     * @return array
     */
    private function fileAttrs($content, $type) {
    
        $mediaId = $content[$type]->{'media_id'};
        $paths = explode('/', $content[$type]->{'path'});
        $filename = $paths[sizeof($paths) - 1];
        
        return [$mediaId, $filename];
        
    }
    
}