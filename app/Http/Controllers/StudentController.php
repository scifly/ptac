<?php
namespace App\Http\Controllers;

use App\Http\Requests\StudentRequest;
use App\Models\Custodian;
use App\Models\CustodianStudent;
use App\Models\Department;
use App\Models\DepartmentUser;
use App\Models\Group;
use App\Models\Student;
use App\Models\User;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Request;
use Maatwebsite\Excel\Facades\Excel;
use Throwable;

/**
 * 学生
 *
 * Class StudentController
 * @package App\Http\Controllers
 */
class StudentController extends Controller {
    
    protected $custodian, $department, $group, $user;
    protected $departmentUser, $student, $custodianStudent;
    
    function __construct(
        Custodian $custodian, Department $department,
        Group $group, User $user,
        DepartmentUser $departmentUser, Student $student,
        CustodianStudent $custodianStudent
    ) {
        
        $this->middleware(['auth']);
        $this->custodian = $custodian;
        $this->department = $department;
        $this->group = $group;
        $this->user = $user;
        $this->departmentUser = $departmentUser;
        $this->student = $student;
        $this->custodianStudent = $custodianStudent;
        
    }
    
    /**
     * 学生记录列表
     *
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function index() {
        
        if (Request::get('draw')) {
            return response()->json($this->student->datatable());
        }
        return $this->output(__METHOD__);
        
    }
    
    /**
     * 创建学生记录
     *
     * @return JsonResponse
     * @throws Throwable
     */
    public function create() {
        
        if (Request::method() === 'POST') {
            return $this->department->tree();
        }
        $items = $this->student->getGradeClass();
        return $this->output(__METHOD__, [
            'grades'  => $items['grades'],
            'classes' => $items['classes'],
        ]);
        
    }
    
    /**
     * 保存学生记录
     *
     * @param StudentRequest $request
     * @return JsonResponse
     * @throws Exception
     */
    public function store(StudentRequest $request) {
        
        return $this->student->store($request)
            ? $this->succeed() : $this->fail();
        
    }
    
    /**
     * 学生记录详情
     *
     * @param $id
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function show($id) {
        
        $student = $this->student->find($id);
        if (!$student) {
            return $this->notFound();
        }
        return $this->output(__METHOD__, ['student' => $student]);
        
    }
    
    /**
     * 编辑学生记录
     *
     * @param $id
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function edit($id) {
        
        # 查询学生信息
        $student = $this->student->find($id);
        if (!$student) {
            return $this->notFound();
        }
        $user = $student->user;
        // print_r($student->toArray());die;
        $items = $this->student->getGradeClass($student->squad->grade_id);
        $student->grade_id = $student->squad->grade_id;
        return $this->output(__METHOD__, [
            'student' => $student,
            // 'user'    => $user,
            'mobiles' => $user->mobiles,
            'grades'  => $items['grades'],
            'classes' => $items['classes'],
        ]);
        
    }
    
    /**
     * 更新学生记录
     *
     * @param StudentRequest $request
     * @param $id
     * @return JsonResponse
     * @throws Exception
     */
    public function update(StudentRequest $request, $id) {
        
        return $this->student->modify($request, $id)
            ? $this->succeed() : $this->fail();
        
    }
    
    /**
     * 删除学生记录
     *
     * @param $id
     * @return JsonResponse
     * @throws Exception
     */
    public function destroy($id) {
        
        return $this->custodian->remove($id)
            ? $this->succeed() : $this->fail();
        
    }
    
    /**
     * 导入学籍
     * @throws \PHPExcel_Exception
     */
    public function import() {
        
        if (Request::isMethod('post')) {
            $file = Request::file('file');
            if (empty($file)) {
                $result = [
                    'statusCode' => 500,
                    'message'    => '您还没选择文件！',
                ];
                return response()->json($result);
            }
            // 文件是否上传成功
            if ($file->isValid()) {
                $this->student->upload($file);
            }
        }
    }
    
    /**
     * 导出学籍
     */
    public function export() {
        $id = Request::query('id');
        if ($id) {
            $data = $this->student->export($id);
            /** @noinspection PhpMethodParametersCountMismatchInspection */
            /** @noinspection PhpUndefinedMethodInspection */
            Excel::create(iconv('UTF-8', 'GBK', '学生列表'), function ($excel) use ($data) {
                /** @noinspection PhpUndefinedMethodInspection */
                $excel->sheet('score', function ($sheet) use ($data) {
                    /** @noinspection PhpUndefinedMethodInspection */
                    $sheet->rows($data);
                    /** @noinspection PhpUndefinedMethodInspection */
                    $sheet->setColumnFormat([
                        'E' => '@',//文本
                        'H' => 'yyyy-mm-dd',
                    ]);
                    /** @noinspection PhpUndefinedMethodInspection */
                    $sheet->setWidth([
                        'A' => 20,
                        'B' => 10,
                        'C' => 25,
                        'D' => 30,
                        'E' => 30,
                        'F' => 30,
                        'G' => 20,
                        'H' => 15,
                        'I' => 30,
                        'J' => 30,
                        'K' => 15,
                        'L' => 30,
                    ]);
                });
            }, 'UTF-8')->export('xls');
        }
        
    }
    
}
