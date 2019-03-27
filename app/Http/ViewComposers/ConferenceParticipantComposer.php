<?php
namespace App\Http\ViewComposers;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Request;

/**
 * Class ConferenceParticipantComposer
 * @package App\Http\ViewComposers
 */
class ConferenceParticipantComposer {
    
    /**
     * @param View $view
     */
    public function compose(View $view) {
    
        $action = explode('/', Request::path())[1];
        if ($action == 'index') {
            $view->with([
                'titles' => ['#', '与会者', '会议名称', '签到时间', '创建于', '更新于', '状态 . 操作'],
            ]);
        }
        
    }
    
}