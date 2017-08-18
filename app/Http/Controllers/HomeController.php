<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use Illuminate\Support\Facades\Request;


class HomeController extends Controller {
    
    protected $menu;
    
    /**
     * Create a new controller instance.
     * @param Menu $menu
     */
    public function __construct(Menu $menu) {
        
        $this->menu = $menu;

    }
    
    /**
     * Show the application dashboard.
     *
     */
    public function index() {
        
    }

    public function menu($id) {
        
        // 获取卡片列表
        $tabArray = [];
        $menu = $this->menu->find($id);
        $tabs = $menu->tabs;
        foreach ($tabs as $tab) {
            $tabArray[] = [
                'id' => 'tab_' . $tab->id,
                'name' => $tab->name,
                'active' => false,
                'url' => $tab->action->route
            ];
        }
        $tabArray[0]['active'] = true;
        
        // 获取菜单列表
        $menu = $this->menu->getMenuHtml($id);
        
        return view('home.page', [
            'menu' => $menu,
            'tabs' => $tabArray,
            'menuId' => $id,
            'js' => 'js/home/page.js',
            'datatable' => true,
            'form' => true,
            'jstree' => true,
            'dialog' => true
        ]);
        
    }
    
}
