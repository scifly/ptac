<?php
namespace App\Http\Controllers;

use App\Http\Requests\SchoolTypeRequest;
use App\Models\SchoolType;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Request;

/**
 * 学校类型
 *
 * Class SchoolTypeController
 * @package App\Http\Controllers
 */
class SchoolTypeController extends Controller {
    
    protected $schoolType;
    
    function __construct(SchoolType $schoolType) {
    
        $this->middleware(['auth']);
        $this->schoolType = $schoolType;
        
    }
    
    /**
     * 学校类型列表
     *
     * @return bool|JsonResponse
     * @throws \Throwable
     */
    public function index() {
        
        if (Request::get('draw')) {
            return response()->json($this->schoolType->datatable());
        }
        
        return $this->output();
        
    }
    
    /**
     * 创建学校类型
     *
     * @return bool|\Illuminate\Http\JsonResponse
     * @throws \Throwable
     */
    public function create() {
        
        return $this->output();
        
    }
    
    /**
     * 保存学校类型
     *
     * @param SchoolTypeRequest|\Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(SchoolTypeRequest $request) {
        
        return $this->schoolType->create($request->all())
            ? parent::succeed() : parent::fail();
        
    }
    
    /**
     * 编辑学校类型
     *
     * @param $id
     * @return bool|\Illuminate\Http\JsonResponse
     * @throws \Throwable
     */
    public function edit($id) {
        
        $schoolType = $this->schoolType->find($id);
        if (!$schoolType) {
            return parent::notFound();
        }
        
        return $this->output(['schoolType' => $schoolType]);
        
    }
    
    /**
     * 更新学校类型
     *
     * @param SchoolTypeRequest $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(SchoolTypeRequest $request, $id) {
        
        $schoolType = $this->schoolType->find($id);
        if (!$schoolType) {
            return parent::notFound();
        }
        
        return $schoolType->update($request->all()) ? parent::succeed() : parent::fail();
        
    }
    
    /**
     * 删除学校类型
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     * @throws Exception
     */
    public function destroy($id) {
        
        $schoolType = $this->schoolType->find($id);
        if (!$schoolType) { return parent::notFound(); }
        
        return $schoolType->delete() ? parent::succeed() : parent::fail();
        
    }
    
}
