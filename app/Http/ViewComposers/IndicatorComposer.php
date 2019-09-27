<?php
namespace App\Http\ViewComposers;

use Illuminate\Contracts\View\View;

/**
 * Class IndicatorComposer
 * @package App\Http\ViewComposers
 */
class IndicatorComposer {
    
    /** @param View $view */
    public function compose(View $view) {
        
        $view->with([
            'titles' => ['#', '名称', '加/减分', '备注', '创建于', '更新于', '状态 . 操作'],
        ]);
        
    }
    
}