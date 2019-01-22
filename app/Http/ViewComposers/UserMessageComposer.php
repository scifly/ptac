<?php
namespace App\Http\ViewComposers;

use App\Helpers\ModelTrait;
use Illuminate\Contracts\View\View;

/**
 * Class UserMessageComposer
 * @package App\Http\ViewComposers
 */
class UserMessageComposer {
    
    use ModelTrait;
    
    /**
     * @param View $view
     */
    public function compose(View $view) {
        
        list($optionAll, $htmlCommType, $htmlApp, $htmlMessageType) = $this->messageFilters();
        $view->with([
            'titles'    => [
                '#',
                ['title' => '通信方式', 'html' => $htmlCommType],
                ['title' => '应用', 'html' => $htmlApp],
                '消息批次', '发送者',
                ['title' => '消息类型', 'html' => $htmlMessageType],
                ['title' => '接收于', 'html' => $this->inputDateTimeRange('接收于')],
                [
                    'title' => '状态',
                    'html'  => $this->singleSelectList(
                        array_merge($optionAll, [0 => '未读', 1 => '已读']), 'filter_read'
                    ),
                ],
            ],
            'batch'     => true,
            'removable' => true,
            'filter'    => true,
        ]);
        
    }
    
}