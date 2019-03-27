<?php
namespace App\Http\ViewComposers;

use App\Models\IconType;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Request;

/**
 * Class IconComposer
 * @package App\Http\ViewComposers
 */
class IconComposer {
    
    /**
     * @param View $view
     */
    public function compose(View $view) {
    
        $action = explode('/', Request::path())[1];
        $data = $action == 'index'
            ? ['titles' => ['#', '名称', '图标类型', '创建于', '更新于', '状态 . 操作']]
            : ['iconTypes' => IconType::pluck('name', 'id')];
        
        $view->with($data);
        
    }
    
}