<?php

namespace App\Http\Controllers;

use App\Http\Requests\MenuRequest;
use App\Models\Menu;
use Illuminate\Support\Facades\Request;

class MenuController extends Controller {
    
    protected $menu;
    
    function __construct(Menu $menu) {
        
        $this->menu = $menu;
        
    }
    
    public function index() {
        
        if (Request::method() === 'POST' ) {
            return $this->menu->tree();
        }
        return parent::output(__METHOD__);
        
    }
    
    /** 显示创建菜单记录的表单 */
    public function create() {
        
        return parent::output(__METHOD__);
        
    }
    
    /**
     * 保存新创建的菜单记录
     *
     * @param MenuRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(MenuRequest $request) {
        
        return $this->menu->store($request) ? parent::succeed() : parent::fail();
        
    }
    
    /**
     * 显示指定的菜单记录详情
     *
     * @param $id
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public function show($id) {
        
        $menu = $this->menu->find($id);
        if (!$menu) { return parent::notFound(); }
        return parent::output(__METHOD__, ['menu' => $menu]);
        
    }
    
    /**
     * 显示编辑指定菜单记录的表单
     *
     * @param $id
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public function edit($id) {
    
        $menu = $this->menu->find($id);
        if (!$menu) { return parent::notFound(); }
        
        # 获取已选定的卡片
        $menuTabs = $menu->tabs;
        $selectedTabs = [];
        foreach ($menuTabs as $tab) {
            $selectedTabs[$tab->id] = $tab->name;
        }
        return parent::output(__METHOD__, [
            'menu' => $menu,
            'selectedTabs' => $selectedTabs
        ]);
        
    }
    
    /**
     * 更新指定的菜单记录
     *
     * @param MenuRequest $request
     * @param integer $id 菜单ID
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(MenuRequest $request, $id) {
        
        $menu = $this->menu->find($id);
        if (!$menu) { return parent::notFound(); }
        return $this->menu->modify($request, $id) ? parent::succeed() : parent::fail();
        
    }
    
    /**
     * 更新菜单所处位置
     *
     * @param $id
     * @param $parentId
     * @return \Illuminate\Http\JsonResponse
     */
    public function move($id, $parentId) {

        $menu = $this->menu->find($id);
        $parentMenu = $this->menu->find($parentId);
        if (!$menu || !$parentMenu) {
            return parent::notFound();
        }
        return $this->menu->move($id, $parentId) ? parent::succeed() : parent::fail();
        
    }
    
    /**
     * 删除指定的菜单记录
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id) {
        
        $menu = $this->menu->find($id);
        if (!$menu) { return parent::notFound(); }
        return $this->menu->remove($id) ? parent::succeed() : parent::fail();
        
    }
    
    /** 保存菜单的顺序 */
    public function sort() {
        
        $positions = Request::get('data');
        foreach ($positions as $id => $pos) {
            $menu = $this->menu->find($id);
            if (isset($menu)) {
                $menu->position = $pos;
                $menu->save();
            }
        }
    
    }
    
}
