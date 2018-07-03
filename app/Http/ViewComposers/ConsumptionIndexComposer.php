<?php
namespace App\Http\ViewComposers;

use Illuminate\Contracts\View\View;

/**
 * Class ConsumptionIndexComposer
 * @package App\Http\ViewComposers
 */
class ConsumptionIndexComposer {
    
    /**
     * @param View $view
     */
    public function compose(View $view) {
        
        $view->with([
            'buttons' => [
                'stat'   => [
                    'id'    => 'stat',
                    'label' => '统计',
                    'icon'  => 'fa fa-bar-chart',
                ],
                'export' => [
                    'id'    => 'export',
                    'label' => '批量导出',
                    'icon'  => 'fa fa-download',
                ],
            ],
            'titles'  => ['#', '学生', '消费地点', '消费机ID', '类型', '金额', '时间'],
        ]);
        
    }
    
}