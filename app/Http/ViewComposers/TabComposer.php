<?php
namespace App\Http\ViewComposers;

use App\Models\Action;
use App\Models\Icon;
use App\Models\Menu;
use App\Models\Tab;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Request;

/**
 * Class TabComposer
 * @package App\Http\ViewComposers
 */
class TabComposer {
    
    protected $icon, $action, $menu;
    
    /**
     * TabComposer constructor.
     * @param Icon $icon
     * @param Action $action
     * @param Menu $menu
     */
    function __construct(Icon $icon, Action $action, Menu $menu) {
        
        $this->icon = $icon;
        $this->action = $action;
        $this->menu = $menu;
        
    }
    
    /**
     * @param View $view
     */
    public function compose(View $view) {
        
        $selectedMenus = null;
        if (Request::route('id')) {
            $tab = Tab::find(Request::route('id'));
            $tabMenus = $tab->menus;
            $selectedMenus = [];
            foreach ($tabMenus as $menu) {
                $selectedMenus[$menu->id] = $menu->name;
            }
        }
        $view->with([
            'icons'         => $this->icon->icons(),
            'actions'       => $this->action->actions(),
            'groups'        => [
                0 => '所有',
                1 => '运营',
                2 => '企业',
                3 => '学校',
            ],
            'menus'         => $this->menu->leaves(1),
            'selectedMenus' => $selectedMenus,
        ]);
        
    }
    
}