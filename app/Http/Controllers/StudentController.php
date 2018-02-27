<?php
namespace App\Http\Controllers;

use App\Http\Requests\StudentRequest;

use App\Models\Department;
use App\Models\Grade;
use App\Models\School;
use App\Models\Squad;
use App\Models\Student;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Maatwebsite\Excel\Facades\Excel;
use PHPExcel_Exception;
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

        $groupId = Auth::user()->group->id;
        if (Request::method() === 'POST') {
            $field = Request::query('field');
            $id = Request::query('id');
            if($field && $id) {
                if($groupId > 5){
                    $educatorId = Auth::user()->educator->id;
                    $gradeClass = Student::getGrade($educatorId)[1];
                    $this->result['html'] = School::getFieldList($field, $id ,$gradeClass);
                }else{
                    $this->result['html'] = School::getFieldList($field, $id);
                }
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

        $users = Auth::user();
        $groupId = Auth::user()->group->id;
        if (Request::method() === 'POST') {
            $field = Request::get('field');
            $id = Request::get('id');
            if($groupId > 5){
                $educatorId = Auth::user()->educator->id;
                $gradeClass = Student::getGrade($educatorId)[1];
                $this->result['html'] = School::getFieldList($field, $id ,$gradeClass);
            }else{
                $this->result['html'] = School::getFieldList($field, $id);
            }
            return response()->json($this->result);
        }
        # 查询学生信息
        $student = Student::find($id);
        if (!$student) { return $this->notFound(); }
        $user = $student->user;
        if($groupId > 5){
            $educatorId = $users->educator->id;
            $gradeIds = Student::getGrade($educatorId)[0];
            $gradeClass = Student::getGrade($educatorId)[1];
            foreach ($gradeClass as $k=>$g){
                $grades = Grade::whereEnabled(1)
                    ->whereIn('id',$gradeIds)
                    ->pluck('name', 'id')
                    ->toArray();
                $classes = Squad::whereEnabled(1)
                    ->whereIn('id',$g)
                    ->pluck('name', 'id')
                    ->toArray();
                break;
            }
        }else{
            $items = Student::gradeClasses(
                $student->squad->grade_id
            );
            $student->{'grade_id'} = $student->squad->grade_id;
            $grades = $items['grades'];
            $classes = $items['classes'];
        }
        return $this->output([
            'student' => $student,
            'mobiles' => $user->mobiles,
            'grades'  => $grades,
            'classes' => $classes,
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
     *
     * @throws PHPExcel_Exception
     */
    public function import() {
        
        if (Request::isMethod('post')) {
            $file = Request::file('file');
<<<<<<< HEAD
            if (empty($file)) {
                $result = [
                    'statusCode' => self::INTERNAL_SERVER_ERROR,
                    'message'    => '您还没选择文件！',
                ];
                return response()->json($result);
            }
            // 文件是否上传成功
            if ($file->isValid()) {
                $result = Student::upload($file);
                return response()->json($result);
=======
            abort_if(
                empty($file),
                HttpStatusCode::INTERNAL_SERVER_ERROR,
                '您还没选择文件！'
            );
            // 文件是否上传成功
            if ($file->isValid()) {
                return response()->json(
                    $this->student->upload($file)
                );
>>>>>>> a8b77c532a4d09f2fe4f9feaadd84ba5d5a4fd12
            }
        }
        
        return abort(HttpStatusCode::INTERNAL_SERVER_ERROR, '上传失败');
        
    }
    
    /**
     * 导出学籍
     *
     * @return JsonResponse
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
        
<<<<<<< HEAD
=======
        return abort(HttpStatusCode::BAD_REQUEST, '导出失败');
        
>>>>>>> a8b77c532a4d09f2fe4f9feaadd84ba5d5a4fd12
    }




}
