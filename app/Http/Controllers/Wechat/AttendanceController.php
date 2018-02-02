<?php
namespace App\Http\Controllers\Wechat;

use App\Facades\Wechat;
use App\Http\Controllers\Controller;
use App\Models\Semester;
use App\Models\Squad;
use App\Models\Student;
use App\Models\StudentAttendance;
use App\Models\StudentAttendanceSetting;
use App\Models\User;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Session;

/**
 *  微信考勤
 *
 * Class AttendanceController
 * @package App\Http\Controllers\Wechat
 */
class AttendanceController extends Controller {
    
    /**
     * 考勤记录列表
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|string
     */
    public function index() {
        $corpId = 'wxe75227cead6b8aec';
        $secret = 'uorwAVlN3_EU31CDX0X1oQJk9lB0Or41juMH-cLcIEU';
        $agentId = 1000007;
        $userId = Session::get('userId') ? Session::get('userId') : null;
        $code = Request::input('code');
        if (empty($code) && empty($userId)) {
            $codeUrl = Wechat::getCodeUrl($corpId, $agentId, 'http://weixin.028lk.com/lists');
            return redirect($codeUrl);
        }elseif(!empty($code) && empty($userId)){
            $accessToken = Wechat::getAccessToken($corpId, $secret);
            $userInfo = json_decode(Wechat::getUserInfo($accessToken, $code), JSON_UNESCAPED_UNICODE);
            $userId = $userInfo['UserId'];
            Session::put('userId',$userId);
        }
        $user = User::whereUserid($userId)->first();
        #判断是否为教职工
        $educator = false;
        if (!$user) {
            return '<h4>你暂不是教职员工或监护人</h4>';
        }
        if ($user->group->name != '教职员工' && $user->group->name != '监护人') {
            return '<h4>你暂不是教职员工或监护人</h4>';
        }
        if ($user->group->name == '教职员工') {
            $educator = true;
        }
        #如果为教职工
        if ($educator) {
            return view('wechat.attendance_records.edu_attendance');
        }
        # 当月第一天
        $beginTime = date('Y-m-01', strtotime(date("Y-m-d"))) . ' 00:00:00';
        # 当月最后一天
        $endTime = date('Y-m-d', strtotime("$beginTime +1 month -1 day"));
        $endTime = $endTime . ' 23:59:59';
        $students = User::whereUserid($userId)->first()->custodian->students;
        $ids = $adays = $ndays = [];
        foreach ($students as $k => $s) {
            $data = $this->getDays($s->id, $beginTime, $endTime);
            $s->abnormal = count($data['adays']);
            $s->normal = count($data['ndays']);
            $s->schoolname = Squad::whereId($s->class_id)->first()->grade->school->name;
            $s->studentname = User::whereId($s->user_id)->first()->realname;
            $s->class_id = Squad::whereId($s->class_id)->first()->name;
        }
        
        return view('wechat.attendance_records.list', [
            'students' => $students,
        ]);
    }
    
    /**
     * 考勤记录
     *
     * @param null $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\View\View|\think\response\View
     */
    public function records($id = null) {
        if (Request::isMethod('post')) {
            if (array_key_exists('ym', Request::all())) {
                $ym = Request::get('ym');
                # 该月第一天
                $begin = $ym . '-01';
                # 该月最后一天
                $end = date('Y-m-d', strtotime("$begin +1 month -1 day"));
                $datas = $this->getDays(Request::get('id'), $begin, $end);
                
                return response()->json(['datas' => $datas]);
                
            } elseif (array_key_exists('years', Request::all())) {
                $years = Request::get('years');
                # 该月第一天
                $start = $years . '-01';
                $ends = date('Y-m-d', strtotime("$start +1 month -1 day"));
                $date = $this->getDays(Request::get('id'), $start, $ends);
                
                return response()->json(['date' => $date]);
            } elseif (array_key_exists('date', Request::all())) {
                $date = Request::get('date');
                $intoAttendance = StudentAttendance::whereStudentId(Request::get('id'))
                    ->whereDate('punch_time', $date)
                    ->where('inorout', 1)
                    ->orderBy('punch_time', 'ASC')
                    ->get();
                $outAttendance = StudentAttendance::whereStudentId(Request::get('id'))
                    ->whereDate('punch_time', $date)
                    ->where('inorout', 0)
                    ->orderBy('punch_time', 'desc')
                    ->get();
                
                return response()->json(['time' => $date, 'into' => $intoAttendance, 'out' => $outAttendance]);
            }
            
        }
        # 当月第一天
        $beginTime = date('Y-m-01', strtotime(date("Y-m-d"))) . ' 00:00:00';
        # 当月最后一天
        $endTime = date('Y-m-d', strtotime("$beginTime +1 month -1 day"));
        $endTime = $endTime . ' 23:59:59';
        # 当天时间
        $time = date('Y-m-d', time());
        // $time = '2018-01-08';
        $into = $out = [];
        $into = StudentAttendance::whereDate('punch_time', $time)
            ->where('student_id', $id)
            ->where('inorout', 1)
            ->orderBy('punch_time', 'ASC')
            ->get();
        $out = StudentAttendance::whereDate('punch_time', $time)
            ->where('student_id', $id)
            ->where('inorout', 0)
            ->orderBy('punch_time', 'ASC')
            ->get();
        $data = $this->getDays($id, $beginTime, $endTime);
        
        return view('wechat.attendance_records.attendance_records', [
            'id'   => $id,
            'data' => $data,
            'days' => json_encode($data, JSON_UNESCAPED_UNICODE),
            'time' => $time,
            'into' => $into,
            'out'  => $out,
        ]);
    }
    
