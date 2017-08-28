<?php

namespace App\Http\Controllers;

use App\Http\Requests\CompanyRequest;
use App\Models\Company;
use Illuminate\Support\Facades\Request;

class CompanyController extends Controller {
    
    protected $company;
    
    function __construct(Company $company) {
        
        $this->company = $company;
        
    }
    
    /**
     * 显示运营者公司列表
     *
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public function index() {
        
        if (Request::get('draw')) {
            return response()->json($this->company->datatable());
        }
        return $this->output(__METHOD__);
        
    }
    
    /**
     * 显示创建运营者公司记录的表单
     *
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public function create() {
        
        return $this->output(__METHOD__);
        
    }
    
    /**
     * 保存新创建的运营者公司记录
     *
     * @param CompanyRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(CompanyRequest $request) {
        
        if ($this->company->existed($request)) {
            return $this->fail('已经有此记录');
        }
        return $this->company->create($request->all()) ? $this->succeed() : $this->fail();
        
    }
    
    /**
     * 显示指定的运营者公司记录详情
     *
     * @param $id
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public function show($id) {
        
        $company = $this->company->find($id);
        if (!$company) { return $this->notFound(); }
        return $this->output(__METHOD__, ['company' => $company]);
        
    }
    
    /**
     * 显示编辑指定运营者公司记录的表单
     *
     * @param $id
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public function edit($id) {
        
        $company = $this->company->find($id);
        if (!$company) { return $this->notFound(); }
        return $this->output(__METHOD__, ['company' => $company]);
        
    }
    
    /**
     * 更新指定的运营者公司记录
     *
     * @param CompanyRequest $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(CompanyRequest $request, $id) {
        
        $company = $this->company->find($id);
        if (!$company) { return $this->notFound(); }
        if ($this->company->existed($request,$id)) {
            return $this->fail('已经有此记录');
        }
        return $company->update($request->all()) ? $this->succeed() : $this->fail();
        
    }
    
    /**
     * 删除指定的运营者公司记录
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id) {
        
        $company = $this->company->find($id);
        if (!$company) { return $this->notFound(); }
        return $company->delete() ? $this->succeed() : $this->fail();
        
    }
}

