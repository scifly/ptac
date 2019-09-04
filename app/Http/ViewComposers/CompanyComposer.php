<?php
namespace App\Http\ViewComposers;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Request;

/**
 * Class CompanyComposer
 * @package App\Http\ViewComposers
 */
class CompanyComposer {
    
    /**
     * @param View $view
     */
    public function compose(View $view) {
    
        if (explode('/', Request::path())[1] == 'index') {
            $view->with([
                'titles' => ['#', '名称', '备注', '创建于', '更新于', '状态 . 操作'],
            ]);
        }
    
    }
    
}