<?php
namespace App\Http\Controllers;

use App\Http\Requests\AlertTypeRequest;
use App\Models\AlertType;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Request;
use Throwable;

/**
 * 警告类型
 *
 * Class AlertTypeController
 * @package App\Http\Controllers
 */
class AlertTypeController extends Controller {
    
    protected $alertType;
    
    function __construct(AlertType $alertType) {
    
        $this->middleware(['auth']);
        $this->alertType = $alertType;
        
    }
    
    /**
     * 警告类型列表
     *
     * @return JsonResponse
     * @throws Throwable
     */
    public function index() {
        
        if (Request::get('draw')) {
            return response()->json($this->alertType->datatable());
        }
        
        return $this->output();
        
    }
    
    /**
     * 创建警告类型
     *
     * @return JsonResponse
     * @throws Throwable
     */
    public function create() {
        
        return $this->output();
        
    }
    
    /**
     * 保存警告类型
     *
     * @param AlertTypeRequest $request
     * @return JsonResponse
     */
    public function store(AlertTypeRequest $request) {
        
        return $this->alertType->create($request->all())
            ? $this->succeed() : $this->fail();
        
    }
    
    /**
     * 编辑警告类型
     *
     * @param $id
     * @return JsonResponse
     * @throws Throwable
     */
    public function edit($id) {
        
        $alertType = $this->alertType->find($id);
        if (!$alertType) { return $this->notFound(); }
        
        return $this->output(['alertType' => $alertType]);
        
    }
    
    /**
     * 更新警告类型
     *
     * @param AlertTypeRequest $request
     * @param $id
     * @return bool|JsonResponse
     */
    public function update(AlertTypeRequest $request, $id) {
        
        $alertType = $this->alertType->find($id);
        if (!$alertType) { return $this->notFound(); }
        
        return $alertType->update($request->all());
        
    }
    
    /**
     * 删除警告类型
     *
     * @param $id
     * @return JsonResponse
     * @throws Exception
     */
    public function destroy($id) {
        
        $alertType = $this->alertType->find($id);
        if (!$alertType) { return $this->notFound(); }
        
        return $alertType->delete() ? $this->succeed() : $this->fail();
        
    }
    
}
