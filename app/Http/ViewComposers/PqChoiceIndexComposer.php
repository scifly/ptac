<?php
namespace App\Http\ViewComposers;

use App\Helpers\ModelTrait;
use Illuminate\Contracts\View\View;

class PqChoiceIndexComposer {
    
    use ModelTrait;
    
    public function compose(View $view) {
        
        $view->with([
            'titles' => ['#', '题目名称', '选项内容', '选项编号', '创建于', '更新于', '操作'],
            'uris'   => $this->uris(),
        ]);
        
    }
    
}