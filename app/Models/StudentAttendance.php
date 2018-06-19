<?php
namespace App\Models;

use Eloquent;
use Exception;
use Throwable;
use Carbon\Carbon;
use App\Helpers\Snippet;
use App\Helpers\Constant;
use Illuminate\View\View;
use App\Helpers\ModelTrait;
use App\Helpers\HttpStatusCode;
use Illuminate\Validation\Rule;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Contracts\View\Factory;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Session;
use App\Facades\DatatableFacade as Datatable;

/**
 * App\Models\StudentAttendance 学生考勤记录
 *
 * @property int $id
 * @property int $student_id 学生ID
 * @property int $sas_id 关联规则id
 * @property string $punch_time 打卡时间
 * @property int $inorout 进或出
 * @property int $attendance_machine_id 考勤机ID
 * @property int $media_id 考勤照片多媒体ID
 * @property int $status 考勤状态
 * @property float $longitude 打卡时所处经度
 * @property float $latitude 打卡时所处纬度
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read AttendanceMachine $attendanceMachine
 * @property-read Media $medias
 * @property-read Student $student
 * @property-read StudentAttendanceSetting $studentAttendancesetting
 * @method static Builder|StudentAttendance whereAttendanceMachineId($value)
 * @method static Builder|StudentAttendance whereCreatedAt($value)
 * @method static Builder|StudentAttendance whereId($value)
 * @method static Builder|StudentAttendance whereInorout($value)
 * @method static Builder|StudentAttendance whereLatitude($value)
 * @method static Builder|StudentAttendance whereLongitude($value)
 * @method static Builder|StudentAttendance whereMediaId($value)
 * @method static Builder|StudentAttendance wherePunchTime($value)
 * @method static Builder|StudentAttendance whereSasId($value)
 * @method static Builder|StudentAttendance whereStatus($value)
 * @method static Builder|StudentAttendance whereStudentId($value)
 * @method static Builder|StudentAttendance whereUpdatedAt($value)
 * @mixin Eloquent
 */
class StudentAttendance extends Model {
    
    use ModelTrait;
    
    protected $table = 'student_attendances';
    protected $fillable = [
        'id', 'student_id', 'punch_time', 'sas_id',
        'inorout', 'attendance_machine_id', 'media_id',
        'status', 'longitude', 'latitude', 'created_at',
        'updated_at',
    ];
    const EXPORT_TITLES = [
        '姓名', '监护人', '手机号码', '打卡时间', '进/出'
    ];
    const WEEK_DAYS = ['星期日', '星期一', '星期二', '星期三', '星期四', '星期五', '星期六'];
    const VIEW_NS = 'wechat.attendance.';
    
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    function attendanceMachine() { return $this->belongsTo('App\Models\AttendanceMachine'); }
    
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    function student() { return $this->belongsTo('App\Models\Student'); }
    
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    function media() { return $this->belongsTo('App\Models\Media'); }
    
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    function studentAttendanceSetting() {
        
        return $this->belongsTo('App\Models\StudentAttendanceSetting', 'sas_id', 'id');
        
    }
    
