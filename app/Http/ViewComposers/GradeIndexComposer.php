<?php
namespace App\Http\ViewComposers;

use App\Helpers\ModelTrait;
use Illuminate\Contracts\View\View;

class GradeIndexComposer {
    
    use ModelTrait;
    
    public function compose(View $view) {
        
        $view->with([
            'titles' => ['#', '名称', '年级主任', '创建于', '更新于', '状态'],
            'uris'   => $this->uris(),
        ]);
        
    }
    
}