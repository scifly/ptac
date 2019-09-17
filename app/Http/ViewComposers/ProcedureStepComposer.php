<?php
namespace App\Http\ViewComposers;

use App\Helpers\ModelTrait;
use App\Models\FlowType;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Request;

/**
 * Class ProcedureStepComposer
 * @package App\Http\ViewComposers
 */
class ProcedureStepComposer {
    
    use ModelTrait;
    
    /**
     * @param View $view
     */
    public function compose(View $view) {
    
        $action = explode('/', Request::path())[1];
        $data = $action == 'index'
            ? ['titles' => ['#', '流程', '审批用户', '相关人员', '步骤', '备注', '创建于', '更新于', '状态 . 操作']]
            : ['procedures' => FlowType::whereSchoolId($this->schoolId())->pluck('name', 'id')];
        
        $view->with($data);
        
    }
    
}