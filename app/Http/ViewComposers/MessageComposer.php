<?php
namespace App\Http\ViewComposers;

use App\Helpers\ModelTrait;
use App\Models\App;
use App\Models\CommType;
use App\Models\MessageType;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Request;

/**
 * Class MessageComposer
 * @package App\Http\ViewComposers
 */
class MessageComposer {
    
    use ModelTrait;
    
    /**
     * @param View $view
     */
    public function compose(View $view) {
    
        $action = explode('/', Request::path())[1];
        if ($action == 'index') {
            list($optionAll, $htmlCommType, $htmlMediaType, $htmlMessageType) = $this->messageFilters();
            $titles = [
                '#', '标题', '批次',
                ['title' => '通信方式', 'html' => $htmlCommType],
                ['title' => '格式', 'html' => $htmlMediaType],
                ['title' => '类型', 'html' => $htmlMessageType],
            ];
            $data = [
                'titles'       => array_merge($titles, [
                    '接收人数',
                    ['title' => '发送于', 'html' => $this->inputDateTimeRange('发送于')],
                    [
                        'title' => '状态',
                        'html'  => $this->singleSelectList(
                            array_merge($optionAll, [0 => '草稿', 1 => '已发', 2 => '定时']), 'filter_sent'
                        )
                    ],
                ]),
                'rTitles'      => array_merge($titles, [
                    '发送者',
                    ['title' => '接收于', 'html' => $this->inputDateTimeRange('接收于')],
                    [
                        'title' => '状态',
                        'html'  => $this->singleSelectList(
                            array_merge($optionAll, [0 => '未读', 1 => '已读']), 'filter_read'
                        )
                    ],
                ]),
                'smsMaxLength' => 300,
                'messageTypes' => MessageType::whereEnabled(1)->pluck('name', 'id')->toArray(),
                'batch'        => true,
                'filter'       => true
            ];
        } else {
            $data = [
                'users'        => User::pluck('realname', 'id'),
                'messageTypes' => MessageType::pluck('name', 'id'),
                'commtypes'    => CommType::pluck('name', 'id'),
                'apps'         => App::pluck('name', 'id'),
            ];
        }
        
        $view->with($data);
    
    }
    
}