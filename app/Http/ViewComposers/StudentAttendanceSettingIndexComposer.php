<?php
namespace App\Http\ViewComposers;

use Illuminate\Contracts\View\View;

/**
 * Class StudentAttendanceSettingIndexComposer
 * @package App\Http\ViewComposers
 */
class StudentAttendanceSettingIndexComposer {
    
    /**
     * @param View $view
     */
    public function compose(View $view) {
        
        $view->with([
            'titles' => [
                '#', '名称', '所属年级', '学期', '是否公用', '起始时间',
                '结束时间', '周几', '进或出', '消息模板', '状态 . 操作',
            ],
        ]);
        
    }
    
}