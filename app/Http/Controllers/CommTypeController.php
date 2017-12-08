<?php
namespace App\Http\Controllers;

use App\Http\Requests\CommTypeRequest;
use App\Models\CommType;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Request;
use Throwable;

/**
 * 通信方式
 *
 * Class CommTypeController
 * @package App\Http\Controllers
 */
class CommTypeController extends Controller {
    
    protected $commType;
    
    function __construct(CommType $commType) {
    
        $this->middleware(['auth']);
        $this->commType = $commType;
        
    }
    
    /**
     * 通信方式列表
     *
     * @return JsonResponse
     * @throws Throwable
     */
    public function index() {
        
        if (Request::get('draw')) {
            return response()->json($this->commType->datatable());
        }
        
        return $this->output(__METHOD__);
        
    }
    
    /**
     * 创建通信方式
     *
     * @return JsonResponse
     * @throws \Throwable
     */
    public function create() {
        
        return $this->output(__METHOD__);
        
    }
    
    /**
     * 保存通信方式
     *
     * @param CommTypeRequest $request
     * @return JsonResponse
     */
    public function store(CommTypeRequest $request) {
        
        return $this->commType->create($request->all())
            ? $this->succeed() : $this->fail();
        
    }
    
    /**
     * 编辑通信方式
     *
     * @param $id
     * @return JsonResponse
     * @throws \Throwable
     */
    public function edit($id) {
        
        $commType = $this->commType->find($id);
        if (!$commType) { return $this->notFound(); }
        
        return $this->output(__METHOD__, ['commType' => $commType]);
        
    }
    
    /**
     * 更新通信方式
     *
     * @param CommTypeRequest $request
     * @param $id
     * @return JsonResponse
     */
    public function update(CommTypeRequest $request, $id) {
        
        $commType = $this->commType->find($id);
        if (!$commType) { return $this->notFound(); }
        
        return $commType->update($request->all())
            ? $this->succeed() : $this->fail();
        
    }
    
    /**
     * 删除通信方式
     *
     * @param $id
     * @return JsonResponse
     * @throws Exception
     */
    public function destroy($id) {
        
        $commType = $this->commType->find($id);
        if (!$commType) { return $this->notFound(); }
        
        return $commType->delete() ? $this->succeed() : $this->fail();
        
    }
    
}
