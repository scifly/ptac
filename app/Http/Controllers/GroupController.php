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
        
        xdebug_break();
        $abc = 'abc';
        if (Request::method() === 'POST') {
            return $this->menu->tree();
        }
        $tabActions = [];
        $tabs = $this->tab->all();
        foreach ($tabs as $tab) {
            $actions = $this->action->where('controller', $tab->controller)->get(['id', 'name']);
            $actionList = [];
            foreach ($actions as $action) {
                $actionList[] = ['id' => $action->id, 'name' => $action->name];
            }
            $tabActions[] = [
                'tab' => ['id' => $tab->id, 'name' => $tab->name],
                'actions' => $actionList
            ];
        }
        return $this->output(__METHOD__, ['tabActions' => $tabActions]);
        
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
        if (!$group) {
            return $this->notFound();
        }
        return $this->output(__METHOD__, ['group' => $group]);
        
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
