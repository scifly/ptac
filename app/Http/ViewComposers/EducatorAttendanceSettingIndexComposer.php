<?php
namespace App\Http\ViewComposers;

use Illuminate\Contracts\View\View;

/**
 * Class EducatorAttendanceSettingIndexComposer
 * @package App\Http\ViewComposers
 */
class EducatorAttendanceSettingIndexComposer {
    
    /**
     * @param View $view
     */
    public function compose(View $view) {
        
        $view->with([
            'titles' => ['姓名', '手机号码', '打卡时间', '进/出', '状态 . 操作'],
        ]);
        
    }
    
}