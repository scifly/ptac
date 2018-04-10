<?php
namespace App\Http\ViewComposers;

use App\Helpers\ModelTrait;
use Illuminate\Contracts\View\View;

class ProcedureIndexComposer {
    
    use ModelTrait;
    
    public function compose(View $view) {
        
        $view->with([
            'titles' => ['#', '流程类型', '所属学校', '名称', '备注', '创建于', '更新于', '状态 . 操作'],
            'uris'   => $this->uris(),
        ]);
        
    }
    
}