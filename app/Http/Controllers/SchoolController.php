<?php

namespace App\Http\Controllers;

use App\Http\Requests\SchoolRequest;
use App\Models\School as School;
use Illuminate\Support\Facades\Request;

class SchoolController extends Controller {
    
    protected $school;
    
    function __construct(School $school) { $this->school = $school; }
    
    /**
     * 显示学校记录列表
     *
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public function index() {
    
        if (Request::get('draw')) {
            return response()->json($this->school->datatable());
        }
        return parent::output(__METHOD__);
    
    }
    
    /**
     * 显示创建学校记录的表单
     *
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public function create() {
        
        return parent::output(__METHOD__);
        
    }
    
    /**
     * 保存新创建的学校记录
     *
     * @param SchoolRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(SchoolRequest $request) {
    
        return $this->school->create($request->all()) ? parent::succeed() : parent::fail();
    
    }
    
    /**
     * 显示指定的学校记录详情
     *
     * @param $id
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public function show($id) {
    
        $school = $this->school->find($id);
        if (!$school) { return parent::notFound(); }
        return parent::output(__METHOD__);
    
    }
    
    /**
     * 显示编辑指定学校记录的表单
     *
     * @param $id
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public function edit($id) {
        
        $school = $this->school->find($id);
        if (!$school) { return parent::notFound(); }
        return parent::output(__METHOD__, ['school' => $school]);
        
    }
    
    /**
     * 更新指定的学校记录
     *
     * @param SchoolRequest|\Illuminate\Http\Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(SchoolRequest $request, $id) {
        
        $school = $this->school->find($id);
        if (!$school) { return parent::notFound(); }
        return $school->update($request->all()) ? parent::succeed() : parent::fail();
        
    }
    
    /**
     * 删除指定的学校记录
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id) {
        
        $school = $this->school->find($id);
        if (!$school) { return parent::notFound(); }
        return $school->delete() ? parent::succeed() : parent::fail();
        
    }
}
