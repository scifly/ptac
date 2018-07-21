<?php
namespace App\Models;

use App\Facades\Datatable;
use App\Helpers\Constant;
use App\Helpers\HttpStatusCode;
use App\Helpers\ModelTrait;
use App\Helpers\Snippet;
use App\Http\Requests\StudentAttendanceRequest;
use App\Jobs\SendMessage;
use Carbon\Carbon;
use Eloquent;
use Exception;
use Illuminate\Contracts\View\Factory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Throwable;
use Validator;

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
 * @property-read StudentAttendanceSetting $studentAttendanceSetting
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
 * @property-read \App\Models\Media $media
 */
class StudentAttendance extends Model {
    
    use ModelTrait;
    
    const EXPORT_TITLES = [
        '姓名', '监护人', '手机号码', '打卡时间', '进/出',
    ];
    const WEEK_DAYS = ['星期日', '星期一', '星期二', '星期三', '星期四', '星期五', '星期六'];
    const VIEW_NS = 'wechat.attendance.';
    protected $table = 'student_attendances';
    protected $fillable = [
        'id', 'student_id', 'punch_time', 'sas_id',
        'inorout', 'attendance_machine_id', 'media_id',
        'status', 'longitude', 'latitude', 'created_at',
        'updated_at',
    ];
    
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
     * 学生考勤记录列表
     *
     * @return array
     */
    function index() {
        
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
                    }
                    