    /**
     * 保存学生考勤记录
     *
     * @param array $data
     * @return bool
     */
    function store(array $data) {
        
        $student = Student::whereCardNumber($data['card_number'])->first();
        abort_if(!$student, HttpStatusCode::NOT_FOUND, __('messages.student.not_found'));
        $class = $student->squad;
        abort_if(!$class, HttpStatusCode::NOT_FOUND, __('messages.class.not_found'));
        $grade = $class->grade;
        abort_if(!$grade, HttpStatusCode::NOT_FOUND, __('messages.grade.not_found'));
        $school = $grade->school;
        abort_if(!$school, HttpStatusCode::NOT_FOUND, __('messages.school.not_found'));
        $dateTime = strtotime($data['punch_time']);
        $day = Constant::WEEK_DAYS[date('w', $dateTime)];
        $strDateTime = date('Y-m-d', $dateTime);
        $semester = Semester::where('start_date', '<=', $strDateTime)
            ->where('end_date', '>=', $strDateTime)
            ->where('enabled', 1)
            ->first();
        abort_if(!$semester, HttpStatusCode::NOT_FOUND, __('messages.semester.not_found'));
        $machine = AttendanceMachine::whereMachineid($data['machineid'])
            ->where('school_id', $school->id)->first();
        abort_if(!$machine, HttpStatusCode::NOT_FOUND, __('messages.attendance_machine.not_found'));
        $sases = StudentAttendanceSetting::whereGradeId($grade->id)
            ->where('semester_id', $semester->id)
            ->where('day', $day)->get();
        abort_if(!$sases, HttpStatusCode::NOT_FOUND, __('messages.sas.not_found'));
        $punchTime = date('H:i:s', $dateTime);
        $status = 0; # 考勤异常
        $sasId = 0;
        foreach ($sases as $sas) {
            $sasId = $sas->id;
            if ($punchTime <= $sas->end && $punchTime >= $sas->start) {
                $status = 1; break;
            }
        }
        
        return $this->create([
            'student_id' => $student->id,
            'sas_id' => $sasId,
            'punch_time' => $dateTime,
            'inorout' => $data['inorout'],
            'attendance_machine_id' => $machine->id,
            'status' => $status,
            'longitude' => $data['longitude'],
            'latitude' => $data['latitude'],
            'media_id' => $data['media_id']
        ]) ? true : false;
        
    }
    
    /**
     * 删除学生考勤记录
     *
     * @param null $id
     * @return bool
     * @throws Exception
     */
    function remove($id = null) {

        return $id
            ? $this->find($id)->delete()
            : $this->whereIn('id', array_values(Request::input('ids')))->delete();
    
    }
    
