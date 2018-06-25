<?php
namespace App\Http\ViewComposers;

use App\Helpers\ModelTrait;
use Illuminate\Contracts\View\View;

class CorpIndexComposer {
    
    use ModelTrait;
    
    public function compose(View $view) {
        
        $view->with([
            'titles' => [
                '#', '名称', '缩写', '所属运营', '企业号ID', '通讯录同步Secret',
                '创建于', '更新于', '状态 . 操作',
            ],
        ]);
        
    }
    
}