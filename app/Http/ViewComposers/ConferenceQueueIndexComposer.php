<?php
namespace App\Http\ViewComposers;

use Illuminate\Contracts\View\View;

/**
 * Class ConferenceQueueIndexComposer
 * @package App\Http\ViewComposers
 */
class ConferenceQueueIndexComposer {
    
    /**
     * @param View $view
     */
    public function compose(View $view) {
        
        $view->with([
            'titles' => ['#', '名称', '发起人', '会议室', '开始时间', '结束时间', '备注', '状态 . 操作'],
        ]);
        
    }
    
}