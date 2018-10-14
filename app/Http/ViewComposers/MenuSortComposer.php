<?php
namespace App\Http\ViewComposers;

use App\Models\MenuTab;
use App\Models\Tab;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Request;

/**
 * Class MenuSortComposer
 * @package App\Http\ViewComposers
 */
class MenuSortComposer {
    
    /**
     * @param View $view
     */
    public function compose(View $view) {
        
        $menuId = Request::route('id');
        $tabIds = MenuTab::whereMenuId($menuId)
            ->get()->sortBy('tab_order')
            ->pluck('tab_id')->toArray();
        
        $view->with([
            'tabs'   => Tab::whereIn('id', $tabIds)->get(),
            'menuId' => $menuId,
        ]);
        
    }
    
}