<?php
namespace App\Http\Controllers;

use App\Http\Requests\CommTypeRequest;
use App\Models\ComboType;
use Illuminate\Support\Facades\Request;

/**
 * 套餐类型
 *
 * Class ComboTypeController
 * @package App\Http\Controllers
 */
class ComboTypeController extends Controller {
    
    protected $comboType;
    
    function __construct(ComboType $comboType) {
        
        $this->comboType = $comboType;
        
    }
    
    /**
     * 套餐类型列表
     *
     * @return \Illuminate\Http\JsonResponse
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
     * @return \Illuminate\Http\JsonResponse
     */
    public function create() {
        
        return $this->output(__METHOD__);
        
    }
    
    /**
     * 保存套餐类型
     *
     * @param CommTypeRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(CommTypeRequest $request) {
        
        return $this->comboType->create($request->all()) ? $this->succeed() : $this->fail();
        
    }
    
    /**
     * 编辑套餐类型
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit($id) {
        
        $comboType = $this->comboType->find($id);
        if (!$comboType) {
            return $this->notFound();
        }
        
        return $this->output(__METHOD__, ['comboType' => $comboType]);
        
    }
    
    /**
     * 更新套餐类型
     *
     * @param CommTypeRequest $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(CommTypeRequest $request, $id) {
        
        $comboType = $this->comboType->find($id);
        if (!$comboType) {
            return $this->notFound();
        }
        
        return $comboType->update($request->all()) ? $this->succeed() : $this->fail();
        
    }
    
    /**
     * 删除套餐类型
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id) {
        
        $comboType = $this->comboType->find($id);
        if (!$comboType) {
            return $this->notFound();
        }
        
        return $comboType->delete() ? $this->succeed() : $this->fail();
        
    }
    
}
