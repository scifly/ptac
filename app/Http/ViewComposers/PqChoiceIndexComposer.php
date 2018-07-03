<?php
namespace App\Http\ViewComposers;

use Illuminate\Contracts\View\View;

/**
 * Class PqChoiceIndexComposer
 * @package App\Http\ViewComposers
 */
class PqChoiceIndexComposer {
    
    /**
     * @param View $view
     */
    public function compose(View $view) {
        
        $view->with([
            'titles' => ['#', '题目名称', '选项内容', '选项编号', '创建于', '更新于', '操作'],
        ]);
        
    }
    
}