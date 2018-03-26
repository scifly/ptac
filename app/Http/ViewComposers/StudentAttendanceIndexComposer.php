<?php

namespace App\Http\ViewComposers;

use App\Helpers\ModelTrait;
use App\Models\Grade;
use App\Models\Squad;
use Illuminate\Contracts\View\View;

class StudentAttendanceIndexComposer {

    use ModelTrait;

    public function compose(View $view) {
    
        $view->with([
            'titles' => ['#', '姓名', '卡号', '打卡时间', '考勤时段', '考勤机', '进/出', '状态'],
            'uris' => $this->uris(),
        ]);

    }

}