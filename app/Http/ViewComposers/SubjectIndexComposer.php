<?php
namespace App\Http\ViewComposers;

use Illuminate\Contracts\View\View;

class SubjectIndexComposer {
    
    public function compose(View $view) {
        
        $view->with([
            'titles' => ['#', '名称', '副科', '满分', '及格线', '创建于', '更新于', '状态 . 操作'],
        ]);
        
    }
    
}