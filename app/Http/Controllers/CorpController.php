<?php
namespace App\Http\Controllers;

use App\Http\Requests\CorpRequest;
use App\Models\Corp;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Request;
use Throwable;

/**
 * 企业
 *
 * Class CorpController
 * @package App\Http\Controllers
 */
class CorpController extends Controller {
    
    protected $corp;
    
    function __construct(Corp $corp) {
        
        $this->middleware(['auth']);
        $this->corp = $corp;
        
    }
    
    /**
     * 企业列表
     *
     * @return bool|JsonResponse
     * @throws Throwable
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
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function create() {
        
        return $this->output(__METHOD__);
        
    }
    
    /**
     * 保存企业
     *
     * @param CorpRequest $request
     * @return JsonResponse
     */
    public function store(CorpRequest $request) {
        
        return $this->corp->store($request->all(), true)
            ? $this->succeed() : $this->fail();
        
    }
    
    /**
     * 编辑企业
     *
     * @param $id
     * @return bool|JsonResponse
     * @throws Throwable
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
     * @return JsonResponse
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
     * @return JsonResponse
     * @throws Exception
     */
    public function destroy($id) {
        
        if (!$this->corp->find($id)) { return $this->notFound(); }
        
        return $this->corp->remove($id, true)
            ? $this->succeed() : $this->fail();
        
    }
    
}