    /**
     * 考勤统计
     *
     * @return array
     */
    function stat() {
    
        Request::validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|greater_than_or_equal_to:start_date'
        ]);
        $classId = Request::input('class_id') ?? head($this->classIds());
        $startDate = Request::input('start_date') ?? date('Y-m-d', strtotime('-7 day'));
        $endDate = Request::input('end_date') ?? date('Y-m-d');
        $days = Carbon::createFromTimestamp(strtotime($startDate))->diffInDays(
            Carbon::createFromTimestamp(strtotime($endDate))
        ) + 1;
        # 指定班级所有学生的id
        $studentIds = Student::whereClassId($classId)->get()->pluck('id')->toArray();
        if (empty($studentIds)) { return []; }
        $attendances = $this->latestAttendances(
            implode(',', $studentIds),
            $startDate,
            $endDate
        );
        $normals = [];
        $abnormals = [];
        if (!empty($attendances)) {
            foreach ($attendances as $key => &$val) {
                if ($val->lastest) {
                    if (isset($normals[$val->day])) {
                        $normals[$val->day] += 1;
                    } else {
                        $normals[$val->day] = 1;
                    }
                } else {
                    if (isset($abnormals[$val->day])) {
                        $abnormals[$val->day] += 1;
                    } else {
                        $abnormals[$val->day] = 1;
                    }
                }
            }
        }
        $results = [];
        for ($i = 0; $i < $days; $i++) {
            $date = strtotime($startDate);
            $date = date("Y-m-d", $date + (86400 * $i));
            $all = sizeof($studentIds);
            $normal = $normals[$date] ?? 0;
            $abnormal = $abnormals[$date] ?? 0;
            $results[$i] = [
                'date' => $date,
                'all' => $all,
                'normal' => $normal,
                'abnormal' => $abnormal,
                'missed' => $all - $normal - $abnormal
            ];
        }
        
        return $results;
        
    }
    
    /**
     * 获取考勤明细
     *
     * @return array
     */
    function detail() {
        
        $details = $this->details();
        # 缓存导出数据
        $this->cache($details);
        
        return $details;
        
    }
    
    /**
     * 导出考勤明细
     *
     * @return mixed
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    function export() {
        
        abort_if(
            !session('sa_details'),
            HttpStatusCode::BAD_REQUEST,
            __('messages.bad_request')
        );
        $details = session('sa_details');
        Session::forget('sa_details');
        
        return $this->excel(
            $details,
            '学生考勤明细',
            '考勤明细'
        );
        
    }
    
    /**
     * 学生考勤记录列表
     *
     * @return array
     */
    function datatable() {
        
        $columns = [
            ['db' => 'StudentAttendance.id', 'dt' => 0],
            ['db' => 'User.realname', 'dt' => 1],
            ['db' => 'Student.card_number', 'dt' => 2],
            ['db' => 'StudentAttendance.punch_time', 'dt' => 3],
            ['db' => 'StudentAttendanceSetting.name as sasname', 'dt' => 4],
            ['db' => 'AttendanceMachine.name as machinename', 'dt' => 5],
            [
                'db'        => 'StudentAttendance.inorout', 'dt' => 6,
                'formatter' => function ($d) {
                    if ($d == 2) {
                        return '';
                    } else {
                        return $d
                            ? sprintf(Snippet::BADGE_GREEN, '进')
                            : sprintf(Snippet::BADGE_RED, '出');
                    }
                },
            ],
            ['db' => 'StudentAttendance.status', 'dt' => 7],
        ];
        $joins = [
            [
                'table'      => 'students',
                'alias'      => 'Student',
                'type'       => 'INNER',
                'conditions' => [
                    'Student.id = StudentAttendance.student_id',
                ],
            ],
            [
                'table'      => 'attendance_machines',
                'alias'      => 'AttendanceMachine',
                'type'       => 'INNER',
                'conditions' => [
                    'AttendanceMachine.id = StudentAttendance.attendance_machine_id',
                ],
            ],
            [
                'table'      => 'student_attendance_settings',
                'alias'      => 'StudentAttendanceSetting',
                'type'       => 'INNER',
                'conditions' => [
                    'StudentAttendanceSetting.id = StudentAttendance.sas_id'
                ]
            ],
            [
                'table'      => 'users',
                'alias'      => 'User',
                'type'       => 'INNER',
                'conditions' => [
                    'User.id = Student.user_id',
                ],
            ],
        ];
        
        $condition = 'StudentAttendance.student_id IN(' .
            implode(',', $this->contactIds('student')) . ')';
        
        return Datatable::simple(
            $this->getModel(), $columns, $joins, $condition
        );
        
    }
    
    /**
     * 考勤中心首页
     *
     * @return Factory|View
     */
    function wIndex() {
    
        $user = Auth::user();
        # 禁止学生学生访问考勤记录
        abort_if(
            !$user || $user->group->name == '学生',
            HttpStatusCode::UNAUTHORIZED,
            __('messages.unauthorized')
        );
        # 如果不是监护人，则返回教职员工页面
        if (!$user->custodian) {
            return view(self::VIEW_NS . 'educator');
        }
        $students = $user->custodian->students;
        foreach ($students as $student) {
            $data = $this->wStat($student->id);
            $student->abnormal = count($data['adays']);
            $student->normal = count($data['ndays']);
            $student->schoolname = $student->class->grade->school->name;
            $student->studentname = $student->user->realname;
            $student->class_id = $student->squad->name;;
        }
        
        return view(self::VIEW_NS . 'custodian', [
            'students' => $students,
        ]);
        
    }
    
    /**
     * 返回指定学生的考勤记录
     *
     * @param null $studentId
     * @return JsonResponse|View
     */
    function wDetail($studentId = null) {
    
        $user = Auth::user();
        if (Request::method() == 'POST') {
            $studentId = Request::input('id');
            $type = Request::input('type');
            $date = Request::input('date');
            Request::validate([
                'id' => 'required|integer',
                'type' => ['required', 'string', Rule::in(['month', 'day']),],
                'date' => 'required|date'
            ]);
            abort_if(
                !in_array($studentId, $this->contactIds('student', $user, $user->educator->school_id)),
                HttpStatusCode::NOT_ACCEPTABLE,
                __('messages.invalid_argument')
            );
            if ($type == 'month') {
                $response = [
                    'data' => $this->wStat(
                        Request::get('id'),
                        $date, date('Y-m-t', strtotime($date))
                    )
                ];
            } else {
                list($ins, $outs) = $this->attendances($studentId, $date);
                $response = [
                    'date' => $date,
                    'ins'  => $ins,
                    'outs' => $outs
                ];
            }
        
            return response()->json($response);
        }
        $today = date('Y-m-d', time());
        list($ins, $outs) = $this->attendances($studentId, $today);
        $data = $this->wStat($studentId);
    
        if (Request::ajax() && Request::method() == 'GET') {
            return response()->json(['days' => $data]);
        }
    
        return view(self::VIEW_NS . 'detail', [
            'id'   => $studentId,
            'data' => $data,
            'date' => $today,
            'ins'  => $ins,
            'outs' => $outs,
        ]);
        
    }
    
    /**
     * 学生考勤饼图
     *
     * @return JsonResponse
     * @throws Throwable
     */
    function wChart() {
    
        $input = Request::all();
        if (isset($input['check'])) { return $this->wCheck(); }
        if (isset($input['classId'])) { return $this->wRule($input['classId']); }
        
        # 角色判断
        $user = Auth::user();
        abort_if(
            !$user || $user->group->name == '学生',
            HttpStatusCode::UNAUTHORIZED,
            __('messages.unauthorized')
        );
        $schoolId = $user->educator ? $user->educator->school_id : session('schoolId');
        
        # 对当前用户可见的所有班级ids
        $classIds = $this->classIds($schoolId);
        abort_if(
            empty(array_diff($classIds, [0])),
            HttpStatusCode::INTERNAL_SERVER_ERROR,
            __('messages.class.no_related_classes')
        );
        $classes = Squad::whereIn('id', $classIds)->get();
        $data['classNames'] = [];
        foreach ($classes as $class) {
            $data['classNames'][] = [
                'title' => $class->name,
                'value' => $class->id,
            ];
        }
        $data['classNames'] = array_unique(
            $data['classNames'], SORT_REGULAR
        );
        
        # 根据年级分组规则
        $gradeIds = $this->gradeIds($schoolId);
        $rules = StudentAttendanceSetting::whereIn('grade_id', $gradeIds)->get();
        $data['ruleNames'] = [];
        foreach ($rules as $rule) {
            $data['ruleNames'][] = [
                'title' => $rule->name,
                'value' => $rule->id,
            ];
        }
        
        # 获取饼图数据
        $data = !isset($input['squad'], $input['time'], $input['rule'])
            ? $this->defcharts($classIds, $data)
            : $this->fltcharts($input, $data);
        abort_if(
            !$data,
            HttpStatusCode::INTERNAL_SERVER_ERROR,
            '请加入相应的考勤规则！'
        );
    
        return response()->json([
            'data' => $data
        ]);
    
    }
    
    /** Helper functions -------------------------------------------------------------------------------------------- */
    /**
     * 返回指定班级对应的年级考勤规则
     *
     * @param $classId
     * @return JsonResponse
     */
    private function wRule($classId) {
    
        # 当前年级所有考勤规则，不分学期
        $gradeId = Squad::find($classId)->grade_id;
        $rules = StudentAttendanceSetting::whereGradeId($gradeId)->get();
        $data = [];
        foreach ($rules as $r) {
            $data[] = [
                'title' => $r->name,
                'value' => $r->id,
            ];
        }
        abort_if(
            empty($data),
            HttpStatusCode::INTERNAL_SERVER_ERROR,
            '该年级未设置考勤规则！'
        );
    
        return response()->json([
            'data' => $data,
        ]);
        
    }
    
    /**
     * 验证考勤规则
     * 
     * @return JsonResponse
     */
    private function wCheck() {
    
        $input = Request::all();
        $result = [
            'statusCode' => HttpStatusCode::OK, 
            'message' => ''
        ];
        if (isset($input['date'], $input['rule'])) {
            # 获取规则的星期
            $ruleDay = StudentAttendanceSetting::find($input['rule'])->day;
            $weekDay = self::WEEK_DAYS[date("w", strtotime($input['date']))];
        
            if ($ruleDay != $weekDay) {
                $result['statusCode'] = HttpStatusCode::INTERNAL_SERVER_ERROR;
                $result['message'] = '请选择和规则对应的星期！';
            }
        }
    
        return response()->json($result);

    }
    
    /**
     * 获取指定学生的最新考勤数据
     *
     * @param string $studentIds - 学生id列表
     * @param $start
     * @param $end
     * @return array
     */
    private function latestAttendances($studentIds, $start, $end) {
        
        return DB::select("
            SELECT
                max(t.id) id,
                t.student_id,
                substring_index(group_concat(t.status ORDER BY t.punch_time DESC), ',', 1) lastest,
                DATE(t.punch_time) day
          FROM (
                SELECT
                    sa.id,
                    sa.student_id,
                    sa.inorout,
                    sa.status,
                    sa.punch_time
            FROM
                student_attendances sa
            WHERE
                sa.punch_time >= '" . $start . "' AND
                sa.punch_time <= '" . $end . "'
            ORDER BY
                sa.student_id ASC,
                sa.punch_time DESC
          ) t
          WHERE
                t.student_id IN (" . $studentIds . ")
          GROUP BY
                t.student_id,
                day
        ");
        
    }
    
    /**
     * @return array
     */
    private function details(): array {
        
        $date = Request::input('date');
        $type = Request::input('type');
        $classId = Request::input('class_id');
        $startDate = date('Y-m-d H:i:s', strtotime($date));
        $endDate = date('Y-m-d H:i:s', strtotime($date) + 24 * 3600 - 1);
        $studentIds = Student::whereClassId($classId)->get()->pluck('id')->toArray();
        $results = []; # 统计结果
        $attendances = $this->latestAttendances(implode(',', $studentIds), $startDate, $endDate);
        if ($type == 'missed') {
            # 打过考勤的学生ids
            $nStudentIds = $this->whereIn('student_id', $studentIds)
                ->whereBetween('punch_time', [$startDate, $endDate])
                ->get()->pluck('student_id')->toArray();
            # 未打考勤的学生
            $mStudents = Student::whereIn('id', array_diff($studentIds, $nStudentIds))->get();
            if ($mStudents) {
                foreach ($mStudents as $student) {
                    $userIds = array_column(json_decode($student->custodians), 'user_id');
                    if ($student->custodians) {
                        $custodians = User::whereIn('id', $userIds)->get()->pluck('realname')->toArray();
                    } else {
                        $custodians = [];
                    }
                    if (json_decode($student->user['mobiles'])) {
                        $mobiles = array_column(json_decode($student->user['mobiles']), 'mobile');
                    } else {
                        $mobiles = [];
                    }
                    $results[] = [
                        'name'       => $student->user['realname'],
                        'custodian'  => $custodians,
                        'mobile'     => $mobiles,
                        'punch_time' => '',
                        'inorout'    => '',
                    ];
                }
            }
        }
        if ($attendances) {
            switch ($type) {
                case 'normal':
                    $saIds = [];
                    foreach ($attendances as $a) {
                        if ($a->lastest) {
                            $saIds[] = $a->id;
                        }
                    }
                    $sas = $this->whereIn('id', $saIds)->get();
                    foreach ($sas as $sa) {
                        $userIds = array_column($sa->student->custodians->toArray(), 'user_id');
                        $results[] = [
                            'name'       => $sa->student->user->realname,
                            'custodian'  => User::whereIn('id', $userIds)->get()->pluck('realname')->toArray(),
                            'mobile'     => array_column($sa->student->user->mobiles->toArray(), 'mobile'),
                            'punch_time' => $sa->punch_time,
                            'inorout'    => $sa->inorout == 1 ? '进' : '出',
                        ];
                    }
                    break;
                case 'abnormal':
                    $saIds = [];
                    foreach ($attendances as $a) {
                        if ($a->lastest == 0) {
                            $saIds[] = $a->id;
                        }
                    }
                    $sas = $this->whereIn('id', $saIds)->get();
                    foreach ($sas as $sa) {
                        $userIds = array_column($sa->student->custodians->toArray(), 'user_id');
                        $custodians = User::whereIn('id', $userIds)->get()->pluck('realname')->toArray();
                        $results[] = [
                            'name'       => $sa->student->user->realname,
                            'custodian'  => $custodians,
                            'mobile'     => array_column($sa->student->user->mobiles->toArray(), 'mobile'),
                            'punch_time' => $sa->punch_time,
                            'inorout'    => $sa->inorout ? '进' : '出',
                        ];
                    }
                    break;
                default:
                    break;
            }
        }

        return $results;
        
    }
    
    /**
     * 缓存导出数据
     *
     * @param $details
     */
    private function cache($details): void {
        
        $rows = [self::EXPORT_TITLES];
        foreach ($details as $detail) {
            $rows[] = [
                $detail['name'],
                implode(',', $detail['custodian']),
                implode(',', $detail['mobile']),
                $detail['punch_time'],
                $detail['inorout'] ? '进' : '出'
            ];
        }
        
        session(['sa_details' => $rows]);
        
    }
    
    /**
     * 获取指定学生的考勤数据（异常和正常）
     *
     * @param $studentId
     * @param $start
     * @param $end
     * @return array
     */
    private function wStat($studentId, $start = null, $end = null) {
    
        # 当月第一天
        $start = $start ?? date('Y-m-01 00:00:00', strtotime(date('Y-m-d')));
        # 当月最后一天
        $end = $end ?? date('Y-m-t 23:59:59', strtotime($start));
        $ids = $aDays = $nDays = [];
        # 查询考勤异常的数据
        $abnormals = $this->whereStudentId($studentId)
            ->whereBetween('punch_time', [$start, $end])
            ->where('status', 0)->get();
        foreach ($abnormals as $a) {
            # 查询考勤异常当天的所有数据
            $date = date('Y-m-d', strtotime($a->punch_time));
            $attendances = $this->whereDate('punch_time', $date)
                ->whereBetween('punch_time', [$start, $end])
                ->where('student_id', $studentId)->get();
            foreach ($attendances as $attendance) {
                $ids[] = $attendance->id;
                $aDays[] = date('Y-m-d', strtotime($attendance->punch_time));
            }
            $aDays = array_unique($aDays);
        }
        # 查询考勤正常天数的数据
        $normals = $this->whereNotIn('id', $ids)
            ->whereBetween('punch_time', [$start, $end])
            ->where('student_id', $studentId)->get();
        foreach ($normals as $n) {
            $nDays[] = date('Y-m-d', strtotime($n->punch_time));
        }
        $nDays = array_unique($nDays);
        
        return [
            'aDays' => $aDays,
            'nDays' => $nDays,
            'aSum' => count($aDays),
            'nSum' => count($nDays)
        ];
        
    }
    
    /**
     * 饼图默认数据填入
     *
     * @param $classIds
     * @param $data
     * @return bool|JsonResponse
     * @throws Throwable
     */
    private function defcharts($classIds, $data) {
        
        #如果条件为空 默认当天 该老师对应的第一个班级，第一个规则
        $class = Squad::whereId(head($classIds))->first();
        $grade = $class->grade;
        $schoolSemesters = Semester::whereSchoolId($grade->school_id)
            ->where('enabled', 1)->get();
        $students = $class->students;
        $studentIds = [];
        $date = date('Y-m-d', time());
        foreach ($students as $student) {
            $studentIds[] = $student->id;
        }
        $weekDay = self::WEEK_DAYS[date("w", time())];
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
        #这个星期这个年级没有设置对应的规则
        if (!$rule) {
            return false;
        }
        #同一个学生这段时间打了多次记录 取这段时间最晚的一条记录
        $attendances = $this->where('sas_id', $rule->id)
            ->whereIn('student_id', $studentIds)
            ->whereNotNull('sas_id')
            ->whereDate('punch_time', $date)
            ->orderBy('punch_time', 'desc')
            ->get()
            ->unique('student_id');
        $normalRecords = $attendances->where('status', 1)->count();
        $abnormalRecords = $attendances->where('status', 0)->count();
        $noRecords = count($studentIds) - $normalRecords - $abnormalRecords;
        $data['charts'] = [
            ['name' => '打卡', 'value' => $normalRecords],
            ['name' => '异常', 'value' => $abnormalRecords],
            ['name' => '未打卡', 'value' => $noRecords],
        ];
        #处理列表页
        $data['view'] = $this->attendList(
            $studentIds, $attendances->where('status', 1),
            $attendances->where('status', 0), $attendances);
        
        return !empty($data) ? $data : false;
        
    }
    
    /**
     * 获取指定学生指定日期的所有考勤记录
     *
     * @param $studentId
     * @param $date
     * @return array
     */
    private function attendances($studentId, $date) {
        
        $attendances = $this->whereStudentId($studentId)
            ->whereDate('punch_time', $date)
            ->orderBy('punch_time', 'ASC')
            ->get()->groupBy('inorout');
        
        return [$attendances[1], head($attendances)];
        
    }
    
    /**
     * 学生考勤列表
     *
     * @param $studentIds
     * @param $normalAttend
     * @param $abnormalAttend
     * @param $attendances
     * @return mixed
     * @throws Throwable
     */
    private function attendList($studentIds, $normalAttend, $abnormalAttend, $attendances) {
        
        # 考勤正常的学生列表
        $normalList = [];
        foreach ($normalAttend as $normal) {
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
        # 考勤异常的学生列表
        $abnormalList = [];
        foreach ($abnormalAttend as $normal) {
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
        $data = view(self::VIEW_NS . 'list', [
            'normallist'   => $normalList,
            'abnormallist' => $abnormalList,
            'nostulist'    => $noStuList,
        ])->render();
        
        return $data;
    }
    
    /**
     * 饼图筛选填入数据
     *
     * @param $input
     * @param $data
     * @return bool|JsonResponse
     * @throws Throwable
     */
    private function fltcharts($input, $data) {
        
        #传过来的值包含班级id 规则id 日期
        $squad = Squad::whereId($input['squad'])->first();
        $students = $squad->students;
        $studentIds = [];
        foreach ($students as $student) {
            $studentIds[] = $student->id;
        }
        $attendances = $this->where('sas_id', $input['rule'])
            ->whereIn('student_id', $studentIds)
            ->whereNotNull('sas_id')
            ->whereDate('punch_time', $input['date'])
            ->orderBy('punch_time', 'desc')
            ->get()->unique('student_id');
        $normalRecords = $attendances->where('status', 1)->count();
        $abnormalRecords = $attendances->where('status', 0)->count();
        $noRecords = count($studentIds) - $normalRecords - $abnormalRecords;
        $data['charts'] = [
            ['name' => '打卡', 'value' => $normalRecords],
            ['name' => '异常', 'value' => $abnormalRecords],
            ['name' => '未打卡', 'value' => $noRecords],
        ];
        #处理列表页
        $data['view'] = $this->attendList(
            $studentIds, $attendances->where('status', 1),
            $attendances->where('status', 0), $attendances);
        
        return !empty($data) ? $data : false;
        
    }
    
}
