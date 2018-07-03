<?php
namespace App\Http\ViewComposers;

use Illuminate\Contracts\View\View;

/**
 * Class TabIndexComposer
 * @package App\Http\ViewComposers
 */
class TabIndexComposer {
    
    /**
     * @param View $view
     */
    public function compose(View $view) {
        
        $view->with([
            'batch'  => true, # 需要批量操作
            'titles' => ['#', '名称', '控制器', '角色', '默认功能', '创建于', '更新于', '状态 . 操作'],
        ]);
        
    }
    
}