    /**
     * 获取考勤异常和正常的天数
     *
     * @param $id
     * @param $beginTime
     * @param $endTime
     * @return array
     */
    public function getDays($id, $beginTime, $endTime) {
        $ids = $adays = $ndays = $data = [];
        $asum = $nsum = 0;
        # 查询考勤异常的数据
        $abnormal = StudentAttendance::whereStudentId($id)
            ->where('punch_time', '>', $beginTime)
            ->where('punch_time', '<', $endTime)
            ->where('status', '0')
            ->get();
        foreach ($abnormal as $a) {
            # 查询考勤异常当天的所有数据
            $sAttendance = StudentAttendance::whereDate('punch_time', substr($a->punch_time, 0, 10))
                ->where('punch_time', '>', $beginTime)
                ->where('punch_time', '<', $endTime)
                ->get();
            foreach ($sAttendance as $sa) {
                $ids[] = $sa->id;
                $adays[] = substr($sa->punch_time, 0, 10);
            }
            $adays = array_unique($adays);
            $asum = count($adays);
        }
        # 查询考勤正常天数的数据
        $normal = StudentAttendance::whereNotIn('id', $ids)
            ->where('punch_time', '>', $beginTime)
            ->where('punch_time', '<', $endTime)
            ->get();
        foreach ($normal as $n) {
            $ndays[] = substr($n->punch_time, 0, 10);
        }
        $ndays = array_unique($ndays);
        $nsum = count($ndays);
        $data = ['adays' => $adays, 'ndays' => $ndays, 'asum' => $asum, 'nsum' => $nsum];
        
        return $data;
    }
    
    /**
     * 学生饼图数据
     *
     * @throws \Throwable
     */
    public function stuChart() {
        #判断是否有条件
        $input = Request::all();
        $user = User::whereUserid(Session::get('userId'))->first();
        $educator = $user->educator;
        if(!$educator){
            return response()->json(['data' => '暂未找到您教师的身份！', 'statusCode' => 500]);
        }
        #班级列表 可能存在多个年级
        $squadLists = $educator->classes;
        if(count($squadLists) == 0){
            return response()->json(['data' => '老师，您还未绑定班级关系！', 'statusCode' => 500]);
        }
        $data['squadnames'] = [];
        $gradeIds = [];
        foreach ($squadLists as $s) {
            $gradeIds[] = $s->grade->id;
            $data['squadnames'][] = [
                'title' => $s->name, 'value' => $s->id,
            ];
        }
        if(!empty($data['squadnames'])){
            array_unique($data['squadnames']);
        }
        #根据年级分组规则
        $rules = StudentAttendanceSetting::whereIn('grade_id', array_unique($gradeIds))->get();
        $data['rulenames'] = [];
        foreach ($rules as $r) {
            $data['rulenames'][] = [
                'title' => $r->name, 'value' => $r->id
            ];
        }
        #饼图数据填充
        if (!isset($input['squad']) && !isset($input['time']) && !isset($input['rule'])) {
            $datas = $this->defcharts($educator, $data);
            if(!$datas){
                return response()->json(['data' => '请加入相应的考勤规则！', 'statusCode' => 500]);
            }
            return response()->json(['data' => $datas, 'statusCode' => 200]);
        } else {
            $datas = $this->fltcharts($input, $data);
            if(!$data){
                return response()->json(['data' => '请加入相应的考勤规则！', 'statusCode' => 500]);
            }
            return response()->json(['data' => $datas, 'statusCode' => 200]);
        }
    }
    