                    return $d
                        ? sprintf(Snippet::BADGE_GREEN, '进')
                        : sprintf(Snippet::BADGE_RED, '出');
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
                    'StudentAttendanceSetting.id = StudentAttendance.sas_id',
                ],
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
     * 批量保存学生考勤记录
     *
     * @return JsonResponse
     * @throws Exception
     */
    function store() {
        
        try {
            DB::transaction(function () {
                $data = Request::input('data');
                abort_if(
                    !($school = School::find($data['school_id'])),
                    HttpStatusCode::NOT_FOUND,
                    __('messages.school.not_found')
                );
                foreach ($data as &$datum) {
                    $datum['inorout'] = $datum['inorout'] ?? 2;
                    $datum['longitude'] = $datum['longitude'] ?? 0;
                    $datum['latitude'] = $datum['latitude'] ?? 0;
                    $datum['machineid'] = $datum['attendid'];
                    $datum['media_id'] = 0;
                    abort_if(
                        !Validator::make($datum, (new StudentAttendanceRequest)->rules()),
                        HttpStatusCode::NOT_ACCEPTABLE,
                        __('messages.not_acceptable')
                    );
                    $student = Student::whereIn('class_id', $school->classes->pluck('id')->toArray())
                        ->where('student_number', $datum['student_number'])->first();
                    abort_if(
                        !$student,
                        HttpStatusCode::NOT_FOUND,
                        __('messages.student.not_found')
                    );
                    $dateTime = strtotime($datum['punch_time']);
                    $day = Constant::WEEK_DAYS[date('w', $dateTime)];
                    $strDateTime = date('Y-m-d', $dateTime);
                    $semester = Semester::where('start_date', '<=', $strDateTime)
                        ->where('end_date', '>=', $strDateTime)
                        ->where('enabled', 1)
                        ->first();
                    abort_if(
                        !$semester,
                        HttpStatusCode::NOT_FOUND,
                        __('messages.semester.not_found')
                    );
                    $machine = AttendanceMachine::whereMachineid($datum['machineid'])
                        ->where('school_id', $school->id)->first();
                    abort_if(
                        !$machine,
                        HttpStatusCode::NOT_FOUND,
                        __('messages.attendance_machine.not_found')
                    );
                    $sases = StudentAttendanceSetting::whereGradeId($student->squad->grade_id)
                        ->where('semester_id', $semester->id)
                        ->where('day', $day)->get();
                    abort_if(
                        empty($sases->toArray()),
                        HttpStatusCode::NOT_FOUND,
                        __('messages.sas.not_found')
                    );
                    $punchTime = date('Y-m-d H:i:s', $dateTime);
                    $status = 0; # 考勤异常
                    $sasId = 0;
                    foreach ($sases as $sas) {
                        $sasId = $sas->id;
                        if ($punchTime <= $sas->end && $punchTime >= $sas->start) {
                            $status = 1;
                            break;
                        }
                    }
                    $sa = $this->create([
                        'student_id'            => $student->id,
                        'sas_id'                => $sasId,
                        'punch_time'            => $punchTime,
                        'inorout'               => $datum['inorout'],
                        'attendance_machine_id' => $machine->id,
                        'status'                => $status,
                        'longitude'             => $datum['longitude'],
                        'latitude'              => $datum['latitude'],
                        'media_id'              => $datum['media_id'],
                    ]);
                    $userIds = $student->custodians->pluck('user_id')->toArray();
                    # todo -
                    list($smsUserIds, $wechatUserIds) = array_pluck(
                        User::get(['id', 'subscribed'])
                            ->whereIn('id', $userIds)
                            ->groupBy('subscribed')
                            ->toArray(),
                        '*.id'
                    );
                    $data = [
                        'dept_ids'        => [],
                        'message_type_id' => MessageType::whereName('考勤消息')->first()->id,
                    ];
                    $content = strtr(
                        $sa->studentAttendanceSetting->msg_template,
                        [
                            '{name}'   => $student->user->realname,
                            '{time}'   => $sa->punch_time,
                            '{rule}'   => $sa->studentAttendanceSetting->name,
                            '{status}' => $sa->status == 1 ? '正常' : '异常',
                        ]
                    );
                    $corp = $school->corp;
                    $apps = [App::whereCorpId($school->corp_id)->whereName('考勤中心')->first()->toArray()];
                    # 需要接收微信消息的用户
                    if (!empty($wechatUserIds)) {
                        SendMessage::dispatch(array_merge($data, [
                            'user_ids' => $wechatUserIds,
                            'type'     => 'text',
                            'text'     => ['content' => $content],
                        ]), null, $corp, $apps);
                    }
                    # 需要接收短信消息的用户
                    if (!empty($smsUserIds)) {
                        SendMessage::dispatch(array_merge($data, [
                            'user_ids' => $smsUserIds,
                            'type'     => 'sms',
                            'sms'      => $content,
                        ]), null, $corp, $apps);
                    }
                }
            });
        } catch (Exception $e) {
            throw $e;
        }
        
        return response()->json([
            'statusCode' => HttpStatusCode::OK,
            'message'    => __('messages.ok'),
        ]);
        
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
            'end_date'   => 'required|date|greater_than_or_equal_to:start_date',
        ]);
        $classId = Request::input('class_id') ?? head($this->classIds());
        $startDate = Request::input('start_date') ?? date('Y-m-d', strtotime('-7 day'));
        $endDate = Request::input('end_date') ?? date('Y-m-d');
        $days = Carbon::createFromTimestamp(strtotime($startDate))->diffInDays(
                Carbon::createFromTimestamp(strtotime($endDate))
            ) + 1;
        # 指定班级所有学生的id
        $studentIds = Student::whereClassId($classId)->get()->pluck('id')->toArray();
        if (empty($studentIds)) {
            return [];
        }
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
                'date'     => $date,
                'all'      => $all,
                'normal'   => $normal,
                'abnormal' => $abnormal,
                'missed'   => $all - $normal - $abnormal,
            ];
        }
        
        return $results;
        
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
    
    /** 微信端 ------------------------------------------------------------------------------------------------------- */
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
                $detail['inorout'] ? '进' : '出',
            ];
        }
        session(['sa_details' => $rows]);
        
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
        
        return view(
            self::VIEW_NS . ($user->custodian ? 'custodian' : 'educator')
        );
        
    }
    
    /**
     * 获取指定学生的考勤数据（异常和正常）
     *
     * @param $studentId
     * @param $start
     * @param $end
     * @return array
     */
    function wStat($studentId, $start = null, $end = null) {
        
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
            'aSum'  => count($aDays),
            'nSum'  => count($nDays),
        ];
        
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
            $result = Validator::make(Request::all(), [
                'id'   => 'required|integer',
                'type' => ['required', 'string', Rule::in(['month', 'day']),],
                'date' => 'required|date',
            ]);
            abort_if(
                $result->failed() || !in_array($studentId, $this->contactIds('student', $user)),
                HttpStatusCode::NOT_ACCEPTABLE,
                __('messages.invalid_argument')
            );
            if ($type == 'month') {
                $response = [
                    'data' => $this->wStat(
                        Request::get('id'),
                        $date, date('Y-m-t', strtotime($date))
                    ),
                ];
            } else {
                list($ins, $outs) = $this->attendances($studentId, $date);
                $response = [
                    'date' => $date,
                    'ins'  => $ins,
                    'outs' => $outs,
                ];
            }
            
            return response()->json($response);
        }
        $today = date('Y-m-d');
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
            'schoolname' => School::find(session('schoolId'))->name
        ]);
        
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
            ->get()->groupBy('inorout')->toArray();
        
        return !empty($attendances)
            ? [$attendances[1], head($attendances)]
            : [[], []];
        
    }
    
    /** Helper functions -------------------------------------------------------------------------------------------- */

    /**
     * 学生考勤饼图
     *
     * @return JsonResponse
     * @throws Throwable
     */
    function wChart() {
        
        if (Request::has('action')) {
            return Request::has('classId')
                ? $this->wRule()
                : $this->wCheck();
        }
        # 角色判断
        $user = Auth::user();
        abort_if(
            !$user || $user->group->name == '学生',
            HttpStatusCode::UNAUTHORIZED,
            __('messages.unauthorized')
        );
        $schoolId = $user->educator ? $user->educator->school_id : session('schoolId');
        # 对当前用户可见的所有班级id
        abort_if(
            empty(array_diff($this->classIds($schoolId), [0])),
            HttpStatusCode::INTERNAL_SERVER_ERROR,
            __('messages.class.no_related_classes')
        );
        
        return response()->json($this->chart());
        
    }
    
    /**
     * 验证考勤规则
     *
     * @return JsonResponse
     */
    private function wCheck() {
        
        # 获取规则的星期
        $ruleDay = StudentAttendanceSetting::find(Request::input('sasId'))->day;
        $weekDay = self::WEEK_DAYS[date('w', strtotime(Request::input('startDate')))];
        abort_if(
            $ruleDay != $weekDay,
            HttpStatusCode::NOT_ACCEPTABLE,
            __('messages.student_attendance.weekday_mismatched')
        );
        
        return response()->json([
            'message' => __('messages.student_attendance.authenticated')
        ]);
        
    }
    
    /**
     * 返回指定班级对应的年级考勤规则
     *
     * @return JsonResponse
     */
    private function wRule() {
        
        # 当前年级所有考勤规则，不分学期
        $gradeId = Squad::find(Request::input('classId'))->grade_id;
        $sases = StudentAttendanceSetting::whereGradeId($gradeId)->pluck('name', 'id')->toArray();
        abort_if(
            empty($sases),
            HttpStatusCode::NOT_ACCEPTABLE,
            __('messages.student_attendance.not_available')
        );
        $options = '';
        foreach ($sases as $key => $value) {
            $options .= '<option value="' . $key . '">' . $value . '</option>';
        }
        
        return response()->json([
            'options' => $options,
        ]);
        
    }
    
    /**
     * 获取饼图数据
     *
     * @return mixed
     * @throws Throwable
     */
    private function chart() {
        
        if (!Request::has('classId')) {
            $schoolId = session('schoolId');
            $class = Squad::find(head($this->classIds($schoolId)));
            $date = date('Y-m-d', time());
            $semesters = Semester::whereSchoolId($schoolId)->where('enabled', 1)
                ->get()->reject(
                    function (Semester $semester) use ($date) {
                        return !(
                            $semester->start_date <= $date &&
                            $semester->end_date >= $date
                        );
                    }
                );
            $semester = $semesters->isNotEmpty() ? $semesters->first() : null;
            $weekDay = self::WEEK_DAYS[date('w', time())];
            $sas = StudentAttendanceSetting::whereGradeId($class->grade_id)
                ->where('semester_id', $semester->id)
                ->where('day', $weekDay)
                ->first();
        } else {
            $class = Squad::find(Request::input('classId'));
            $sas = StudentAttendanceSetting::find(Request::input('sasId'));
            $date = Request::input('startDate');
        }
        abort_if(
            !$sas, HttpStatusCode::NOT_ACCEPTABLE,
            __('messages.student_attendance.not_available')
        );
        # 指定班级所有学生id
        $studentIds = $class->students->pluck('id')->toArray();
        # 如果同一个学生在指定考勤时段内多次打卡，则取该时段内最晚的一条记录
        $attendances = StudentAttendance::whereSasId($sas->id)
            ->whereIn('student_id', $studentIds)
            ->whereNotNull('sas_id')
            ->whereDate('punch_time', $date)
            ->orderBy('punch_time', 'desc')
            ->get()->unique('student_id');
        $normals = $attendances->where('status', 1)->count();
        $abnormals = $attendances->where('status', 0)->count();
        $missed = count($studentIds) - $normals - $abnormals;
        
        return [
            'chart' => [
                ['name' => '打卡', 'value' => $normals],
                ['name' => '异常', 'value' => $abnormals],
                ['name' => '未打卡', 'value' => $missed],
            ],
            'view' => $this->lists($studentIds, $attendances)
        ];
        
    }
    
    /**
     * 学生考勤列表
     *
     * @param array $studentIds
     * @param Collection|StudentAttendance[] $attendances
     * @return mixed
     * @throws Throwable
     */
    private function lists($studentIds, $attendances) {
    
        // 未打卡的学生
        $ids = $attendances->pluck('student_id')->toArray();
        $studentIds = array_diff($studentIds, $ids);
        $students = Student::whereIn('id', $studentIds)->get();
    
        list($normals, $abnormals, $missed) = array_map(
            function (Collection $objects) {
                $list = [];
                foreach ($objects as $object) {
                    $class = class_basename($object);
                    $student = $class == 'Student' ? $object : $object->student;
                    $custodians = [];
                    $mobiles = [];
                    foreach ($student->custodians as $custodian) {
                        $custodians[] = $custodian->user->realname;
                        $mobiles = array_merge(
                            $mobiles, $custodian->user->mobiles->pluck('mobile')->toArray()
                        );
                    }
                    $item = [
                        'student'    => $student->user->realname,
                        'custodians' => $custodians,
                        'mobiles'    => $mobiles,
                    ];
                    $list[] = $class == 'Student'
                        ? $item
                        : array_merge($item, ['punch_time' => $object->punch_time]);
                }
                
                return $list;
            },
            [
                $attendances->where('status', 1),
                $attendances->where('status', 0),
                $students
            ]
        );
        
        return view(self::VIEW_NS . 'list', [
            'normals'   => $normals,
            'abnormals' => $abnormals,
            'missed'    => $missed,
        ])->render();
        
    }
    
}
