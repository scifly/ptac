<?php
namespace App\Http\Controllers;

use App\Http\Requests\EducatorRequest;
use App\Models\{Custodian, Department, Educator, School};
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Request;
use ReflectionException;
use Throwable;

/**
 * 教职员工
 *
 * Class EducatorController
 * @package App\Http\Controllers
 */
class EducatorController extends Controller {
    
    protected $educator, $department, $school, $custodian;
    
    /**
     * EducatorController constructor.
     * @param Educator $educator
     * @param Department $department
     * @param School $school
     * @param Custodian $custodian
     */
    public function __construct(
        Educator $educator,
        Department $department,
        School $school,
        Custodian $custodian
    ) {
        
        $this->middleware(['auth', 'checkrole']);
        $this->educator = $educator;
        $this->department = $department;
        $this->school = $school;
        $this->custodian = $custodian;
        Request::has('ids') ?: $this->approve($educator);
        
    }
    
    /**
     * 教职员工列表
     *
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function index() {
        
        return Request::get('draw')
            ? response()->json($this->educator->index())
            : $this->output();
        
    }
    
    /**
     * 创建教职员工
     *
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function create() {
        
        return Request::method() === 'POST'
            ? (
                Request::has('field')
                ? $this->custodian->csList()
                : $this->department->contacts(false)
            )
            : $this->output();
        
    }
    
    /**
     * 保存教职员工
     *
     * @param EducatorRequest $request
     * @return JsonResponse
     * @throws Throwable
     */
    public function store(EducatorRequest $request) {
        
        return $this->result(
            $this->educator->store(
                $request->all()
            )
        );
        
    }
    
    /**
     * 编辑教职员工
     *
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function edit() {
        
        return Request::method() === 'POST'
            ? (
                Request::has('field')
                ? $this->custodian->csList()
                : $this->department->contacts(false)
            )
            : $this->output();
        
    }
    
    /**
     * 更新教职员工
     *
     * @param EducatorRequest $request
     * @param $id
     * @return JsonResponse
     * @throws Throwable
     */
    public function update(EducatorRequest $request, $id = null) {
        
        return $this->result(
            $this->educator->modify(
                $request->all(), $id
            )
        );
        
    }
    
    /**
     * 增加短信额度（充值）
     *
     * @param $id
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function recharge($id) {
        
        return Request::method() == 'PUT'
            ? $this->educator->recharge($id, Request::all())
            : $this->output(['educator' => $this->educator->find($id)]);
        
    }
    
    /**
     * 删除教职员工
     *
     * @param $id
     * @return JsonResponse
     * @throws Throwable
     * @throws Exception
     */
    public function destroy($id = null) {
        
        return $this->result(
            $this->educator->remove($id)
        );
        
    }
    
    /**
     * 导入教职员工
     *
     * @return JsonResponse|null
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Reader\Exception
     */
    public function import() {
    
        return $this->result(
            $this->educator->import(),
            __('messages.import_started'),
            __('messages.file_upload_failed')
        );
        
    }
    
    /**
     * 导出教职员工
     *
     * @return mixed
     * @throws ReflectionException
     */
    public function export() {
        
        return $this->result(
            $this->educator->export(),
            __('messages.export_started')
        );
        
    }
    
    /**
     * 批量发卡
     *
     * @return bool|JsonResponse|string
     * @throws Throwable
     */
    public function issue() {
        
        return Request::method() == 'POST'
            ? $this->educator->issue()
            : $this->output();
        
    }

    /**
     * 批量授权
     *
     * @return bool|JsonResponse|string
     * @throws Throwable
     */
    public function permit() {
        
        return Request::method() == 'POST'
            ? $this->educator->permit()
            : $this->output();
        
    }
    
}
