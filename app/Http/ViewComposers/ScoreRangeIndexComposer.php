<?php
namespace App\Http\ViewComposers;

use Illuminate\Contracts\View\View;

class ScoreRangeIndexComposer {
    
    public function compose(View $view) {
        
        $view->with([
            'buttons' => [
                'stat' => [
                    'id'    => 'stat',
                    'label' => '统计',
                    'icon'  => 'fa fa-bar-chart',
                ],
            ],
            'titles'  => ['#', '名称', '起始分数', '截止分数', '创建于', '更新于', '状态 . 操作'],
        ]);
        
    }
}