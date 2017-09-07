<?php

namespace App\Http\Controllers;

use App\Http\Requests\CorpRequest;
use App\Models\Corp;
use Illuminate\Support\Facades\Request;

/**
 * 企业
 *
 * Class CorpController
 * @package App\Http\Controllers
 */
class CorpController extends Controller {
    protected $corp;

    function __construct(Corp $corp) {
        $this->corp = $corp;
    }
    
    /**
     * 显示企业列表
     *
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public function index() {

        if (Request::get('draw')) {
            return response()->json($this->corp->datatable());
        }
        return $this->output(__METHOD__);
        
    }
    
    /**
     * 显示创建企业记录的表单
     *
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public function create() {
        
        return $this->output(__METHOD__);
        
    }
    
    /**
     * 保存新创建的企业记录
     *
     * @param CorpRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(CorpRequest $request) {
        
        return $this->corp->create($request->all()) ? $this->succeed() : $this->fail();
        
    }
    
    /**
     * 显示指定的企业记录详情
     *
     * @param $id
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public function show($id) {
        
        $corp = $this->corp->find($id);
        if (!$corp) { return $this->notFound(); }
        return $this->output(__METHOD__, ['corp' => $corp]);
        
    }
    
    /**
     * 显示编辑指定企业记录的表单
     *
     * @param $id
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public function edit($id) {
    
        $corp = $this->corp->find($id);
        if (!$corp) { return $this->notFound(); }
        return $this->output(__METHOD__, ['corp' => $corp]);
        
    }
    
    /**
     * 更新指定的企业记录
     *
     * @param CorpRequest $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(CorpRequest $request, $id) {
        
        $corp = $this->corp->find($id);
        if (!$corp) { return $this->notFound(); }
        return $corp->update($request->all()) ? $this->succeed() : $this->fail();
        
    }
    
    /**
     * 删除指定的企业记录
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id) {
        
        $corp = $this->corp->find($id);
        if (!$corp) { return $this->notFound(); }
        return $corp->delete() ? $this->succeed() : $this->fail();
        
    }
    
}
