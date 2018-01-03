<?php
namespace App\Http\Controllers;

use App\Http\Requests\StudentAttendanceRequest;
use App\Models\AttendanceMachine;
use App\Models\Media;
use App\Models\School;
use App\Models\Student;
use App\Models\StudentAttendance;
use Illuminate\Support\Facades\Request;

/**
 * 学生考勤记录
 *
 * Class StudentAttendanceController
 * @package App\Http\Controllers
 */
class StudentAttendanceController extends Controller {
    
    protected $studentAttendance, $student, $media;
    
    function __construct(StudentAttendance $studentAttendance, Student $student, Media $media) {
        
        // $this->middleware(['auth']);
        $this->studentAttendance = $studentAttendance;
        $this->student = $student;
        $this->media = $media;
        
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
                StudentAttendance::datatable()
            );
        }
        
        return $this->output();
        
    }
    
    /**
     * 写入学生考勤记录接口
     *
     * @param StudentAttendanceRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     * @throws \Throwable
     */
    public function createStuAttendance(StudentAttendanceRequest $request) {
        
        $input = $request->all();
        $school = School::whereName($input['schoolName'])->first();
        if (empty($school)) {
            return response()->json('学校有误 ，请重新输入！', 500);
        } else {
            $schoolId = $school->id;
        }
        $student = $this->student
            ->where('student_number', $input['student_number'])
            ->where('card_number', $input['cardId'])
            ->first();
        if (empty($student)) {
            return response()->json('卡号或学号有误 ，请重新输入！', 500);
        } else {
            $input['student_id'] = $student->id;
        }
        $attendance = AttendanceMachine::whereMachineid($input['attendId'])
            ->where('school_id', $schoolId)->first();
        if (empty($attendance)) {
            return response()->json('考勤机id有误 ，请重新输入！', 500);
        } else {
            $input['attendance_machine_id'] = $attendance->id;
        }
        
        return $this->studentAttendance->storeByFace($input)
            ? response()->json('success', 200)
            : response()->json('failed', 500);
    }
    
}