    /**
     * 饼图默认数据填入
     *
     * @param $educator
     * @param $data
     * @return bool|\Illuminate\Http\JsonResponse
     * @throws \Throwable
     */
    private function defcharts($educator, $data) {
        
        #如果条件为空 默认当天 该老师对应的第一个班级，第一个规则图
        #班级存在多个 默认取一个
        $squad = $educator->classes->first();
        $grade = $squad->grade;
        $school = $grade->school;
        $schoolSemesters = Semester::where('school_id', $school->id)->whereEnabled(1)->get();
        $students = $squad->students;
        $studentIds = [];
        $date = date('Y-m-d', time());
        foreach ($students as $student) {
            $studentIds[] = $student->id;
        }
        $weekArray = ['星期日', '星期一', '星期二', '星期三', '星期四', '星期五', '星期六'];
        $weekDay = $weekArray[date("w", time())];
        #找出对应的学期 根据当前时间
        foreach ($schoolSemesters as $se) {
            
            if ($se->start_date <= $date && $se->end_date >= $date) {
                $semester = $se->id;
            }
        }
        if (!isset($semester)) {
            #没有找到打卡对应的学期
            $semester = '';
        }
        $rule = StudentAttendanceSetting::where('grade_id', $grade->id)
            ->where('semester_id', $semester)
            ->where('day', $weekDay)
            ->first();
        #这个星期没有设置对应的规则
        if (!$rule){
            return false;
        }
        #同一个学生这段时间打了多次记录 取这段时间最晚的一条记录
        $attendances = StudentAttendance::where('sas_id', $rule->id)
            ->whereIn('student_id', $studentIds)
            ->whereNotNull('sas_id')
            ->whereDate('punch_time', $date)
            ->orderBy('punch_time', 'desc')
            ->get()
            ->unique('student_id');
        $normalRecords = $attendances->where('status', 1)->count();
        $abnormalRecords = $attendances->where('status', 0)->count();
        $noRecords = $squad->students->count() - $normalRecords - $abnormalRecords;
        $data['charts'] = [
            ['name' => '打卡', 'value' => $normalRecords],
            ['name' => '异常', 'value' => $abnormalRecords],
            ['name' => '未打卡', 'value' => $noRecords],
        ];
        #处理列表页
        //正常的学生列表
        $normalList = [];
        foreach ($attendances->where('status', 1) as $normal) {
            $student = $normal->student;
            $username = $student->user->realname;
            #对应的监护人
            $cusName = [];
            $cusPhone = [];
            $custodians = $student->custodians;
            foreach ($custodians as $custodian) {
                $cusName[] = $custodian->user->realname;
                foreach ($custodian->user->mobiles as $mobile) {
                    $cusPhone[] = $mobile->mobile;
                }
            }
            $normalList[] = [
                'username'   => $username,
                'cusname'    => $cusName,
                'cusphone'   => $cusPhone,
                'punch_time' => $normal->punch_time,
            ];
            
        }
        //异常的学生列表
        $abnormalList = [];
        foreach ($attendances->where('status', 0) as $normal) {
            $student = $normal->student;
            $username = $student->user->realname;
            #对应的监护人
            $cusName = [];
            $cusPhone = [];
            $custodians = $student->custodians;
            foreach ($custodians as $custodian) {
                $cusName[] = $custodian->user->realname;
                foreach ($custodian->user->mobiles as $mobile) {
                    $cusPhone[] = $mobile->mobile;
                }
            }
            $abnormalList[] = [
                'username'   => $username,
                'cusname'    => $cusName,
                'cusphone'   => $cusPhone,
                'punch_time' => $normal->punch_time,
            ];
        }
        //未打卡的学生列表
        $ids = [];
        $noStuList = [];
        foreach ($attendances as $attend) {
            $ids[] = $attend->student_id;
        }
        $stuIds = array_diff($studentIds, $ids);
        $stues = Student::whereIn('id', $stuIds)->get();
        foreach ($stues as $s) {
            $username = $s->user->realname;
            $custodians = $s->custodians;
            $cusName = [];
            $cusPhone = [];
            foreach ($custodians as $custodian) {
                $cusName[] = $custodian->user->realname;
                foreach ($custodian->user->mobiles as $mobile) {
                    $cusPhone[] = $mobile->mobile;
                }
            }
            $noStuList[] = [
                'username' => $username,
                'cusname'  => $cusName,
                'cusphone' => $cusPhone,
            ];
        }
        $data['view'] = view('wechat.attendance_records.edu_lists', [
            'normallist'   => $normalList,
            'abnormallist' => $abnormalList,
            'nostulist'    => $noStuList,
        ])->render();
        
        return !empty($data) ? $data : false;
    }
    
