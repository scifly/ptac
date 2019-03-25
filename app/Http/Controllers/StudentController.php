<?php
namespace App\Http\Controllers;

use App\Http\Requests\StudentRequest;
use App\Models\{Grade, Student};
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Request;
use ReflectionException;
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
        Request::has('ids') ?: $this->approve($student);
        
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
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function edit() {
        
        return Request::method() == 'POST'
            ? $this->student->classList()
            : $this->output();
        
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
     * @return JsonResponse|mixed
     * @throws ReflectionException
     */
    public function export() {
        
        return Request::has('range')
            ? $this->result(
                $this->student->export(),
                __('messages.export_started')
            )
            : $this->student->classList();
        
    }
    
    /**
     * 批量发卡
     *
     * @return bool|JsonResponse|string
     * @throws Throwable
     */
    public function issue() {
    
        return Request::method() == 'POST'
            ? $this->student->issue()
            : $this->output();
    
    }
    
    /**
     * 批量发卡
     *
     * @return bool|JsonResponse|string
     * @throws Throwable
     */
    public function permit() {
    
        return Request::method() == 'POST'
            ? $this->student->permit()
            : $this->output();
    
    }
    
}