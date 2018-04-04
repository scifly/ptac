<?php
namespace App\Http\ViewComposers;

use App\Helpers\ModelTrait;
use Illuminate\Contracts\View\View;

class TabIndexComposer {
    
    use ModelTrait;
    
    public function compose(View $view) {
        
        $view->with([
            'titles' => ['#', '名称', '控制器', '角色', '默认功能', '创建于', '更新于', '状态'],
            'uris'  => $this->uris(),
        ]);
        
    }
    
}