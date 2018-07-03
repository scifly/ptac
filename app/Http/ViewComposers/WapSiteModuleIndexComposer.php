<?php
namespace App\Http\ViewComposers;

use App\Helpers\ModelTrait;
use Illuminate\Contracts\View\View;

/**
 * Class WapSiteModuleIndexComposer
 * @package App\Http\ViewComposers
 */
class WapSiteModuleIndexComposer {
    
    use ModelTrait;
    
    /**
     * @param View $view
     */
    public function compose(View $view) {
        
        $view->with([
            'titles' => ['#', '栏目名称', '所属网站', '创建于', '更新于', '状态 . 操作'],
        ]);
        
    }
    
}