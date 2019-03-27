<?php
namespace App\Http\ViewComposers;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Request;

/**
 * Class ConferenceRoomComposer
 * @package App\Http\ViewComposers
 */
class ConferenceRoomComposer {
    
    /**
     * @param View $view
     */
    public function compose(View $view) {
    
        $action = explode('/', Request::path())[1];
        if ($action == 'index') {
            $view->with([
                'batch'  => true,
                'titles' => ['#', '名称', '容量', '备注', '创建于', '更新于', '状态 . 操作'],
            ]);
        }
        
    }
    
}