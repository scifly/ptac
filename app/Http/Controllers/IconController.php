<?php

namespace App\Http\Controllers;

use App\Http\Requests\IconRequest;
use App\Models\Icon;
use Illuminate\Support\Facades\Request as Request;

class IconController extends Controller {
    
    protected $icon;
    
    function __construct(Icon $icon) { $this->icon = $icon; }
    
    /**
     * 显示图标列表
     *
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public function index() {
        
        if (Request::get('draw')) {
            return response()->json($this->icon->datatable());
        }
        return $this->output(__METHOD__);
        
    }
    
    /**
     * 显示创建图标记录的表单
     *
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public function create() {
        
        return $this->output(__METHOD__);
        
    }
    
    /**
     * 保存新创建的图标记录
     *
     * @param IconRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(IconRequest $request) {
        
        return $this->icon->create($request->all()) ? $this->succeed() : $this->fail();
        
    }
    
    /**
     * 显示指定图标记录的详情
     *
     * @param $id
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public function show($id) {

        $icon = $this->icon->find($id);
        if (!$icon) { return $this->notFound(); }
        return $this->output(__METHOD__, ['icon' => $icon]);
        
    }
    
    /**
     * 显示编辑指定图标记录的表单
     *
     * @param $id
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public function edit($id) {
    
        $icon = $this->icon->find($id);
        if (!$icon) { return $this->notFound(); }
        return $this->output(__METHOD__, ['icon' => $icon]);
        
    }
    
    /**
     * 更新指定的图标记录
     *
     * @param IconRequest $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(IconRequest $request, $id) {
    
        $icon = $this->icon->find($id);
        if (!$icon) { return $this->notFound(); }
        return $icon->update($request->all()) ? $this->succeed() : $this->fail();
    
    }
    
    /**
     * 删除指定的图标记录
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id) {
    
        $icon = $this->icon->find($id);
        if (!$icon) { return $this->notFound(); }
        return $icon->delete() ? $this->succeed() : $this->fail();
        
    }
}
