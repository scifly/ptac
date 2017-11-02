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
    
    function __construct(Corp $corp) { $this->corp = $corp; }
    
    /**
     * 企业列表
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
     * 创建企业
     *
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public function create() {
        
        return $this->output(__METHOD__);
        
    }
    
    /**
     * 保存企业
     *
     * @param CorpRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(CorpRequest $request) {
        
        return $this->corp->store($request->all(), true)
            ? $this->succeed() : $this->fail();
        
    }
    
    /**
     * 企业详情
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
     * 编辑企业
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
     * 更新企业
     *
     * @param CorpRequest $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(CorpRequest $request, $id) {
        
        if (!$this->corp->find($id)) { return $this->notFound(); }
        return $this->corp->modify($request->all(), $id, true)
            ? $this->succeed() : $this->fail();
        
    }
    
    /**
     * 删除企业
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id) {
        
        if (!$this->corp->find($id)) { return $this->notFound(); }
        return $this->corp->remove($id, true)
            ? $this->succeed() : $this->fail();
        
    }
    
}
