<?php
namespace App\Http\ViewComposers;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Request;

/**
 * Class TurnstileComposer
 * @package App\Http\ViewComposers
 */
class TurnstileComposer {
    
    /**
     * @param View $view
     */
    public function compose(View $view) {
    
        $action = explode('/', Request::path())[1];
        if ($action == 'index') {
            $view->with([
                'buttons' => [
                    'store' => [
                        'id'    => 'store',
                        'label' => '刷新',
                        'icon'  => 'fa fa-refresh'
                    ]
                ],
                'titles'  => ['#', 'sn', '安装地点', '门数', '设备id', '创建于', '更新于', '状态 . 操作'],
            ]);
        }
        
    }
    
}