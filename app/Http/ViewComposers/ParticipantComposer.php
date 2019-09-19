<?php
namespace App\Http\ViewComposers;

use Illuminate\Contracts\View\View;

/**
 * Class ParticipantComposer
 * @package App\Http\ViewComposers
 */
class ParticipantComposer {
    
    /**
     * @param View $view
     */
    public function compose(View $view) {
    
        $view->with([
            'titles' => ['#', '与会者', '会议名称', '签到时间', '创建于', '更新于', '状态 . 操作'],
        ]);
        
    }
    
}