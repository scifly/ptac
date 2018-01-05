<?php
namespace App\Http\Controllers;

use App\Http\Requests\StudentRequest;
use App\Models\Custodian;
use App\Models\Department;
use App\Models\School;
use App\Models\Student;
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
    
    function __construct() {
        
        $this->middleware(['auth', 'checkrole']);
        
    }
    
    /**
     * 学生记录列表
     *
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function index() {

        if (Request::get('draw')) {
            return response()->json(Student::datatable());
        }
        
        return $this->output();
        
    }
    
    /**
     * 创建学生记录
     *
     * @return JsonResponse
     * @throws Throwable
     */
    public function create() {
        
        if (Request::method() === 'POST') {
            $field = Request::query('field');
            $id = Request::query('id');
            if($field && $id) {
                $this->result['html'] = School::getFieldList($field, $id);
                return response()->json($this->result);
            }else{
                return response()->json(Department::tree());
            }
        }

        $items = Student::gradeClasses();
        
        return $this->output([
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
     * @throws Throwable
     */
    public function store(StudentRequest $request) {
        
        return $this->result(Student::store($request));
        
    }
    
    /**
     * 学生记录详情
     *
     * @param $id
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function show($id) {
        
        $student = Student::find($id);
        if (!$student) { return $this->notFound(); }
        
        return $this->output(['student' => $student]);
        
    }
    
    /**
     * 编辑学生记录
     *
     * @param $id
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function edit($id) {
        if (Request::method() === 'POST') {
            $field = Request::query('field');
            $id = Request::query('id');
            $this->result['html'] = School::getFieldList($field, $id);
            return response()->json($this->result);
        }
        # 查询学生信息
        $student = Student::find($id);
        if (!$student) { return $this->notFound(); }
        $user = $student->user;
        $items = Student::gradeClasses(
            $student->squad->grade_id
        );
        $student->{'grade_id'} = $student->squad->grade_id;
        return $this->output([
            'student' => $student,
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
     * @throws Throwable
     */
    public function update(StudentRequest $request, $id) {
        
        return $this->result(Student::modify($request, $id));
        
    }
    
    /**
     * 删除学生记录
     *
     * @param $id
     * @return JsonResponse
     * @throws Exception
     * @throws Throwable
     */
    public function destroy($id) {
        
        $student = Student::find($id);
        if (!$student) { return $this->notFound(); }
        
        return $this->result(Student::remove($id));
        
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
                $result = Student::upload($file);
                return response()->json($result);
            }
        }
        
        return null;
        
    }
    
    /**
     * 导出学籍
     */
    public function export() {

        if (Request::method() === 'POST') {
            $field = Request::query('field');
            $id = Request::query('id');
            $this->result['html'] = School::getFieldList($field, $id);
            return response()->json($this->result);
        }
        $id = Request::query('id');
        if ($id) {
            $data = Student::export($id);
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
