<?php
namespace App\Http\ViewComposers;

use App\Helpers\ModelTrait;
use Illuminate\Contracts\View\View;

class WsmArticleIndexComposer {
    
    use ModelTrait;
    
    public function compose(View $view) {
        
        $view->with([
            'titles' => ['#', '所属栏目', '文章名称', '文章摘要', '创建于', '更新于', '状态 . 操作'],
            'uris'   => $this->uris(),
        ]);
        
    }
    
}