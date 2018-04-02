<?php
namespace App\Http\Controllers;

use App\Helpers\HttpStatusCode;
use App\Http\Requests\StudentRequest;
use App\Models\Grade;
use App\Models\Student;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
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
    
    function __construct(Student $student, Grade $grade) {
        
        $this->middleware(['auth', 'checkrole']);
        $this->student = $student;
        $this->grade = $grade;
        
    }
    
    /**
     * 学籍列表
     *
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function index() {
        
        if (Request::get('draw')) {
            return response()->json(
                $this->student->datatable()
            );
        }
        
        return $this->output();
        
    }
    
    /**
     * 创建学籍
     *
     * @return JsonResponse
     * @throws Throwable
     */
    public function create() {
        
        $this->authorize(
            'csie', Student::class
        );
        if (Request::method() === 'POST') {
            list($classes) = $this->grade->classList(
                Request::input('id')
            );
            $this->result['html']['classes'] = $classes;
            
            return response()->json($this->result);
        }
        
        return $this->output();
        
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
        
        $this->authorize(
            'csie', Student::class
        );
        
        return $this->result(
            $this->student->store(
                $request->all()
            )
        );
        
    }
    
    /**
     * 学籍详情
     *
     * @param $id
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function show($id) {
        
        $student = Student::find($id);
        $this->authorize('seud', $student);
        
        return $this->output([
            'student' => $student,
        ]);
        
    }
    
    /**
     * 编辑学籍
     *
     * @param $id
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function edit($id) {
        
        $student = $this->student->find($id);
        $this->authorize('seud', $student);
        if (Request::method() === 'POST') {
            list($classes) = $this->grade->classList(
                Request::input('id')
            );
            $this->result['html']['classes'] = $classes;
            
            return response()->json($this->result);
        }
        
        return $this->output([
            'student' => $student,
            'user'    => $student->user,
            'mobiles' => $student->user->mobiles,
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
    public function update(StudentRequest $request, $id) {
        
        $student = $this->student->find($id);
        $this->authorize('seud', $student);
        
        return $this->result(
            $student->modify(
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
    public function destroy($id) {
        
        $student = $this->student->find($id);
        $this->authorize('seud', $student);
        
        return $this->result(
            $student->remove($id)
        );
        
    }
    
    /**
     * 导入学籍
     *
     * @return JsonResponse
     * @throws AuthorizationException
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Reader\Exception
     */
    public function import() {
        
        $this->authorize(
            'csie', Student::class
        );
        if (Request::isMethod('post')) {
            $file = Request::file('file');
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
            }
        }
        
        return abort(HttpStatusCode::INTERNAL_SERVER_ERROR, '上传失败');
        
    }
    
    /**
     * 导出学籍
     *
     * @throws AuthorizationException
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    public function export() {
        
        $this->authorize(
            'csie', Student::class
        );
        if (Request::method() === 'POST') {
            list($classes) = $this->grade->classList(
                Request::input('id')
            );
            $this->result['html']['classes'] = $classes;
            
            return response()->json($this->result);
        }
        $range = Request::query('range');
        $id = Request::query('id');
        
        return $this->student->export($range, $id);
        
    }
    
}
