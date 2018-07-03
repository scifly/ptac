<?php
namespace App\Http\ViewComposers;

use Illuminate\Contracts\View\View;

/**
 * Class PqSubjectIndexComposer
 * @package App\Http\ViewComposers
 */
class PqSubjectIndexComposer {
    
    /**
     * @param View $view
     */
    public function compose(View $view) {
        
        $view->with([
            'titles' => ['#', '题目名称', '所属问卷', '题目类型', '创建于', '更新于', '操作'],
        ]);
        
    }
    
}