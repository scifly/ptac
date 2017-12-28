<?php
namespace App\Http\Controllers;

use App\Http\Requests\IconRequest;
use App\Models\Icon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Request as Request;
use Throwable;

/**
 * 图标
 *
 * Class IconController
 * @package App\Http\Controllers
 */
class IconController extends Controller {
    
    function __construct() {
    
        $this->middleware(['auth', 'checkrole']);
    
    }
    
    /**
     * 图标列表
     *
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function index() {
        
        if (Request::get('draw')) {
            return response()->json(Icon::datatable());
        }
        
        return $this->output();
        
    }
    
    /**
     * 创建图标
     *
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function create() {
        
        return $this->output();
        
    }
    
    /**
     * 保存图标
     *
     * @param IconRequest $request
     * @return JsonResponse
     */
    public function store(IconRequest $request) {
        
        return $this->result(Icon::store($request->all()));
        
    }
    
    /**
     * 图标详情
     *
     * @param $id
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function show($id) {
        
        $icon = Icon::find($id);
        if (!$icon) { return $this->notFound(); }
        
        return $this->output(['icon' => $icon]);
        
    }
    
    /**
     * 编辑图标
     *
     * @param $id
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function edit($id) {
        
        $icon = Icon::find($id);
        if (!$icon) { return $this->notFound(); }
        
        return $this->output(['icon' => $icon]);
        
    }
    
    /**
     * 更新图标
     *
     * @param IconRequest $request
     * @param $id
     * @return JsonResponse
     */
    public function update(IconRequest $request, $id) {
        
        $icon = Icon::find($id);
        if (!$icon) { return $this->notFound(); }
        
        return $this->result($icon->modify($request->all(), $id));
        
    }
    
    /**
     * 删除图标
     *
     * @param $id
     * @return JsonResponse
     * @throws Exception
     */
    public function destroy($id) {
        
        $icon = Icon::find($id);
        if (!$icon) { return $this->notFound(); }
        
        return $this->result($icon->remove($id));
        
    }
    
}
