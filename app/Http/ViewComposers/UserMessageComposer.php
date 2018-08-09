<?php
namespace App\Http\ViewComposers;

use Illuminate\Contracts\View\View;

/**
 * Class UserMessageComposer
 * @package App\Http\ViewComposers
 */
class UserMessageComposer {
    
    /**
     * @param View $view
     */
    public function compose(View $view) {
        
        $view->with([
            'titles' => ['#', '通信方式', '应用', '消息批次', '发送者', '类型', '接收于', '状态'],
            'batch' => true,
            'removable' => true
        ]);
        
    }
    
}