<?php
namespace App\Http\ViewComposers;

use Illuminate\Contracts\View\View;

class SquadIndexComposer {
    
    public function compose(View $view) {
        
        $view->with([
            'titles' => ['#', '名称', '所属年级', '班主任', '创建于', '更新于', '状态 . 操作'],
        ]);
        
    }
    
}