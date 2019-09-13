<?php
namespace App\Http\ViewComposers;

use Illuminate\Contracts\View\View;

/**
 * Class BuildingComposer
 * @package App\Http\ViewComposers
 */
class BuildingComposer {
    
    /**
     * @param View $view
     */
    public function compose(View $view) {
    
        $view->with([
            'titles' => ['#', '名称', '楼层数', '备注', '创建于', '更新于', '状态 . 操作']
        ]);
        
    }
    
}