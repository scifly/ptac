<?php

namespace App\Http\Controllers;

use App\Http\Requests\IconRequest;
use App\Models\Icon;
use Illuminate\Support\Facades\Request as Request;

/**
 * 图标
 *
 * Class IconController
 * @package App\Http\Controllers
 */
class IconController extends Controller {
    
    protected $icon;
    
    function __construct(Icon $icon) { $this->icon = $icon; }
    
    /**
     * 图标列表
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
     * 创建图标
     *
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public function create() {
        
        return $this->output(__METHOD__);
        
    }
    
    /**
     * 保存图标
     *
     * @param IconRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(IconRequest $request) {
        
        return $this->icon->create($request->all()) ? $this->succeed() : $this->fail();
        
    }
    
    /**
     * 图标详情
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
     * 编辑图标
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
     * 更新图标
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
     * 删除图标
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
