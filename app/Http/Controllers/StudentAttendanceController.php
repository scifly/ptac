<?php
namespace App\Http\Controllers;

use App\Models\Grade;
use App\Models\Student;
use App\Models\StudentAttendance;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Request;
use Throwable;

/**
 * 学生考勤记录
 *
 * Class StudentAttendanceController
 * @package App\Http\Controllers
 */
class StudentAttendanceController extends Controller {
    
    protected $sa, $student, $grade;
    
    /**
     * StudentAttendanceController constructor.
     * @param StudentAttendance $sa
     * @param Student $student
     * @param Grade $grade
     */
    function __construct(StudentAttendance $sa, Student $student, Grade $grade) {
        
        $this->middleware(['auth', 'checkrole']);
        $this->sa = $sa;
        $this->student = $student;
        $this->grade = $grade;
        $this->approve($sa);
        
    }
    
    /**
     * 学生考勤记录列表
     *
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function index() {
        
        return Request::get('draw')
            ? response()->json($this->sa->index())
            : $this->output();
        
    }
    
    /**
     * 学生考勤统计
     *
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function stat() {
        
        if (Request::method() === 'POST') {
            $field = Request::input('field');
            $id = Request::input('id');
            if (isset($field, $id)) {
                list($classes) = $this->grade->classList(Request::input('id'));
                $response = response()->json(['html' => ['classes' => $classes]]);
            } else {
                $response = response()->json($this->sa->stat());
            }
        } else {
            $response = $this->output();
        }
        
        return $response;
        
    }
    
    /**
     * 查询学生考勤明细
     *
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function detail() {
        
        return Request::method() === 'POST'
            ? response()->json($this->sa->detail())
            : $this->output();
        
    }
    
    /**
     * 导出学生考勤明细
     *
     * @return mixed
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    public function export() {
        
        return $this->sa->export();
        
    }
    
}
