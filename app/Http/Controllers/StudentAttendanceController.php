<?php
namespace App\Http\Controllers;

use App\Models\Grade;
use App\Models\Media;
use App\Models\School;
use App\Models\Student;
use App\Models\StudentAttendance;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Request;

/**
 * 学生考勤记录
 *
 * Class StudentAttendanceController
 * @package App\Http\Controllers
 */
class StudentAttendanceController extends Controller {
    
    protected $sa, $student, $grade;
    
    function __construct(StudentAttendance $sa, Student $student, Grade $grade) {
        
        $this->middleware(['auth', 'checkrole']);
        $this->sa = $sa;
        $this->student = $student;
        $this->grade = $grade;
        
    }
    
    /**
     * 学生考勤记录列表
     *
     * @return bool|\Illuminate\Http\JsonResponse
     * @throws \Throwable
     */
    public function index() {
        
        if (Request::get('draw')) {
            return response()->json(
                $this->sa->datatable()
            );
        }
        
        return $this->output();
        
    }
    
    /**
     * 考勤统计
     *
     * @return bool|\Illuminate\Http\JsonResponse
     * @throws \Throwable
     */
    public function stat() {
        
        $this->authorize(
            'sde', StudentAttendance::class
        );
        if (Request::method() === 'POST') {
            $field = Request::input('field');
            $id = Request::input('id');
            if ($field && $id) {
                list($classes) = $this->grade->classList(
                    Request::input('id')
                );
                $this->result['html']['classes'] = $classes;
                return response()->json($this->result);
            } else {
                return response()->json(
                    $this->sa->stat()
                );
            }
        }
        
        return $this->output();
        
    }
    
    /**
     * 查学生考勤明细
     *
     * @return bool|\Illuminate\Http\JsonResponse
     * @throws \Throwable
     */
    public function detail() {
        
        $this->authorize(
            'sde', StudentAttendance::class
        );
        if (Request::method() === 'POST') {
            return response()->json(
                $this->sa->detail()
            );
        }
        
        return $this->output();
        
    }
    
    /**
     * 导出学生考勤明细
     *
     * @return mixed
     * @throws AuthorizationException
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    public function export() {
    
        $this->authorize(
            'sde', StudentAttendance::class
        );
        
        return $this->sa->export();
    
    }
    
}
