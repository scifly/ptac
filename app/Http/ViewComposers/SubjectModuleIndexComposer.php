<?php
namespace App\Http\ViewComposers;

use Illuminate\Contracts\View\View;

class SubjectModuleIndexComposer {
    
    public function compose(View $view) {
        
        $view->with([
            'titles' => ['#', '科目名称', '次分类名称', '次分类权重', '创建于', '更新于', '状态 . 操作'],
        ]);
        
    }
    
}