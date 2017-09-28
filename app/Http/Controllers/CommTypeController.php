<?php
namespace App\Http\Controllers;

use App\Http\Requests\CommTypeRequest;
use App\Models\CommType;
use Illuminate\Support\Facades\Request;

/**
 * 通信方式
 *
 * Class CommTypeController
 * @package App\Http\Controllers
 */
class CommTypeController extends Controller {
    
    protected $commType;
    
    function __construct(CommType $commType) {
        
        $this->commType = $commType;
        
    }
    
    /**
     * 通信方式列表
     *
     * @return \Illuminate\Http\JsonResponse
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
     * @return \Illuminate\Http\JsonResponse
     */
    public function create() {
        
        return $this->output(__METHOD__);
        
    }
    
    /**
     * 保存通信方式
     *
     * @param CommTypeRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(CommTypeRequest $request) {
        
        return $this->commType->create($request->all()) ? $this->succeed() : $this->fail();
        
    }
    
    /**
     * 编辑通信方式
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit($id) {
        
        $commType = $this->commType->find($id);
        if (!$commType) {
            return $this->notFound();
        }
        
        return $this->output(__METHOD__, ['commType' => $commType]);
        
    }
    
    /**
     * 更新通信方式
     *
     * @param CommTypeRequest $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(CommTypeRequest $request, $id) {
        
        $commType = $this->commType->find($id);
        if (!$commType) {
            return $this->notFound();
        }
        
        return $commType->update($request->all()) ? $this->succeed() : $this->fail();
        
    }
    
    /**
     * 删除通信方式
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id) {
        
        $commType = $this->commType->find($id);
        if (!$commType) {
            return $this->notFound();
        }
        
        return $commType->delete() ? $this->succeed() : $this->fail();
        
    }
}
