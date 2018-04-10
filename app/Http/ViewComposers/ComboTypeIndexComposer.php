<?php
namespace App\Http\ViewComposers;

use App\Helpers\ModelTrait;
use Illuminate\Contracts\View\View;

class ComboTypeIndexComposer {
    
    use ModelTrait;
    
    public function compose(View $view) {
        
        $view->with([
            'titles' => ['#', '名称', '金额', '折扣', '月数', '创建于', '更新于', '状态 . 操作'],
            'uris'   => $this->uris(),
        ]);
        
    }
    
}