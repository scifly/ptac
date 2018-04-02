<?php
namespace App\Http\ViewComposers;

use App\Helpers\ModelTrait;
use Illuminate\Contracts\View\View;

class AttendanceMachineComposer {
    
    use ModelTrait;
    
    public function compose(View $view) {
        
        $view->with([
            'titles' => ['#', '名称', '安装位置', '考勤机ID', '创建于', '更新于', '状态'],
            'uris'   => $this->uris(),
        ]);
        
    }
    
}