<?php
namespace App\Http\Controllers;

use App\Http\Requests\StudentAttendanceRequest;
use App\Models\AttendanceMachine;
use App\Models\Media;
use App\Models\School;
use App\Models\Semester;
use App\Models\Student;
use App\Models\StudentAttendance;
use App\Models\StudentAttendanceSetting;
use Illuminate\Support\Facades\Log;
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
            $classId = Request::query('class_id');
            $startTime = Request::query('start_time');
            $endTime = Request::query('end_time');
            if ($field && $id) {
                $this->result['html'] = School::getFieldList($field, $id);
                return response()->json($this->result);
            }else{
                return response()->json($this->studentAttendance->getData($classId , $startTime , $endTime));
            }

        }

        return $this->output([
            'addBtn' => true,
            'item' => $this->studentAttendance->getData(),
        ]);

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
        #处理返回错误信息
        $student = Student::where('card_number', $input['card_number'])->first();
        $squad = $student->squad;
        $grade = $squad->grade;
        $school = $grade->school;
        if (!$squad) {
            return response()->json('学生信息有误！', 500);
        }
        $weekArray = ['星期日', '星期一', '星期二', '星期三', '星期四', '星期五', '星期六'];
        //将时间转化成时间戳 获得星期 日期 时间
        $time = strtotime($input['punch_time']);
        $weekDay = $weekArray[date("w", $time)];
        $date_time = date("Y-m-d", $time);
        $schoolSemesters = Semester::where('school_id', $school->id)->get();
        //找出对应的学期 根据打卡时间
        foreach ($schoolSemesters as $se) {
            if ($se->start_date <= $date_time && $se->end_date >= $date_time) {
                $semester = $se->id;
            }
        }
        if (!isset($semester)) {
            #没有找到打卡对应的学期
            return response()->json('学期信息有误！', 500);
        }
        //找出对应的考勤机id
        $attendance = AttendanceMachine::whereMachineid($input['attendId'])
            ->where('school_id', $school->id)->first();
        if (empty($attendance)) {
            return response()->json('考勤机信息有误！', 500);
        }
        //根据时间找出对应的 规则
        $rules = StudentAttendanceSetting::where('grade_id', $grade->id)
            ->where('semester_id', $semester)
            ->where('day', $weekDay)
            ->where('inorout', $input['inorout'])
            ->get();
        if (count($rules) == 0) {
            return response()->json('考勤规则有误！', 500);
        }
        
        return $this->studentAttendance->storeByFace($input) ? response()->json('success', 200) : response()->json('failed', 500);
    }
    
}
