<?php
namespace App\Http\Controllers;

use App\Http\Requests\SchoolTypeRequest;
use App\Models\SchoolType;
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
        // $this->middleware(['auth', 'checkRole']);
        $this->schoolType = $schoolType;
        
    }
    
    /**
     * 学校类型列表
     *
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public function index() {
        if (Request::get('draw')) {
            return response()->json($this->schoolType->datatable());
        }
        
        return parent::output(__METHOD__);
        
    }
    
    /**
     * 创建学校类型
     *
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public function create() {
        return parent::output(__METHOD__);
        
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
     * 学校类型详情
     *
     * @param $id
     * @internal param SchoolType $schoolType
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public function show($id) {
        $schoolType = $this->schoolType->find($id);
        if (!$schoolType) {
            return parent::notFound();
        }
        
        return parent::output(__METHOD__, ['schoolType' => $schoolType]);
        
    }
    
    /**
     * 编辑学校类型
     *
     * @param $id
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public function edit($id) {
        $schoolType = $this->schoolType->find($id);
        if (!$schoolType) {
            return parent::notFound();
        }
        
        return parent::output(__METHOD__, ['schoolType' => $schoolType]);
        
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
     */
    public function destroy($id) {
        $schoolType = $this->schoolType->find($id);
        if (!$schoolType) {
            return parent::notFound();
        }
        
        return $schoolType->delete() ? parent::succeed() : parent::fail();
        
    }
    
}
