<?php
namespace App\Http\Controllers;

use App\Http\Requests\GroupRequest;
use App\Models\Action;
use App\Models\Group;
use App\Models\Menu;
use App\Models\Tab;
use Illuminate\Support\Facades\Request;

/**
 * 角色
 *
 * Class GroupController
 * @package App\Http\Controllers
 */
class GroupController extends Controller {
    
    protected $group, $menu, $tab, $action;
    
    function __construct(Group $group, Menu $menu, Tab $tab, Action $action) {
        
        $this->group = $group;
        $this->menu = $menu;
        $this->tab = $tab;
        $this->action = $action;
        
    }
    
    /**
     * 角色列表
     *
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public function index() {
        
        if (Request::get('draw')) {
            return response()->json($this->group->datatable());
        }
        return $this->output(__METHOD__);
        
    }
    
    /**
     * 创建角色
     *
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public function create() {
        
        if (Request::method() === 'POST') {
            return $this->menu->tree();
        }
        return $this->output(__METHOD__);
        
    }
    
    /**
     * 保存角色
     *
     * @param GroupRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(GroupRequest $request) {
        return $this->group->store($request->all())
            ? $this->succeed() : $this->fail();
        
    }
    
    /**
     * 角色详情
     *
     * @param $id
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public function show($id) {
        
        $group = $this->group->find($id);
        if (!$group) { return $this->notFound(); }
        return $this->output(__METHOD__, ['group' => $group]);
        
    }
    
    /**
     * 编辑角色
     *
     * @param $id
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public function edit($id) {
        
        $group = $this->group->find($id);
        if (!$group) { return $this->notFound(); }
        if (Request::method() === 'POST') {
            return $this->menu->tree();
        }
        $menus = $group->menus;
        $selectedMenuIds = [];
        foreach ($menus as $menu) {
            $selectedMenuIds[] = $menu->id;
        }
        $tabs = $group->tabs;
        $selectedTabs = [];
        foreach ($tabs as $tab) {
            $selectedTabs[] = $tab->id;
        }
        $actions = $group->actions;
        $selectedActions = [];
        foreach ($actions as $action) {
            $selectedActions[] = $action->id;
        }
        return $this->output(__METHOD__, [
            'group'           => $group,
            'selectedMenuIds' => implode(',', $selectedMenuIds),
            'selectedTabs'    => $selectedTabs,
            'selectedActions' => $selectedActions,
        ]);
        
    }
    
    /**
     * 更新角色
     *
     * @param GroupRequest $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(GroupRequest $request, $id) {
        
        $group = $this->group->find($id);
        if (!$group) {
            return $this->notFound();
        }
        return $group->modify($request->all(), $id)
            ? $this->succeed() : $this->fail();
        
    }
    
    /**
     * 删除角色
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id) {
        
        $group = $this->group->find($id);
        if (!$group) { return $this->notFound(); }
        return $group->remove($id) ? $this->succeed() : $this->fail();
        
    }
    
}
