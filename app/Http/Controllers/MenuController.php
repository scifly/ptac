<?php

namespace App\Http\Controllers;

use App\Http\Requests\MenuRequest;
use App\Models\Menu;
use Illuminate\Support\Facades\Request;

class MenuController extends Controller {
    
    protected $menu;
    
    function __construct(Menu $menu) { $this->menu = $menu; }
    
    public function index() {
        
        if (Request::ajax()) {
            $menus = $this->menu->get(['id', 'parent_id', 'name', 'position'])
                ->sortBy(['position'])->toArray();
            $data = [];
            foreach ($menus as $menu) {
                if (isset($menu['parent_id'])) {
                    $m = $this->menu->find($menu['id']);
                    $icon = $m->icon;
                    $menu['name'] = ($icon ? '<i class="' . $icon->name . '"></i>' : '<i class="fa fa-circle-o"></i>') .
                        '&nbsp;' . $menu['name'];
                }
                $parentId = isset($menu['parent_id']) ? $menu['parent_id'] : '#';
                $data[] = [
                    'id' => $menu['id'],
                    'parent' => $parentId,
                    'text' => $menu['name']
                ];
            }
            return response()->json($data);
        }
        return view('menu.index', [
            'dialog' => true,
            'js' => 'js/menu/index.js',
            'jstree' => true,
            'form' => true,
            'menus' => $this->menu->leaves(1)
        ]);
        
    }
    
    /**
     * 显示创建菜单记录的表单
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create() {
        
        if (Request::ajax()) {
            return response()->json(['html' => view('menu.create')->render()]);
        }
        return view('menu.create', [
            'js' => 'js/action/create.js',
            'form' => true,
        ]);
        
    }
    
    /**
     * 保存新创建的菜单记录
     *
     * @param MenuRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(MenuRequest $request) {
        
        if ($this->menu->store($request)) {
            $this->result['statusCode'] = self::HTTP_STATUSCODE_OK;
            $this->result['message'] = self::MSG_CREATE_OK;
        } else {
            $this->result['statusCode'] = self::HTTP_STATUSCODE_INTERNAL_SERVER_ERROR;
            $this->result['message'] = '';
        }
        return response()->json($this->result);
        
    }
    
    /**
     * 显示指定的菜单记录详情
     *
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show($id) {
        
        return view('menu.show', [
            'menu' => $this->menu->findOrFail($id)
        ]);
        
    }
    
    /**
     * 显示编辑指定菜单记录的表单
     *
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @internal param null $parentId
     */
    public function edit($id) {
        
        if (Request::ajax()) {
            $menu = $this->menu->find($id);
            $menuTabs = $menu->tabs;
            $selectedTabs = [];
            foreach ($menuTabs as $tab) {
                $selectedTabs[$tab->id] = $tab->name;
            }
            return response()->json([
                'html' => view('menu.edit', [
                    'selectedTabs' => $selectedTabs,
                    'menu' => $menu
                ])->render()
            ]);
        }
        return view('menu.edit', [
            'js' => 'js/menu/edit.js',
            'form' => true,
            'menu' => $this->menu->findOrFail($id),
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
        
        if ($this->menu->modify($request, $id)) {
            $this->result['statusCode'] = self::HTTP_STATUSCODE_OK;
            $this->result['message'] = self::MSG_EDIT_OK;
        } else {
            $this->result['statusCode'] = self::HTTP_STATUSCODE_INTERNAL_SERVER_ERROR;
            $this->result['message'] = '修改失败';
        }
        
        return response()->json($this->result);
        
    }
    
    /**
     * 更新菜单所处位置
     *
     * @param $id
     * @param $parentId
     * @return \Illuminate\Http\JsonResponse
     */
    public function move($id, $parentId) {
    
        if ($this->menu->move($id, $parentId)) {
            $this->result['statusCode'] = self::HTTP_STATUSCODE_OK;
            $this->result['message'] = '菜单位置更新成功';
        } else {
            $this->result['statusCode'] = self::HTTP_STATUSCODE_INTERNAL_SERVER_ERROR;
            $this->result['message'] = '菜单位置更新失败';
        }
        
        return response()->json($this->result);
        
    }
    
    /**
     * 删除指定的菜单记录
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id) {
        
        if ($this->menu->remove($id)) {
            $this->result['statusCode'] = self::HTTP_STATUSCODE_OK;
            $this->result['message'] = self::MSG_DEL_OK;
        } else {
            $this->result['statusCode'] = self::HTTP_STATUSCODE_INTERNAL_SERVER_ERROR;
            $this->result['message'] = '删除失败';
        }
        return response()->json($this->result);
        
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
