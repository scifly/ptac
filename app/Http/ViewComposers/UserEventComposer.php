<?php
namespace App\Http\ViewComposers;

use Illuminate\Contracts\View\View;

/**
 * Class UserEventComposer
 * @package App\Http\ViewComposers
 */
class UserEventComposer {
    
    /**
     * @param View $view
     */
    public function compose(View $view) {
    
        $view->with([
            'titles' => [
                '#', '名称', '备注', '地点', '开始时间', '结束时间',
                '公共事件', '课程', '提醒', '创建者', '更新于'
            ]
        ]);
        
    }
    
}