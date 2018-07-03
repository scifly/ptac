<?php
namespace App\Http\ViewComposers;

use App\Helpers\ModelTrait;
use Illuminate\Contracts\View\View;

/**
 * Class MediaTypeIndexComposer
 * @package App\Http\ViewComposers
 */
class MediaTypeIndexComposer {
    
    use ModelTrait;
    
    /**
     * @param View $view
     */
    public function compose(View $view) {
        
        $view->with([
            'titles' => ['#', '名称', '备注', '创建于', '更新于', '状态 . 操作'],
        ]);
        
    }
    
}