    /**
     * 饼图筛选填入数据
     *
     * @param $input
     * @param $data
     * @return bool|\Illuminate\Http\JsonResponse
     * @throws \Throwable
     */
    private function fltcharts($input, $data) {
        #传过来的值包含班级id 规则id 日期
        $squad = Squad::whereId($input['squad'])->first();
        $students = $squad->students;
        $studentIds = [];
        foreach ($students as $student) {
            $studentIds[] = $student->id;
        }
        // $grade = $squad->grade;
        $attendances = StudentAttendance::where('sas_id', $input['rule'])
            ->whereIn('student_id', $studentIds)
            ->whereNotNull('sas_id')
            ->whereDate('punch_time', $input['date'])
            ->orderBy('punch_time', 'desc')
            ->get()
            ->unique('student_id');
        $normalRecords = $attendances->where('status', 1)->count();
        $abnormalRecords = $attendances->where('status', 0)->count();
        $noRecords = $squad->students->count() - $normalRecords - $abnormalRecords;
        $data['charts'] = [
            ['name' => '打卡', 'value' => $normalRecords],
            ['name' => '异常', 'value' => $abnormalRecords],
            ['name' => '未打卡', 'value' => $noRecords],
        ];
        #处理列表页
        //正常的学生列表
        $normalList = [];
        foreach ($attendances->where('status', 1) as $normal) {
            $student = $normal->student;
            $username = $student->user->realname;
            #对应的监护人
            $cusName = [];
            $cusPhone = [];
            $custodians = $student->custodians;
            foreach ($custodians as $custodian) {
                $cusName[] = $custodian->user->realname;
                foreach ($custodian->user->mobiles as $mobile) {
                    $cusPhone[] = $mobile->mobile;
                }
            }
            $normalList[] = [
                'username'   => $username,
                'cusname'    => $cusName,
                'cusphone'   => $cusPhone,
                'punch_time' => $normal->punch_time,
            ];
            
        }
        //异常的学生列表
        $abnormalList = [];
        foreach ($attendances->where('status', 0) as $normal) {
            $student = $normal->student;
            $username = $student->user->realname;
            #对应的监护人
            $cusName = [];
            $cusPhone = [];
            $custodians = $student->custodians;
            foreach ($custodians as $custodian) {
                $cusName[] = $custodian->user->realname;
                foreach ($custodian->user->mobiles as $mobile) {
                    $cusPhone[] = $mobile->mobile;
                }
            }
            $abnormalList[] = [
                'username'   => $username,
                'cusname'    => $cusName,
                'cusphone'   => $cusPhone,
                'punch_time' => $normal->punch_time,
            ];
        }
        //未打卡的学生列表
        $ids = [];
        $noStuList = [];
        foreach ($attendances as $attend) {
            $ids[] = $attend->student_id;
        }
        $stuIds = array_diff($studentIds, $ids);
        $stues = Student::whereIn('id', $stuIds)->get();
        foreach ($stues as $s) {
            $username = $s->user->realname;
            $custodians = $s->custodians;
            $cusName = [];
            $cusPhone = [];
            foreach ($custodians as $custodian) {
                $cusName[] = $custodian->user->realname;
                foreach ($custodian->user->mobiles as $mobile) {
                    $cusPhone[] = $mobile->mobile;
                }
            }
            $noStuList[] = [
                'username' => $username,
                'cusname'  => $cusName,
                'cusphone' => $cusPhone,
            ];
        }
        $data['view'] = view('wechat.attendance_records.edu_lists', [
            'normallist'   => $normalList,
            'abnormallist' => $abnormalList,
            'nostulist'    => $noStuList,
        ])->render();
    
        return !empty($data) ? $data : false;
    }
    
    /**
     * 根据年级返回对应的规则
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getRules($id) {
        #当前年级所有考勤规则，不分学期
        $gradeId = Squad::whereId($id)->first()->grade_id;
        $rules = StudentAttendanceSetting::whereGradeId($gradeId)->get();
        $data = [];
        foreach ($rules as $r) {
            $data[] = [
                'title' => $r->name, 'value' => $r->id
            ];
        }
        if (empty($data)) {
            return response()->json(['data' => '该年级下未设置考勤规则！', 'statusCode' => 500]);
        }
        return response()->json(['data' => $data, 'statusCode' => 200]);
    }
    
    /**
     * 判断日期和规则是否匹配
     */
    public function dateRules(){
        $input = Request::all();
        if($input['date'] != null && $input['rule'] != null){
            #获取规则的星期
            $ruleDay = StudentAttendanceSetting::whereId($input['rule'])->first()->day;
            $weekArray = ['星期日', '星期一', '星期二', '星期三', '星期四', '星期五', '星期六'];
            $weekDay = $weekArray[date("w", strtotime($input['date']))];
            return $ruleDay == $weekDay ? response()->json(['message' => '', 'statusCode' => 200]) :
            response()->json(['message' => '请选择和规则对应的星期！', 'statusCode' => 500]);
        }
        return response()->json(['message' => '', 'statusCode' => 200]);
    }
}