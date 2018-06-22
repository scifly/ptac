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
    
    function __construct(Custodian $custodian) {
        
        $this->middleware(['auth', 'checkrole']);
        $this->custodian = $custodian;
        if (!Request::has('ids')) {
            $this->approve($custodian);
        }
        
    }
    
    /**
     * 监护人列表
     *
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function index() {
        
        if (Request::get('draw')) {
            return response()->json(
                $this->custodian->datatable()
            );
        }
        
        return $this->output();
        
    }
    
    /**
     * 创建监护人
     *
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function create() {
        
        if (Request::method() === 'POST') {
            return $this->custodian->studentList();
        }
        
        return $this->output();
        
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
     * 监护人详情
     *
     * @param $id
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function show($id) {
        
        return $this->output([
            'custodian' => $this->custodian->find($id),
        ]);
        
    }
    
    /**
     * 编辑监护人
     *
     * @param $id
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function edit($id) {
        
        if (Request::method() === 'POST') {
            return $this->custodian->studentList();
        }
        
        return $this->output([
            'custodian' => $this->custodian->find($id),
        ]);
        
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
     * 导出监护人
     *
     * @return array
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    public function export() {
    
        $range = Request::query('range');
        $departmentId = Request::query('id');
        
        return $this->custodian->export(
            $range, $departmentId
        );
        
    }
    
}
