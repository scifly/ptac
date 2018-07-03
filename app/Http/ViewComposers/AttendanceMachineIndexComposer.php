<?php
namespace App\Http\ViewComposers;

use Illuminate\Contracts\View\View;

/**
 * Class AttendanceMachineIndexComposer
 * @package App\Http\ViewComposers
 */
class AttendanceMachineIndexComposer {
    
    /**
     * @param View $view
     */
    public function compose(View $view) {
        
        $view->with([
            'batch'  => true,
            'titles' => ['#', '名称', '安装位置', '考勤机ID', '创建于', '更新于', '状态 . 操作'],
        ]);
        
    }
    
}