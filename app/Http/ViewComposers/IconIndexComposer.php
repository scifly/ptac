<?php
namespace App\Http\ViewComposers;

use Illuminate\Contracts\View\View;

/**
 * Class IconIndexComposer
 * @package App\Http\ViewComposers
 */
class IconIndexComposer {
    
    /**
     * @param View $view
     */
    public function compose(View $view) {
        
        $view->with([
            'titles' => ['#', '名称', '图标类型', '创建于', '更新于', '状态 . 操作'],
        ]);
        
    }
    
}