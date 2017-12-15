<?php
namespace App\Http\Controllers;

use App\Http\Requests\ComboTypeRequest;
use App\Http\Requests\CommTypeRequest;
use App\Models\ComboType;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Request;
use Throwable;

/**
 * 套餐类型
 *
 * Class ComboTypeController
 * @package App\Http\Controllers
 */
class ComboTypeController extends Controller {
    
    protected $comboType;
    
    function __construct(ComboType $comboType) {
    
        $this->middleware(['auth']);
        $this->comboType = $comboType;
        
    }
    
    /**
     * 套餐类型列表
     *
     * @return JsonResponse
     * @throws Throwable
     */
    public function index() {
        
        if (Request::get('draw')) {
            return response()->json($this->comboType->datatable());
        }
        
        return $this->output(__METHOD__);
        
    }
    
    /**
     * 创建套餐类型
     *
     * @return JsonResponse
     * @throws Throwable
     */
    public function create() {
        
        return $this->output(__METHOD__);
        
    }
    
    /**
     * 保存套餐类型
     *
     * @param ComboTypeRequest $request
     * @return JsonResponse
     */
    public function store(ComboTypeRequest $request) {
        
        return $this->comboType->create($request->all())
            ? $this->succeed() : $this->fail();
        
    }
    
    /**
     * 编辑套餐类型
     *
     * @param $id
     * @return JsonResponse
     * @throws Throwable
     */
    public function edit($id) {
        
        $comboType = $this->comboType->find($id);
        if (!$comboType) { return $this->notFound(); }
        
        return $this->output(__METHOD__, ['comboType' => $comboType]);
        
    }
    
    /**
     * 更新套餐类型
     *
     * @param ComboTypeRequest $request
     * @param $id
     * @return JsonResponse
     */
    public function update(ComboTypeRequest $request, $id) {
        
        $comboType = $this->comboType->find($id);
        if (!$comboType) { return $this->notFound(); }
        
        return $comboType->update($request->all())
            ? $this->succeed() : $this->fail();
        
    }
    
    /**
     * 删除套餐类型
     *
     * @param $id
     * @return JsonResponse
     * @throws Exception
     */
    public function destroy($id) {
        
        $comboType = $this->comboType->find($id);
        if (!$comboType) { return $this->notFound(); }
        
        return $comboType->delete()
            ? $this->succeed() : $this->fail();
        
    }
    
}
