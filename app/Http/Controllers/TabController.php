<?php

namespace App\Http\Controllers;

use App\Http\Requests\TabRequest;
use App\Models\Action;
use App\Models\Menu;
use App\Models\Tab;
use Illuminate\Support\Facades\Request;

class TabController extends Controller {
    
    protected $tab, $action, $menu;
    
    function __construct(Tab $tab, Menu $menu, Action $action) {
        
        $this->tab = $tab;
        $this->menu = $menu;
        $this->action = $action;
        
    }
    
    /**
     * 显示卡片列表
     *
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public function index() {

        if (Request::get('draw')) {
            return response()->json($this->tab->datatable());
        }
        return parent::output(__METHOD__);
        
    }
    
    /**
     * 显示创建卡片记录的表单
     *
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public function create() {
        
        return parent::output(__METHOD__, ['menus' => $this->menu->leaves(1)]);
        
    }
    
    /**
     * 保存新创建的卡片记录
     *
     * @param TabRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(TabRequest $request) {
    
        return $this->tab->store($request) ? parent::succeed() : parent::fail();
        
    }
    
    /**
     * 显示指定的卡片记录详情
     *
     * @param $id
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public function show($id) {

        $tab = $this->tab->find($id);
        if (!$tab) { return parent::notFound(); };
        return parent::output(__METHOD__, ['tab' => $tab]);
        
    }
    
    /**
     * 显示编辑指定卡片记录的表单
     *
     * @param $id
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public function edit($id) {
        
        $tab = $this->tab->find($id);
        if (!$tab) { return parent::notFound(); }
        $tabMenus = $tab->menus;
        $selectedMenus = [];
        foreach ($tabMenus as $menu) {
            $selectedMenus[$menu->id] = $menu->name;
        }
        return parent::output(__METHOD__, [
            'tab' => $tab,
            'menus' => $this->menu->leaves(1),
            'selectedMenus' => $selectedMenus,
        ]);
        
    }
    
    /**
     * 更新指定的卡片记录
     *
     * @param TabRequest $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(TabRequest $request, $id) {
        
        $tab = $this->tab->find($id);
        if (!$tab) { return parent::notFound(); }
        return $this->tab->modify($request, $id) ? parent::succeed() : parent::fail();
        
    }
    
    /**
     * 删除指定的卡片记录
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id) {

        $tab = $this->tab->find($id);
        if (!$tab) { return parent::notFound(); }
        return $this->tab->remove($id) ? parent::succeed() : parent::fail();
        
    }
    
}
