<?php
namespace App\Http\ViewComposers;

use Illuminate\Contracts\View\View;

/**
 * Class GroupIndexComposer
 * @package App\Http\ViewComposers
 */
class GroupIndexComposer {
    
    /**
     * @param View $view
     */
    public function compose(View $view) {
        
        $view->with([
            'titles' => ['#', '名称', '所属学校', '所属企业', '备注', '创建于', '更新于', '状态 . 操作'],
        ]);
        
    }
    
}