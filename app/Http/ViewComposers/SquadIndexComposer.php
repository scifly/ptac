<?php
namespace App\Http\ViewComposers;

use App\Helpers\ModelTrait;
use Illuminate\Contracts\View\View;

class SquadIndexComposer {
    
    use ModelTrait;
    
    public function compose(View $view) {
        
        $view->with([
            'titles' => ['#', '名称', '所属年级', '班主任', '创建于', '更新于', '状态 . 操作'],
            'uris'   => $this->uris(),
        ]);
        
    }
    
}