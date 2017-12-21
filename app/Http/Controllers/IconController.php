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
    
    protected $icon;
    
    function __construct(Icon $icon) {
    
        $this->middleware(['auth']);
        $this->icon = $icon;
    
    }
    
    /**
     * 图标列表
     *
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function index() {
        
        if (Request::get('draw')) {
            return response()->json($this->icon->datatable());
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
        
        return $this->icon->store($request->all())
            ? $this->succeed() : $this->fail();
        
    }
    
    /**
     * 图标详情
     *
     * @param $id
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function show($id) {
        
        $icon = $this->icon->find($id);
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
        
        $icon = $this->icon->find($id);
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
        
        $icon = $this->icon->find($id);
        if (!$icon) { return $this->notFound(); }
        
        return $icon->modify($request->all(), $id)
            ? $this->succeed() : $this->fail();
        
    }
    
    /**
     * 删除图标
     *
     * @param $id
     * @return JsonResponse
     * @throws Exception
     */
    public function destroy($id) {
        
        $icon = $this->icon->find($id);
        if (!$icon) { return $this->notFound(); }
        
        return $icon->remove($id)
            ? $this->succeed() : $this->fail();
        
    }
    
}
