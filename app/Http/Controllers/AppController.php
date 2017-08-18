<?php

namespace App\Http\Controllers;

use App\Http\Requests\AppRequest;
use App\Models\App;
use Illuminate\Support\Facades\Request;

class AppController extends Controller {
    
    protected $app;
    
    function __construct(App $app) { $this->app = $app; }
    
    /**
     * 显示微信应用记录列表
     *
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public function index() {
        
        if (Request::get('draw')) {
            return response()->json($this->app->datatable());
        }
        return $this->output(__METHOD__);
        
    }
    
    /**
     * 显示创建微信应用记录的表单
     *
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public function create() {
        
        return $this->output(__METHOD__);
        
    }
    
    /**
     * 保存新创建的应用记录
     *
     * @param AppRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(AppRequest $request) {
        
        return $this->app->create($request->all()) ? $this->succeed() : $this->fail();

    }
    
    /**
     * 显示指定的微信应用记录详情
     *
     * @param $id
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public function show($id) {

        $app = $this->app->find($id);
        if (!$app) { return $this->notFound(); }
        return $this->output(__METHOD__, ['app' => $app]);
        
    }
    
    /**
     * 显示编辑指定微信应用记录的表单
     *
     * @param $id
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public function edit($id) {
        
        $app = $this->app->find($id);
        if (!$app) { return $this->notFound(); }
        return $this->output(__METHOD__, ['app' => $app]);
        
    }
    
    /**
     * 更新指定的微信应用记录
     *
     * @param AppRequest $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(AppRequest $request, $id) {
        
        $app = $this->app->find($id);
        if (!$app) { return $this->notFound(); }
        return $app->update($request->all()) ? $this->succeed() : $this->fail();
        
    }
    
    /**
     * 删除指定的微信应用记录
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id) {

        $app = $this->app->find($id);
        if (!$app) { return $this->notFound(); }
        return $app->delete() ? $this->succeed() : $this->fail();
        
    }
    
}
