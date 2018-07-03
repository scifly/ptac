<?php
namespace App\Http\ViewComposers;

use Illuminate\Contracts\View\View;

/**
 * Class WsmArticleIndexComposer
 * @package App\Http\ViewComposers
 */
class WsmArticleIndexComposer {
    
    /**
     * @param View $view
     */
    public function compose(View $view) {
        
        $view->with([
            'titles' => ['#', '所属栏目', '文章名称', '文章摘要', '创建于', '更新于', '状态 . 操作'],
        ]);
        
    }
    
}