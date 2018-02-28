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
    
    function __construct(StudentAttendance $sa, Student $student, Media $media, School $school) {
        
        // $this->middleware(['auth']);
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
        if (!$student) {
            return response()->json(['message' => '学生信息有误！', 'statusCode' => HttpStatusCode::INTERNAL_SERVER_ERROR]);
        }
        $squad = $student->squad;
        if (!$squad) {
            return response()->json(['message' => '学生信息有误！', 'statusCode' => HttpStatusCode::INTERNAL_SERVER_ERROR]);
        }
        $grade = $squad->grade;
        $school = $grade->school;
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
            return response()->json(['message' => '学期信息有误！', 'statusCode' => HttpStatusCode::INTERNAL_SERVER_ERROR]);
        }
        //找出对应的考勤机id
        $attendance = AttendanceMachine::whereMachineid($input['attendId'])
            ->where('school_id', $school->id)->first();
        if (empty($attendance)) {
            return response()->json(['message' => '考勤机信息有误！', 'statusCode' => HttpStatusCode::INTERNAL_SERVER_ERROR]);
        }
        //根据时间找出对应的 规则
        $rules = StudentAttendanceSetting::where('grade_id', $grade->id)
            ->where('semester_id', $semester)
            ->where('day', $weekDay)
            ->get();
        if (count($rules) == 0) {
            return response()->json(['message' => '考勤规则有误！', 'statusCode' => HttpStatusCode::INTERNAL_SERVER_ERROR]);
        }
        
        return $this->sa->storeByFace($input)
            ? response()->json(['message' => 'success', 'statusCode' => HttpStatusCode::OK])
            : response()->json(['message' => 'failed', 'statusCode' => HttpStatusCode::INTERNAL_SERVER_ERROR]);
    }
    
}
