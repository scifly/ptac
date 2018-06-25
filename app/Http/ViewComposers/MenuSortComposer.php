<?php
namespace App\Http\ViewComposers;

use App\Models\MenuTab;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Request;

class MenuSortComposer {
    
    public function compose(View $view) {
        
        $menuId = Request::route('id');
        $tabs = MenuTab::whereMenuId($menuId)
            ->get()->sortBy('tab_order')
            ->pluck('tab_id')->toArray();
        $view->with([
            'tabs'   => $tabs,
            'menuId' => $menuId,
        ]);
        
    }
    
}