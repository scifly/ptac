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
            $menus = $this->menu->get(['id', 'parent_id', 'name']);
            $data = [];
            foreach ($menus as $menu) {
                $parentId = isset($menu->parent_id) ? $menu->parent_id : '#';
                $data[] = [
                    'id' => $menu->id,
                    'parent' => $parentId,
                    'text' => $menu->name
                ];
            }
            return response()->json($data);
        }
        return view('menu.index');
    
    }
    
    /**
     * 显示创建菜单记录的表单
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create() {
        
        if (Request::ajax()) {
            return response()->json([
                'html' => view('menu.create')->render()
            ]);
        }
        return view('menu.create', [
            'js' => 'js/action/create.js',
            'form' => true
        ]);
        
    }
    
    /**
     * 保存新创建的菜单记录
     *
     * @param MenuRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(MenuRequest $request) {
    
        if ($this->menu->create($request->all())) {
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
     */
    public function edit($id) {
        
        if (Request::ajax()) {
            return response()->json([
                'html' => view('menu.edit')->render()
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
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(MenuRequest $request, $id) {
        
        $this->menu->findOrFail($id)->update($request->all());
        $this->result['message'] = self::MSG_EDIT_OK;
        
        return response()->json($this->result);
        
    }
    
    /**
     * 删除指定的菜单记录
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id) {
    
        if ($this->menu->findOrFail($id)->delete()) {
            $this->result['statusCode'] = self::HTTP_STATUSCODE_OK;
            $this->result['message'] = self::MSG_DEL_OK;
        } else {
            $this->result['statusCode'] = self::HTTP_STATUSCODE_INTERNAL_SERVER_ERROR;
            $this->result['message'] = '';
        }
        return response()->json($this->result);
        
    }
    
}
