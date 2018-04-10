<?php
namespace App\Http\ViewComposers;

use App\Helpers\ModelTrait;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;

class TabIndexComposer {
    
    use ModelTrait;
    
    public function compose(View $view) {
        
        $view->with([
            'batch' => true, # 需要批量操作
            'titles' => ['#', '名称', '控制器', '角色', '默认功能', '创建于', '更新于', '状态'],
            'uris'  => $this->uris(),
        ]);
        
    }
    
}