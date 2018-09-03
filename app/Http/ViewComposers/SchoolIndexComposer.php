<?php
namespace App\Http\ViewComposers;

use Illuminate\Contracts\View\View;

/**
 * Class SchoolIndexComposer
 * @package App\Http\ViewComposers
 */
class SchoolIndexComposer {
    
    /**
     * @param View $view
     */
    public function compose(View $view) {
        
        $view->with([
            'titles' => ['#', '名称', '地址', '类型', '所属企业', '创建于', '更新于', '同步状态', '状态 . 操作'],
            'batch'  => true,
        ]);
        
    }
    
}