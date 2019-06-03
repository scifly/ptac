<?php
namespace App\Http\Controllers;

use App\Http\Requests\CustodianRequest;
use App\Models\Custodian;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Request;
use Throwable;

/**
 * 监护人
 *
 * Class CustodianController
 * @package App\Http\Controllers
 */
class CustodianController extends Controller {
    
    protected $custodian;
    
    /**
     * CustodianController constructor.
     * @param Custodian $custodian
     */
    function __construct(Custodian $custodian) {
        
        $this->middleware(['auth', 'checkrole']);
        $this->custodian = $custodian;
        Request::has('ids') ?: $this->approve($custodian);
        
    }
    
    /**
     * 监护人列表
     *
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function index() {
        
        return Request::get('draw')
            ? response()->json($this->custodian->index())
            : $this->output();
    }
    
    /**
     * 创建监护人
     *
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function create() {
        
        return Request::method() === 'POST'
            ? $this->custodian->csList()
            : $this->output();
        
    }
    
    /**
     * 保存监护人
     *
     * @param CustodianRequest $request
     * @return JsonResponse
     * @throws Exception
     * @throws Throwable
     */
    public function store(CustodianRequest $request) {
        
        return $this->result(
            $this->custodian->store(
                $request->all()
            )
        );
        
    }
    
    /**
     * 编辑监护人
     *
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function edit() {
        
        return Request::method() === 'POST'
            ? $this->custodian->csList()
            : $this->output();
        
    }
    
    /**
     * 更新监护人
     *
     * @param CustodianRequest $request
     * @param $id
     * @return JsonResponse
     * @throws Throwable
     */
    public function update(CustodianRequest $request, $id = null) {
        
        return $this->result(
            $this->custodian->modify(
                $request->all(), $id
            )
        );
        
    }
    
    /**
     * 删除指定监护人
     *
     * @param $id
     * @return JsonResponse
     * @throws Exception
     * @throws Throwable
     */
    public function destroy($id = null) {
        
        return $this->result(
            $this->custodian->remove($id)
        );
        
    }
    
    /**
     * 批量发卡
     *
     * @return bool|JsonResponse|string
     * @throws Throwable
     */
    public function issue() {
        
        if (Request::method() == 'POST') {
            $result = $this->custodian->issue();
            !is_bool($result) ?: $result = response()->json([
                'title'   => '批量发卡',
                'message' => __('messages.ok'),
            ]);
            
            return $result;
        }
        return $this->output();
        
    }
    
    /**
     * 批量授权
     *
     * @return bool|JsonResponse|string
     * @throws Throwable
     */
    public function permit() {
        
        return Request::method() == 'POST'
            ? $this->custodian->permit()
            : $this->output();
        
    }
    
}
