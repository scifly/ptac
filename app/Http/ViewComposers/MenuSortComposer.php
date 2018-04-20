<?php
namespace App\Http\ViewComposers;

use App\Helpers\ModelTrait;
use App\Models\MenuTab;
use App\Models\Tab;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Request;

class MenuSortComposer {
    
    use ModelTrait;
    
    public function compose(View $view) {
    
        $menuId = Request::route('id');
        $tabs = MenuTab::whereMenuId($menuId)
            ->get()->sortBy('tab_order')
            ->pluck('tab_id')->toArray();
    
        $view->with([
            'tabs' => $tabs,
            'menuId' => $menuId,
            'uris' => $this->uris()
        ]);
        
    }
    
}