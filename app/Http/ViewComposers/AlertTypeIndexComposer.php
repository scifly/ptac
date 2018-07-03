<?php
namespace App\Http\ViewComposers;

use Illuminate\Contracts\View\View;

/**
 * Class AlertTypeIndexComposer
 * @package App\Http\ViewComposers
 */
class AlertTypeIndexComposer {
    
    /**
     * @param View $view
     */
    public function compose(View $view) {
        
        $view->with([
            'titles' => ['#', '名称', '英文名称', '创建于', '更新于', '状态 . 操作'],
        ]);
        
    }
    
}