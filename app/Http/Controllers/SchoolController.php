<?php

namespace App\Http\Controllers;

use App\Http\Requests\SchoolRequest;
use App\Models\School as School;
use Illuminate\Support\Facades\Request;

/**
 * 学校
 *
 * Class SchoolController
 * @package App\Http\Controllers
 */
class SchoolController extends Controller {
    
    protected $school;
    
    function __construct(School $school) { $this->school = $school; }
    
    /**
     * 学校列表
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
     * 创建学校
     *
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public function create() {
        
        return parent::output(__METHOD__);
        
    }
    
    /**
     * 保存学校
     *
     * @param SchoolRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(SchoolRequest $request) {
    
        return $this->school->create($request->all()) ? parent::succeed() : parent::fail();
    
    }
    
    /**
     * 学校详情
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
     * 编辑学校
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
     * 更新学校
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
     * 删除学校
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
