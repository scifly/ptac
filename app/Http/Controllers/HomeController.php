<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\MenuTab;
use App\Models\Tab;
use Illuminate\Support\Facades\Request;


class HomeController extends Controller {
    
    protected $menu;
    
    public function __construct(Menu $menu) { $this->menu = $menu; }
    
    public function index() {
        
        return 'hi';
        
    }

    public function menu($id) {

        session(['menuId' => $id]);
        # 获取卡片列表
        $tabArray = [];
        $tabRanks = MenuTab::whereMenuId($id)->get()->sortBy('tab_order')->toArray();
        $isTabLegit = true;
        foreach ($tabRanks as $rank) {
            $tab = Tab::whereId($rank['tab_id'])->first();
            if (!empty($tab->action->route)) {
                $tabArray[] = [
                    'id' => 'tab_' . $tab->id,
                    'name' => $tab->name,
                    'active' => false,
                    'url' => $tab->action->route
                ];
            } else {
                $isTabLegit = false;
                break;
            }
        }
        if ($isTabLegit) {
            $tabArray[0]['active'] = true;
        } else {
            $tabArray = [];
        }
        # 获取菜单列表
        $menu = $this->menu->getMenuHtml($id);
        
        return view('home.page', [
            'menu' => $menu,
            'tabs' => $tabArray,
            'menuId' => $id,
            'js' => 'js/home/page.js',
        ]);
        
    }
    
}
