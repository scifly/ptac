<?php
namespace App\Http\ViewComposers;

use Illuminate\Contracts\View\View;

/**
 * Class EducatorAttendanceIndexComposer
 * @package App\Http\ViewComposers
 */
class EducatorAttendanceIndexComposer {
    
    /**
     * @param View $view
     */
    public function compose(View $view) {
        
        $view->with([
            'titles'  => ['#', '姓名', '打卡时间', '进/出', '考勤时段', '状态 . 操作'],
            'buttons' => [
                'stat' => [
                    'id'    => 'stat',
                    'label' => '统计',
                    'icon'  => 'fa fa-bar-chart',
                ],
            ],
            'filter' => true
        ]);
        
    }
    
}