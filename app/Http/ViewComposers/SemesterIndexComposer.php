<?php
namespace App\Http\ViewComposers;

use Illuminate\Contracts\View\View;

/**
 * Class SemesterIndexComposer
 * @package App\Http\ViewComposers
 */
class SemesterIndexComposer {
    
    /**
     * @param View $view
     */
    public function compose(View $view) {
        
        $view->with([
            'titles' => ['#', '名称', '起始日期', '结束日期', '创建于', '更新于', '状态 . 操作'],
        ]);
        
    }
    
}