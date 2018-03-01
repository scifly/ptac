<?php
namespace App\Http\Controllers;

use App\Helpers\Constant;
use App\Http\Requests\CustodianRequest;
use App\Models\Custodian;
use App\Models\CustodianStudent;
use App\Models\Department;
use App\Models\School;
use App\Models\Student;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Maatwebsite\Excel\Facades\Excel;
use Throwable;

/**
 * 监护人
 *
 * Class CustodianController
 * @package App\Http\Controllers
 */
class CustodianController extends Controller {
    
    protected $custodian, $student, $department, $school;
    
    function __construct(
        Custodian $custodian, Student $student,
        Department $department, School $school
    ) {
        
        $this->middleware(['auth', 'checkrole']);
        $this->custodian = $custodian;
        $this->student = $student;
        $this->department = $department;
        $this->school = $school;
        
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
        
        $this->authorize(
            'cse', Custodian::class
        );
        if (Request::method() === 'POST') {
            $user = Auth::user();
            $field = Request::query('field');
            $id = Request::query('id');
            if (!in_array($user->group->name, Constant::SUPER_ROLES)) {
                $educatorId = $user->educator->id;
                $gradeClass = $this->student->getGrade($educatorId)[1];
                $this->result['html'] = $this->school->getFieldList($field, $id, $gradeClass);
            } else {
                $this->result['html'] = $this->school->getFieldList($field, $id);
            }
            
            return response()->json($this->result);
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
        
        $this->authorize(
            'cse', Custodian::class
        );
        
        return $this->result(
            $this->custodian->store($request)
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
        
        $custodian = $this->custodian->find($id);
        $this->authorize('seud', $custodian);
        
        return $this->output([
            'custodian' => $custodian,
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
    
        $custodian = $this->custodian->find($id);
        $this->authorize('seud', $custodian);
        if (Request::method() === 'POST') {
            $field = Request::query('field');
            $id = Request::query('id');
            if ($field && $id) {
                $this->result['html'] = $this->school->getFieldList($field, $id);
                return response()->json($this->result);
            } else {
                return response()->json($this->department->tree());
            }
        }
        $pupils = CustodianStudent::whereCustodianId($id)->get();
        
        return $this->output([
            'mobiles'   => $custodian->user->mobiles,
            'custodian' => $custodian,
            'pupils'    => $pupils,
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
    public function update(CustodianRequest $request, $id) {
        
        $custodian = $this->custodian->find($id);
        $this->authorize('seud', $custodian);
        
        return $this->result(
            $custodian->modify($request, $id)
        );
        
    }
    
    /**
     * 删除指定的监护人
     *
     * @param $id
     * @return JsonResponse
     * @throws Exception
     * @throws Throwable
     */
    public function destroy($id) {
        
        $custodian = $this->custodian->find($id);
        $this->authorize('seud', $custodian);
        
        return $this->result(
            $custodian->remove($id)
        );
        
    }
    
    /**
     * 导出监护人
     *
     * @return void
     * @throws AuthorizationException
     */
    public function export() {

        $this->authorize(
            'cse', Custodian::class
        );
        $data = $this->custodian->export();
        /** @noinspection PhpMethodParametersCountMismatchInspection */
        /** @noinspection PhpUndefinedMethodInspection */
        Excel::create(iconv('UTF-8', 'GBK', '监护人列表'), function ($excel) use ($data) {
            /** @noinspection PhpUndefinedMethodInspection */
            $excel->sheet('score', function ($sheet) use ($data) {
                /** @noinspection PhpUndefinedMethodInspection */
                $sheet->rows($data);
                /** @noinspection PhpUndefinedMethodInspection */
                $sheet->setWidth([
                    'A' => 30,
                    'B' => 30,
                    'C' => 30,
                    'D' => 30,
                    'E' => 30,
                    'F' => 30,
                ]);
                
            });
        }, 'UTF-8')->export('xls');
        
    }
    
}
