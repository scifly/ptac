<?php
namespace App\Http\ViewComposers;

use App\Models\ProcedureType;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Request;

/**
 * Class ProcedureComposer
 * @package App\Http\ViewComposers
 */
class ProcedureComposer {
    
    /**
     * @param View $view
     */
    public function compose(View $view) {
    
        $action = explode('/', Request::path())[1];
        $data = $action == 'index'
            ? ['titles' => ['#', '流程类型', '所属学校', '名称', '备注', '创建于', '更新于', '状态 . 操作']]
            : ['procedureTypes' => ProcedureType::pluck('name', 'id')];
        
        $view->with($data);
        
    }
    
}