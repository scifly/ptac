<?php
namespace App\Http\ViewComposers;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Request;

/**
 * Class CameraComposer
 * @package App\Http\ViewComposers
 */
class CameraComposer {
    
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
                'titles'  => ['#', '设备名称', 'ip', 'mac', '安装地点', '方向', '创建于', '更新于', '状态 . 操作'],
            ]);
        }
        
    }
    
}