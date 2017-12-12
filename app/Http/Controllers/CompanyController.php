<?php
namespace App\Http\Controllers;

use App\Http\Requests\CompanyRequest;
use App\Models\Company;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Pusher\Pusher;
use Throwable;

/**
 * 运营者
 *
 * Class CompanyController
 * @package App\Http\Controllers
 */
class CompanyController extends Controller {
    
    protected $company;
    
    function __construct(Company $company) {
    
        $this->middleware(['auth']);
        $this->company = $company;
        
    }
    
    /**
     * 运营者公司列表
     *
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function index() {

        $pusher = new Pusher(
            env('PUSHER_APP_KEY'),
            env('PUSHER_APP_SECRET'),
            env('PUSHER_APP_ID'),
            ['cluster' => 'ap1', 'encrypted' => true]
        );
//        $pusher->socket_auth('user.' . Auth::id(), '5686.5060336');
        $pusher->trigger(
            'user.' . Auth::id(),
            'App\Events\eventTrigger',
            'test'
        );
        exit;
        if (Request::get('draw')) {
            return response()->json($this->company->datatable());
        }
        
        return $this->output(__METHOD__);
        
    }
    
    /**
     * 创建运营者公司记录
     *
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function create() {
        
        return $this->output(__METHOD__);
        
    }
    
    /**
     * 保存运营者公司记录
     *
     * @param CompanyRequest $request
     * @return JsonResponse
     */
    public function store(CompanyRequest $request) {
        
        return $this->company->store($request->all(), true)
            ? $this->succeed() : $this->fail();
        
    }
    
    /**
     * 编辑运营者公司记录
     *
     * @param $id
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function edit($id) {
        
        $company = $this->company->find($id);
        if (!$company) { return $this->notFound(); }
        
        return $this->output(__METHOD__, ['company' => $company]);
        
    }
    
    /**
     * 更新运营者公司记录
     *
     * @param CompanyRequest $request
     * @param $id
     * @return JsonResponse
     */
    public function update(CompanyRequest $request, $id) {
        
        $company = $this->company->find($id);
        if (!$company) { return $this->notFound(); }
        
        return $company->modify($request->all(), $id, true)
            ? $this->succeed() : $this->fail();
        
    }
    
    /**
     * 删除运营者公司记录
     *
     * @param $id
     * @return JsonResponse
     * @throws Exception
     */
    public function destroy($id) {
    
        $company = $this->company->find($id);
        if (!$company) { return $this->notFound(); }
        
        return $company->remove($id, true)
            ? $this->succeed() : $this->fail();
        
    }
    
}

