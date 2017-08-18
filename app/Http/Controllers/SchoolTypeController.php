<?php

namespace App\Http\Controllers;

use App\Http\Requests\SchoolTypeRequest;
use App\Models\SchoolType;
use Illuminate\Support\Facades\Request;

class SchoolTypeController extends Controller {
    
    protected $schoolType;
    
    function __construct(SchoolType $schoolType) {
        $this->schoolType = $schoolType;
    }
    
    /**
     * 显示学校类型列表
     *
     * @return \Illuminate\Http\Response
     * @internal param SchoolType $schoolType
     */
    public function index() {
        
        if (Request::get('draw')) {
            return response()->json($this->schoolType->datatable());
        }
        return parent::output(__METHOD__);
        
    }
    
    /**
     * 返回创建学校类型的表单
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        
        return parent::output(__METHOD__);
        
    }
    
    /**
     * 保存新创建的学校类型记录
     *
     * @param SchoolTypeRequest|\Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(SchoolTypeRequest $request) {
        
        return $this->schoolType->create($request->all()) ? parent::succeed() : parent::fail();
        
    }
    
    /**
     * 显示指定的学校类型记录详情
     *
     * @param $id
     * @internal param SchoolType $schoolType
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public function show($id) {
        
        $schoolType = $this->schoolType->find($id);
        if (!$schoolType) { return parent::notFound(); }
        return parent::output(__METHOD__, ['schoolType' => $schoolType]);
        
    }
    
    /**
     * 显示编辑学校类型记录的表单
     *
     * @param $id
     * @internal param $ \Ap p\Models\SchoolType $schoolType
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public function edit($id) {
        
        $schoolType = $this->schoolType->find($id);
        if (!$schoolType) { return parent::notFound(); }
        return parent::output(__METHOD__, ['schoolType' => $schoolType]);
        
    }
    
    /**
     * 更新指定的学校类型记录
     *
     * @param SchoolTypeRequest $request
     * @param $id
     * @internal param \Illuminate\Http\Request $request
     * @internal param SchoolType $schoolType
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(SchoolTypeRequest $request, $id) {
    
        $schoolType = $this->schoolType->find($id);
        if (!$schoolType) { return parent::notFound(); }
        return $schoolType->update($request->all()) ? parent::succeed() : parent::fail();
        
    }
    
    /**
     * Remove the specified resource from storage.
     *
     * @param $id
     * @return \Illuminate\Http\Response
     * @internal param SchoolType $schoolType
     */
    public function destroy($id) {

        $schoolType = $this->schoolType->find($id);
        if (!$schoolType) { return parent::notFound(); }
        return $schoolType->delete() ? parent::succeed() : parent::fail();
        
    }
}
