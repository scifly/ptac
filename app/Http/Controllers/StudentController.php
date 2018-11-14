<?php
namespace App\Http\Controllers;

use App\Http\Requests\StudentRequest;
use App\Models\{Grade, Squad, Student};
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Request;
use Throwable;

/**
 * 学生
 *
 * Class StudentController
 * @package App\Http\Controllers
 */
class StudentController extends Controller {
    
    protected $student, $grade;
    
    /**
     * StudentController constructor.
     * @param Student $student
     * @param Grade $grade
     */
    function __construct(Student $student, Grade $grade) {
        
        $this->middleware(['auth', 'checkrole']);
        $this->student = $student;
        $this->grade = $grade;
        if (!Request::has('ids')) {
            $this->approve($student);
        }
        
    }
    
    /**
     * 学籍列表
     *
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function index() {
        
        return Request::get('draw')
            ? response()->json($this->student->index())
            : $this->output();
        
    }
    
    /**
     * 创建学籍
     *
     * @return JsonResponse
     * @throws Throwable
     */
    public function create() {
        
        return Request::method() == 'POST'
            ? $this->student->classList()
            : $this->output();
        
    }
    
    /**
     * 保存学籍
     *
     * @param StudentRequest $request
     * @return JsonResponse
     * @throws Exception
     * @throws Throwable
     */
    public function store(StudentRequest $request) {
        
        return $this->result(
            $this->student->store(
                $request->all()
            )
        );
        
    }
    
    /**
     * 编辑学籍
     *
     * @param $id
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function edit($id) {
        
        if (Request::method() === 'POST') {
            return $this->student->classList();
        }
        $student = $this->student->find($id);
        $student->{'grade_id'} = Squad::find($student->class_id)->grade_id;
        
        return $this->output([
            'student' => $student,
        ]);
        
    }
    
    /**
     * 更新学籍
     *
     * @param StudentRequest $request
     * @param $id
     * @return JsonResponse
     * @throws Exception
     * @throws Throwable
     */
    public function update(StudentRequest $request, $id = null) {
        
        return $this->result(
            $this->student->modify(
                $request->all(), $id
            )
        );
        
    }
    
    /**
     * 删除学籍
     *
     * @param $id
     * @return JsonResponse
     * @throws Exception
     * @throws Throwable
     */
    public function destroy($id = null) {
        
        return $this->result(
            $this->student->remove($id)
        );
        
    }
    
    /**
     * 导入学籍
     *
     * @return JsonResponse
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Reader\Exception
     */
    public function import() {
        
        return $this->result(
            $this->student->import(),
            __('messages.import_started'),
            __('messages.file_upload_failed')
        );
        
    }
    
    /**
     * 导出学籍
     *
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    public function export() {
        
        return Request::method() == 'POST'
            ? $this->student->classList()
            : $this->student->export();
        
    }
    
}