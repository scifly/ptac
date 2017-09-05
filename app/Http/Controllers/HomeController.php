<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\MenuTab;
use App\Models\Tab;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Session;


class HomeController extends Controller {
    
    protected $menu;
    
    public function __construct(Menu $menu) { $this->menu = $menu; }
    
    public function index() {
        
        return 'hi';
        
    }

    public function menu($id) {

        if (!session('menuId') || session('menuId') !== $id) {
            session(['menuId' => $id]);
            session(['menuChanged' => true]);
        } else {
            Session::forget('menuChanged');
        }
        
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
            # 刷新页面时打开当前卡片, 不一定是第一个卡片
            if (session('tabId')) {
                $key = array_search('tab_' . session('tabId'), array_column($tabArray, 'id'));
                $tabArray[$key]['active'] = true;
                if (!session('tabChanged') && !session('menuChanged')) {
                    $tabArray[$key]['url'] = session('tabUrl');
                }
            } else {
                $tabArray[0]['active'] = true;
            }
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
