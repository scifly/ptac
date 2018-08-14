<?php
namespace App\Http\ViewComposers;

use Illuminate\Contracts\View\View;

/**
 * Class StudentAttendanceIndexComposer
 * @package App\Http\ViewComposers
 */
class StudentAttendanceIndexComposer {
    
    /**
     * @param View $view
     */
    public function compose(View $view) {
        
        $view->with([
            'titles'  => ['#', '姓名', '卡号', '打卡时间', '考勤时段', '考勤机', '进/出', '状态 . 操作'],
            'buttons' => [
                'stat' => [
                    'id'    => 'stat',
                    'label' => '统计',
                    'icon'  => 'fa fa-bar-chart',
                ],
            ],
            'filter' => true,
        ]);
        
    }
    
}