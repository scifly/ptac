<?php
namespace App\Http\Controllers;

use App\Helpers\HttpStatusCode;
use App\Http\Requests\ConsumptionRequest;
use App\Http\Requests\StudentAttendanceRequest;
use App\Models\AttendanceMachine;
use App\Models\Consumption;
use App\Models\Semester;
use App\Models\Student;
use App\Models\StudentAttendance;
use App\Models\StudentAttendanceSetting;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Throwable;

class ApiController extends Controller {
    
    protected $consumption, $sa;
    
    function __construct(Consumption $consumption, StudentAttendance $sa) {
        
        $this->consumption = $consumption;
        $this->sa = $sa;
        
    }
    
    /**
     * 登录
     *
     * @return JsonResponse
     */
    public function login() {
    
        if (Auth::id() || Auth::attempt([
            'username' => request('username'),
            'password' => request('password')
        ])) {
            $user = Auth::user();
            $this->result['token'] = $user->createToken('ptac')->accessToken;
            $statusCode = HttpStatusCode::OK;
        } else {
            $this->result['message'] = __('messages.forbidden');
            $this->result['statusCode'] = $statusCode = HttpStatusCode::UNAUTHORIZED;
        }
        
        return response()->json(
            $this->result,
            $statusCode
        );
    
    }
    
    /**
     * 消费记录
     *
     * @param ConsumptionRequest $request
     * @return JsonResponse|string
     */
    public function studentConsumption(ConsumptionRequest $request) {
    
        return $this->result(
            $this->consumption->store(
                $request->all()
            )
        );
    
    }
    
    /**
     * 学生考勤记录
     *
     * @param StudentAttendanceRequest $request
     * @return JsonResponse
     * @throws Throwable
     */
    public function studentAttendance(StudentAttendanceRequest $request) {
        
        $input = $request->all();
        #处理返回错误信息
        $student = Student::where('card_number', $input['card_number'])->first();
        if (!$student) {
            $this->result['statusCode'] = HttpStatusCode::INTERNAL_SERVER_ERROR;
            $this->result['message'] = '学生信息有误！';
            return response()->json(
                $this->result, HttpStatusCode::INTERNAL_SERVER_ERROR
            );
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
