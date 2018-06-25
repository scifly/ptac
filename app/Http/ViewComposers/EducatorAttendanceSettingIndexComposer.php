<?php
namespace App\Http\ViewComposers;

use Illuminate\Contracts\View\View;

class EducatorAttendanceSettingIndexComposer {
    
    public function compose(View $view) {
        
        $view->with([
            'titles' => ['姓名', '手机号码', '打卡时间', '进/出', '状态 . 操作'],
        ]);
        
    }
    
}