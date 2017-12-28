<?php
namespace App\Http\Controllers;

use App\Http\Requests\IconTypeRequest;
use App\Models\Icon;
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
    
    function __construct() {
    
        $this->middleware(['auth']);
    
    }
    
    /**
     * 图标类型列表
     *
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function index() {
        
        if (Request::get('draw')) {
            return response()->json(IconType::datatable());
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
        
        return $this->result(IconType::store($request->all()));
        
    }
    
    /**
     * 编辑图标类型
     *
     * @param $id
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function edit($id) {
        
        $iconType = IconType::find($id);
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
        
        $iconType = IconType::find($id);
        if (!$iconType) { return $this->notFound(); }
        
        return $this->result($iconType->modify($request->all(), $id));
        
    }
    
    /**
     * 删除图标类型
     *
     * @param $id
     * @return JsonResponse
     * @throws Exception
     */
    public function destroy($id) {
        
        $iconType = IconType::find($id);
        if (!$iconType) { return $this->notFound(); }
        
        return $this->result($iconType->remove($id));
        
    }
    
}
