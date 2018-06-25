<?php
namespace App\Http\ViewComposers;

use App\Helpers\ModelTrait;
use Illuminate\Contracts\View\View;

class ActionIndexComposer {
    
    use ModelTrait;
    
    public function compose(View $view) {
        
        $view->with([
            'titles' => ['#', '名称', '方法', '路由', '控制器', 'View路径', 'js路径', '请求类型', '状态 . 操作'],
            // 'uris'   => $this->uris(),
        ]);
        
    }
    
}