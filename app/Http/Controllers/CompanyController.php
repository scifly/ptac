<?php
namespace App\Http\Controllers;

use App\Http\Requests\CompanyRequest;
use App\Models\Company;
use Illuminate\Support\Facades\Request;

/**
 * 运营者
 *
 * Class CompanyController
 * @package App\Http\Controllers
 */
class CompanyController extends Controller {
    
    protected $company;
    
    function __construct(Company $company) {
        
        $this->company = $company;
        
    }
    
    /**
     * 运营者公司列表
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
     * 创建运营者公司记录
     *
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public function create() {
        
        return $this->output(__METHOD__);
        
    }
    
    /**
     * 保存运营者公司记录
     *
     * @param CompanyRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(CompanyRequest $request) {
        
        return $this->company->store($request->all(), true)
            ? $this->succeed() : $this->fail();
        
    }
    
    /**
     * 运营者公司记录详情
     *
     * @param $id
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public function show($id) {
        
        $company = $this->company->find($id);
        if (!$company) {
            return $this->notFound();
        }
        
        return $this->output(__METHOD__, ['company' => $company]);
        
    }
    
    /**
     * 编辑运营者公司记录
     *
     * @param $id
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public function edit($id) {
        
        $company = $this->company->find($id);
        if (!$company) {
            return $this->notFound();
        }
        
        return $this->output(__METHOD__, ['company' => $company]);
        
    }
    
    /**
     * 更新运营者公司记录
     *
     * @param CompanyRequest $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(CompanyRequest $request, $id) {
        
        if (!$this->company->find($id)) {
            return $this->notFound();
        }
        
        return $this->company->modify($request->all(), $id, true)
            ? $this->succeed() : $this->fail();
        
    }
    
    /**
     * 删除运营者公司记录
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id) {
        
        if (!$this->company->find($id)) {
            return $this->notFound();
        }
        
        return $this->company->remove($id, true)
            ? $this->succeed() : $this->fail();
        
    }
    
}

