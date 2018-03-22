<?php

namespace App\Http\ViewComposers;

use App\Helpers\ModelTrait;
use App\Models\Grade;
use App\Models\Squad;
use Illuminate\Contracts\View\View;

class EducatorAttendanceIndexComposer {

    use ModelTrait;

    public function compose(View $view) {
    
        $view->with([
            'titles' => ['#', '姓名', '打卡时间', '进/出', '考勤时段', '状态'],
            'uris' => $this->uris(),
        ]);

    }

}