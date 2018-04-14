<?php
namespace App\Http\Controllers;

use App\Http\Requests\CompanyRequest;
use App\Models\Company;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Request;
use Throwable;

/**
 * 运营者
 *
 * Class CompanyController
 * @package App\Http\Controllers
 */
class CompanyController extends Controller {
    
    protected $company;
    
    /**
     * CompanyController constructor.
     * @param Company $company
     */
    function __construct(Company $company) {
        
        $this->middleware(['auth', 'checkrole']);
        $this->middleware(function ($request, $next) use ($company) {
            $this->company = $company;
            return $this->approve($request, $next, $company);
        });
    
    }
    
    /**
     * 运营者公司列表
     *
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function index() {
        
        if (Request::get('draw')) {
            return response()->json(
                $this->company->datatable()
            );
        }
        
        return $this->output();
        
    }
    
    /**
     * 创建运营者公司记录
     *
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function create() {
        
        return $this->output();
        
    }
    
    /**
     * 保存运营者公司记录
     *
     * @param CompanyRequest $request
     * @return JsonResponse
     */
    public function store(CompanyRequest $request) {
        
        return $this->result(
            $this->company->store(
                $request->all(), true
            )
        );
        
    }
    
    /**
     * 编辑运营者公司记录
     *
     * @param $id
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function edit($id) {
        
        return $this->output([
            'company' => $this->company->find($id),
        ]);
        
    }
    
    /**
     * 更新运营者公司记录
     *
     * @param CompanyRequest $request
     * @param $id
     * @return JsonResponse
     */
    public function update(CompanyRequest $request, $id) {
        
        return $this->result(
            $this->company->modify(
                $request->all(), $id, true
            )
        );
        
    }
    
    /**
     * 删除运营者公司记录
     *
     * @param $id
     * @return JsonResponse
     * @throws Exception
     */
    public function destroy($id) {
        
        return $this->result(
            $this->company->remove($id, true)
        );
        
    }
    
}