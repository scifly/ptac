<?php
namespace App\Http\Controllers;

use App\Http\Requests\EducatorRequest;
use App\Models\Department;
use App\Models\Educator;
use App\Models\School;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Request;
use Throwable;

/**
 * 教职员工
 *
 * Class EducatorController
 * @package App\Http\Controllers
 */
class EducatorController extends Controller {
    
    protected $educator, $department, $school;
    
    /**
     * EducatorController constructor.
     * @param Educator $educator
     * @param Department $department
     * @param School $school
     */
    public function __construct(Educator $educator, Department $department, School $school) {
        
        $this->middleware(['auth', 'checkrole']);
        $this->educator = $educator;
        $this->department = $department;
        $this->school = $school;
        if (!Request::has('ids')) {
            $this->approve($educator);
        }
        
    }
    
    /**
     * 教职员工列表
     *
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function index() {
        
        return Request::get('draw')
            ? response()->json(
                $this->educator->index()
            )
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
            ? $this->department->contacts(false)
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
     * @param $id
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function edit($id) {
        
        return Request::method() === 'POST'
            ? $this->department->contacts(false)
            : $this->output([
                'educator' => $this->educator->find($id),
            ]);
        
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
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    public function export() {
        
        return $this->educator->export();
        
    }
    
}
