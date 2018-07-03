<?php
namespace App\Http\ViewComposers;

use Illuminate\Contracts\View\View;

/**
 * Class ProcedureIndexComposer
 * @package App\Http\ViewComposers
 */
class ProcedureIndexComposer {
    
    /**
     * @param View $view
     */
    public function compose(View $view) {
        
        $view->with([
            'titles' => ['#', '流程类型', '所属学校', '名称', '备注', '创建于', '更新于', '状态 . 操作'],
        ]);
        
    }
    
}