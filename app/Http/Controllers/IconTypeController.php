<?php

namespace App\Http\Controllers;

use App\Http\Requests\IconTypeRequest;
use App\Models\IconType;
use Illuminate\Support\Facades\Request as Request;

class IconTypeController extends Controller {
    
    protected $iconType;
    
    function __construct(IconType $iconType) { $this->iconType = $iconType; }
    
    /**
     * 显示图标类型列表
     *
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public function index() {
        
        if (Request::get('draw')) {
            return response()->json($this->iconType->datatable());
        }
        return $this->output(__METHOD__);
        
    }
    
    /**
     * 显示创建图表类型记录的表单
     *
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public function create() {
        
        return $this->output(__METHOD__);
        
    }
    
    /**
     * 保存新创建的图标类型记录
     *
     * @param IconTypeRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(IconTypeRequest $request) {
        
        return $this->iconType->create($request->all()) ? $this->succeed() : $this->fail();
        
    }
    
    /**
     * 显示指定图表类型记录的详情
     *
     * @param $id
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public function show($id) {
        
        $iconType = $this->iconType->find($id);
        if (!$iconType) { return $this->notFound(); }
        return $this->output(__METHOD__, ['iconType' => $iconType]);
        
    }
    
    /**
     * 显示编辑指定图标记录的表单
     *
     * @param $id
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public function edit($id) {
    
        $iconType = $this->iconType->find($id);
        if (!$iconType) { return $this->notFound(); }
        return $this->output(__METHOD__, ['iconType' => $iconType]);
        
    }
    
    /**
     * 更新指定的图标类型记录
     *
     * @param IconTypeRequest $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(IconTypeRequest $request, $id) {
    
        $iconType = $this->iconType->find($id);
        if (!$iconType) { return $this->notFound(); }
        return $iconType->update($request->all()) ? $this->succeed() : $this->fail();
        
    }
    
    /**
     * 删除指定的图标类型记录
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id) {
    
        $iconType = $this->iconType->find($id);
        if (!$iconType) { return $this->notFound(); }
        return $iconType->delete() ? $this->succeed() : $this->fail();
    
    }
    
}
