<?php
namespace App\Http\ViewComposers;

use Illuminate\Contracts\View\View;

class UserMessageComposer {
    
    public function compose(View $view) {
        
        $view->with([
            'titles' => ['#', '通信方式', '应用', '消息批次', '接收者', '类型', '发送于', '状态(发送/阅读)'],
        ]);
        
    }
    
}