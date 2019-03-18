<?php
namespace App\Http\ViewComposers;

use Illuminate\Contracts\View\View;

/**
 * Class TurnstileIndexComposer
 * @package App\Http\ViewComposers
 */
class TurnstileIndexComposer {
    
    /**
     * @param View $view
     */
    public function compose(View $view) {
        
        $view->with([
            'buttons'        => [
                'issue' => [
                    'id' => 'refresh',
                    'label' => '刷新',
                    'icon' => 'fa fa-refresh'
                ]
            ],
            'titles' => ['#', 'sn', '安装地点', '门数', '设备id', '创建于', '更新于', '状态 . 操作'],
        ]);
        
    }
    
}