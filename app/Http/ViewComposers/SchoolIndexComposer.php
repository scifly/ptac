<?php
namespace App\Http\ViewComposers;

use App\Helpers\ModelTrait;
use Illuminate\Contracts\View\View;

class SchoolIndexComposer {
    
    use ModelTrait;
    
    public function compose(View $view) {
        
        $view->with([
            'titles' => ['#', '名称', '地址', '类型', '所属企业', '创建于', '更新于', '状态 . 操作'],
            'batch' => true,
            'uris'   => $this->uris(),
        ]);
        
    }
    
}