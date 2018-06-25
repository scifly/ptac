<?php
namespace App\Http\ViewComposers;

use Illuminate\Contracts\View\View;

class TeamIndexComposer {
    
    public function compose(View $view) {
        
        $view->with([
            'titles' => ['#', '名称', '备注', '创建于', '更新于', '状态 . 操作'],
        ]);
        
    }
    
}