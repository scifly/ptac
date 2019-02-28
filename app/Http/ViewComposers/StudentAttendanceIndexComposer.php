<?php
namespace App\Http\ViewComposers;

use App\Helpers\ModelTrait;
use Illuminate\Contracts\View\View;

/**
 * Class StudentAttendanceIndexComposer
 * @package App\Http\ViewComposers
 */
class StudentAttendanceIndexComposer {
    
    use ModelTrait;
    
    /**
     * @param View $view
     */
    public function compose(View $view) {
        
        $view->with([
            'titles'  => [
                '#', '姓名',
                [
                    'title' => '打卡时间',
                    'html' => $this->inputDateTimeRange('打卡时间')
                ],
                '考勤时段', '考勤机',
                [
                    'title' => '进/出',
                    'html' => $this->singleSelectList(
                        [null => '全部', 0 => '进', 1 => '出'], 'filter_inorout'
                    )
                ],
                [
                    'title' => '状态 . 操作',
                    'html' => $this->singleSelectList(
                        [null => '全部', 0 => '异常', 1 => '正常'], 'filter_status'
                    )
                ],
            ],
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