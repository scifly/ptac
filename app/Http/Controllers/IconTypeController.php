<?php
namespace App\Http\Controllers;

use App\Http\Requests\IconTypeRequest;
use App\Models\IconType;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Request as Request;
use Throwable;

/**
 * 图标类型
 *
 * Class IconTypeController
 * @package App\Http\Controllers
 */
class IconTypeController extends Controller {
    
    protected $iconType;
    
    function __construct(IconType $iconType) {
    
        $this->middleware(['auth']);
        $this->iconType = $iconType;
    
    }
    
    /**
     * 图标类型列表
     *
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function index() {
        
        if (Request::get('draw')) {
            return response()->json($this->iconType->datatable());
        }
        
        return $this->output();
        
    }
    
    /**
     * 创建图标类型
     *
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function create() {
        
        return $this->output();
        
    }
    
    /**
     * 保存图标类型
     *
     * @param IconTypeRequest $request
     * @return JsonResponse
     */
    public function store(IconTypeRequest $request) {
        
        return $this->iconType->store($request->all())
            ? $this->succeed() : $this->fail();
        
    }
    
    /**
     * 编辑图标类型
     *
     * @param $id
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function edit($id) {
        
        $iconType = $this->iconType->find($id);
        if (!$iconType) { return $this->notFound(); }
        
        return $this->output(['iconType' => $iconType]);
        
    }
    
    /**
     * 更新图标类型
     *
     * @param IconTypeRequest $request
     * @param $id
     * @return JsonResponse
     */
    public function update(IconTypeRequest $request, $id) {
        
        $iconType = $this->iconType->find($id);
        if (!$iconType) { return $this->notFound(); }
        
        return $iconType->modify($request->all(), $id)
            ? $this->succeed() : $this->fail();
        
    }
    
    /**
     * 删除图标类型
     *
     * @param $id
     * @return JsonResponse
     * @throws Exception
     */
    public function destroy($id) {
        
        $iconType = $this->iconType->find($id);
        if (!$iconType) { return $this->notFound(); }
        
        return $iconType->remove($id) ? $this->succeed() : $this->fail();
        
    }
    
}
