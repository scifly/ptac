<?php
namespace App\Http\ViewComposers;

use App\Helpers\ModelTrait;
use Illuminate\Contracts\View\View;

class SemesterIndexComposer {
    
    use ModelTrait;
    
    public function compose(View $view) {
        
        $view->with([
            'titles' => ['#', '名称', '起始日期', '结束日期', '创建于', '更新于', '状态'],
            'uris'   => $this->uris(),
        ]);
        
    }
    
}