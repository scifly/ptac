<?php
namespace App\Http\ViewComposers;

use Illuminate\Contracts\View\View;

/**
 * Class PrizeComposer
 * @package App\Http\ViewComposers
 */
class PrizeComposer {
    
    /**
     * @param View $view
     */
    public function compose(View $view) {
    
        $view->with([
            'titles' => ['#', '名称', '分数', '备注', '创建于', '更新于', '状态 . 操作']
        ]);
        
    }
    
}