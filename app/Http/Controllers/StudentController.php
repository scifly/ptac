<?php
namespace App\Http\Controllers;

use App\Http\Requests\StudentRequest;
use App\Models\Student;
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
    
    protected $student;
    
    /**
     * StudentController constructor.
     * @param Student $student
     */
    function __construct(Student $student) {
        
        $this->middleware(['auth', 'checkrole']);
        $this->approve($this->student = $student);
        
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
     * @throws Throwable
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
     * @throws Exception
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
     * 批量授权
     *
     * @return bool|JsonResponse|string
     * @throws Throwable
     */
    public function grant() {
    
        return Request::method() == 'POST'
            ? $this->student->grant()
            : $this->output();
    
    }
    
    /**
     * 批量设置人脸识别
     *
     * @return bool|JsonResponse|string
     * @throws Throwable
     */
    public function face() {
        
        return Request::method() == 'POST'
            ? $this->student->face()
            : $this->output();
        
    }
    
}