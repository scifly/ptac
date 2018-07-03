<?php
namespace App\Http\ViewComposers;

use Illuminate\Contracts\View\View;

/**
 * Class GradeIndexComposer
 * @package App\Http\ViewComposers
 */
class GradeIndexComposer {
    
    /**
     * @param View $view
     */
    public function compose(View $view) {
        
        $view->with([
            'titles' => ['#', '名称', '年级主任', '创建于', '更新于', '状态 . 操作'],
        ]);
        
    }
    
}