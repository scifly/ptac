<?php
namespace App\Http\Controllers;

use App\Helpers\HttpStatusCode;
use App\Http\Requests\StudentAttendanceRequest;
use App\Models\AttendanceMachine;
use App\Models\Media;
use App\Models\School;
use App\Models\Semester;
use App\Models\Student;
use App\Models\StudentAttendance;
use App\Models\StudentAttendanceSetting;
use Illuminate\Support\Facades\Request;

/**
 * 学生考勤记录
 *
 * Class StudentAttendanceController
 * @package App\Http\Controllers
 */
class StudentAttendanceController extends Controller {
    
    protected $sa, $student, $media, $school;
    
    function __construct(
        StudentAttendance $sa, Student $student,
        Media $media, School $school
    ) {
        
        // $this->middleware(['auth', 'checkrole']);
        $this->sa = $sa;
        $this->student = $student;
        $this->media = $media;
        $this->school = $school;
        
    }
    
    /**
     * 学生考勤记录列表
     *
     * @return bool|\Illuminate\Http\JsonResponse
     * @throws \Throwable
     */
    public function index() {
        
        $this->middleware(['auth']);
        if (Request::get('draw')) {
            return response()->json(
                $this->sa->datatable()
            );
        }
        
        return $this->output(['addBtn' => true]);
        
    }

    /**
     * 考勤统计
     *
     * @return bool|\Illuminate\Http\JsonResponse
     * @throws \Throwable
     */
    public function count() {

        if (Request::method() === 'POST') {
            $field = Request::query('field');
            $id = Request::query('id');
            $classId = Request::input('class_id');
            $startTime = Request::input('start_time');
            $endTime = Request::input('end_time');
            $days = Request::input('days');
            if ($field && $id) {
                $this->result['html'] = $this->school->getFieldList($field, $id);
                return response()->json($this->result);
            }else{
                return response()->json($this->sa->getData($classId , $startTime , date("Y-m-d", (strtotime($endTime) + 86400)), $days));
            }

        }

        return $this->output([
            'addBtn' => true,
//            'item' => $this->studentAttendance->getData(),
        ]);

    }
    /**
     * 考勤统计学生详情
     *
     * @return bool|\Illuminate\Http\JsonResponse
     * @throws \Throwable
     */
    public function student() {

        if (Request::method() === 'POST') {
            $date = Request::input('date');
            $type = Request::input('type');
            $classId = Request::input('class_id');

            if ($date && $type && $classId) {

                return response()->json($this->sa->getStudentData($date , $type, $classId));

            }

        }

        return $this->output([
            'addBtn' => true,
//            'item' => $this->studentAttendance->getData(),
        ]);

    }
    
    
}
