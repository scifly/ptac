<?php
namespace App\Http\ViewComposers;

use Illuminate\Contracts\View\View;

class ConferenceRoomIndexComposer {
    
    public function compose(View $view) {
        
        $view->with([
            'batch'  => true,
            'titles' => ['#', '名称', '容量', '备注', '创建于', '更新于', '状态 . 操作'],
        ]);
        
    }
    
}