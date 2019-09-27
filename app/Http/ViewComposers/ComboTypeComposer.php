<?php
namespace App\Http\ViewComposers;

use Illuminate\Contracts\View\View;

/**
 * Class ComboTypeComposer
 * @package App\Http\ViewComposers
 */
class ComboTypeComposer {
    
    /**
     * @param View $view
     */
    public function compose(View $view) {
        
        $view->with([
            'titles' => ['#', '名称', '金额', '折扣', '月数', '创建于', '更新于', '状态 . 操作'],
        ]);
        
    }
    
}