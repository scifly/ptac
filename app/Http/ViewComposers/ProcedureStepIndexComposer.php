<?php
namespace App\Http\ViewComposers;

use Illuminate\Contracts\View\View;

class ProcedureStepIndexComposer {
    
    public function compose(View $view) {
        
        $view->with([
            'titles' => ['#', '流程', '审批用户', '相关人员', '步骤', '备注', '创建于', '更新于', '状态 . 操作'],
        ]);
        
    }
    
}