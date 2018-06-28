<?php
namespace App\Http\ViewComposers;

use Illuminate\Contracts\View\View;

class UserEventComposer {
    
    public function compose(View $view) {
    
        $view->with([
            'titles' => [
                '#', '名称', '备注', '地点', '开始时间', '结束时间',
                '公共事件', '课程', '提醒', '创建者', '更新于'
            ]
        ]);
        
    }
    
}