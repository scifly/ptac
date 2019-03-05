<?php
namespace App\Models;

use App\Facades\Datatable;
use App\Helpers\{Constant, HttpStatusCode, ModelTrait, Snippet};
use App\Http\Requests\StudentAttendanceRequest;
use App\Jobs\SendMessage;
use Carbon\Carbon;
use Eloquent;
use Exception;
use Illuminate\Contracts\View\Factory;
use Illuminate\Database\Eloquent\{Collection, Model, Relations\BelongsTo};
use Illuminate\Database\Query\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\{Auth, DB, Request, Session};
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use ReflectionException;
use Throwable;
use Validator;

/**
 * App\Models\StudentAttendance 学生考勤记录
 *
 * @property int $id
 * @property int $student_id 学生ID
 * @property int $sas_id 关联规则id
 * @property string $clocked_at 打卡时间
 * @property int $direction 进或出
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
 * @property-read Media $media
 * @method static Builder|StudentAttendance whereAttendanceMachineId($value)
 * @method static Builder|StudentAttendance whereCreatedAt($value)
 * @method static Builder|StudentAttendance whereId($value)
 * @method static Builder|StudentAttendance whereDirection($value)
 * @method static Builder|StudentAttendance whereLatitude($value)
 * @method static Builder|StudentAttendance whereLongitude($value)
 * @method static Builder|StudentAttendance whereMediaId($value)
 * @method static Builder|StudentAttendance whereClockedAt($value)
 * @method static Builder|StudentAttendance whereSasId($value)
 * @method static Builder|StudentAttendance whereStatus($value)
 * @method static Builder|StudentAttendance whereStudentId($value)
 * @method static Builder|StudentAttendance whereUpdatedAt($value)
 * @method static Builder|StudentAttendance newModelQuery()
 * @method static Builder|StudentAttendance newQuery()
 * @method static Builder|StudentAttendance query()
 * @mixin Eloquent
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
        'id', 'student_id', 'clocked_at', 'sas_id',
        'direction', 'attendance_machine_id', 'media_id',
        'status', 'longitude', 'latitude', 'created_at',
        'updated_at',
    ];
    
    /**
     * 返回对应的考勤机对象
     *
     * @return BelongsTo
     */
    function attendanceMachine() { return $this->belongsTo('App\Models\AttendanceMachine'); }
    
    /**
     * 返回对应的学生对象
     *
     * @return BelongsTo
     */
    function student() { return $this->belongsTo('App\Models\Student'); }
    
    /**
     * 返回对应的媒体对象
     *
     * @return BelongsTo
     */
    function media() { return $this->belongsTo('App\Models\Media'); }
    
    /**
     * 返回对应的学生考勤设置对象
     *
     * @return BelongsTo
     */
    function studentAttendanceSetting() {
        
        return $this->belongsTo('App\Models\StudentAttendanceSetting', 'sas_id', 'id');
        
    }
    
    /**
     * 学生考勤记录列表
     *
     * @return array
     * @throws ReflectionException
     */
    function index() {
        
        $columns = [
            ['db' => 'StudentAttendance.id', 'dt' => 0],
            ['db' => 'User.realname', 'dt' => 1],
            ['db' => 'StudentAttendance.clocked_at', 'dt' => 2, 'dr' => true],
            ['db' => 'StudentAttendanceSetting.name as sasname', 'dt' => 3],
            ['db' => 'AttendanceMachine.name as machinename', 'dt' => 4],
            [
                'db'        => 'StudentAttendance.direction', 'dt' => 5,
                'formatter' => function ($d) {
                    if ($d == 2) return '';
    
                    return sprintf(
                        Snippet::BADGE,
                        $d ? 'text-green' : 'text-red',
                        $d ? '进' : '出'
                    );
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
     * 批量保存学生考勤记录（接口）
     *
     * @return JsonResponse
     * @throws Exception
     * @throws Throwable
     */
    function store() {
        
        try {
            DB::transaction(function () {
                $post = json_decode(Request::getContent(), true);
                $data = $post['data'];
                $school = School::find($post['school_id']);
                abort_if(
                    !$school,
                    HttpStatusCode::NOT_FOUND,
                    __('messages.school.not_found')
                );
                $commTypeId = CommType::whereName('微信')->first()->id;
                $mediaTypeId = MediaType::whereName('text')->first()->id;
                $app = $this->app($school->corp_id);
                $message = new Message;
                foreach ($data as &$datum) {
                    $datum['direction'] = $datum['direction'] ?? 2;
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
                        ->where('sn', $datum['sn'])->first();
                    abort_if(
                        !$student,
                        HttpStatusCode::NOT_FOUND,
                        __('messages.student.not_found')
                    );
                    $dateTime = strtotime($datum['clocked_at']);
                    $day = Constant::WEEK_DAYS[date('w', $dateTime)];
                    $strDateTime = date('Y-m-d', $dateTime);
                    $semester = Semester::where([
                        ['start_date', '<=', $strDateTime],
                        ['end_date', '>=', $strDateTime],
                        ['school_id', '=', 1],
                        ['enabled', '=', 1],
                    ])->first();
                    abort_if(
                        !$semester,
                        HttpStatusCode::NOT_FOUND,
                        __('messages.semester.not_found')
                    );
                    $machine = AttendanceMachine::where([
                        'machineid' => $datum['machineid'],
                        'school_id' => $school->id,
                    ])->first();
                    abort_if(
                        !$machine,
                        HttpStatusCode::NOT_FOUND,
                        __('messages.attendance_machine.not_found')
                    );
                    $sases = StudentAttendanceSetting::where([
                        'grade_id'    => $student->squad->grade_id,
                        'semester_id' => $semester->id,
                        'day'         => $day,
                    ])->get();
                    abort_if(
                        empty($sases->toArray()),
                        HttpStatusCode::NOT_FOUND,
                        __('messages.sas.not_found')
                    );
                    $clockedAt = date('Y-m-d H:i:s', $dateTime);
                    $status = 0; # 考勤异常
                    $sasId = 0;
                    foreach ($sases as $sas) {
                        $sasId = $sas->id;
                        if ($clockedAt <= $sas->end && $clockedAt >= $sas->start) {
                            $status = 1;
                            break;
                        }
                    }
                    # 保存考勤记录
                    $sa = $this->create(
                        array_combine(Constant::SA_FIELDS, [
                            $student->id, $sasId, $clockedAt, $datum['direction'],
                            $machine->id, $datum['media_id'], $status,
                            $datum['longitude'], $datum['latitude'],
                        ])
                    );
                    # 创建并发送消息
                    $msl = (new MessageSendingLog)->store([
                        'read_count'      => 0,
                        'received_count'  => 0,
                        'recipient_count' => 0,
                    ]);
                    $userIds = $student->custodians->pluck('user_id');
                    $userids = User::whereIn('id', $userIds)->pluck('userid')->toArray();
                    $content = [
                        'touser'  => implode('|', $userids),
                        'toparty' => '',
                        'agentid' => $app->agentid,
                        'msgtype' => 'text',
                        'text'    => [
                            'content' => strtr(
                                $sa->studentAttendanceSetting->msg_template,
                                [
                                    '{name}'   => $student->user->realname,
                                    '{time}'   => $sa->clocked_at,
                                    '{rule}'   => $sa->studentAttendanceSetting->name,
                                    '{status}' => $sa->status == 1 ? '正常' : '异常',
                                ]
                            ),
                        ],
                    ];
                    $messages[] = $message->create(
                        array_combine(Constant::MESSAGE_FIELDS, [
                            $commTypeId, $mediaTypeId, $app->id, $msl->id, '考勤消息(文本)',
                            json_encode($content, JSON_UNESCAPED_UNICODE),
                            0, 0, 1, 'http://', 0, 0, 0, 0, 1,
                        ])
                    );
                }
                empty($messages) ?: SendMessage::dispatch($messages);
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
     * @throws Throwable
     */
    function remove($id = null) {
        
        return $this->purge(['StudentAttendance'], 'id', 'purge', $id);
        
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
        if (empty($studentIds)) return [];
        $attendances = $this->latestAttendances(
            implode(',', $studentIds),
            $startDate,
            $endDate
        );
        $normals = [];
        $abnormals = [];
        if (!empty($attendances)) {
            foreach ($attendances as $key => &$val) {
                $val->lastest
                    ? (isset($normals[$val->day]) ? $normals[$val->day]++ : $normals[$val->day] = 1)
                    : (isset($abnormals[$val->day]) ? $abnormals[$val->day]++ : $abnormals[$val->day] = 1);
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
                substring_index(group_concat(t.status ORDER BY t.clocked_at DESC), ',', 1) lastest,
                DATE(t.clocked_at) day
          FROM (
                SELECT
                    sa.id,
                    sa.student_id,
                    sa.direction,
                    sa.status,
                    sa.clocked_at
            FROM
                student_attendances sa
            WHERE
                sa.clocked_at >= '" . $start . "' AND
                sa.clocked_at <= '" . $end . "'
            ORDER BY
                sa.student_id ASC,
                sa.clocked_at DESC
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
                ->whereBetween('clocked_at', [$startDate, $endDate])
                ->get()->pluck('student_id')->toArray();
            # 未打考勤的学生
            $mStudents = Student::whereIn('id', array_diff($studentIds, $nStudentIds))->get();
            if ($mStudents) {
                foreach ($mStudents as $student) {
                    $userIds = array_column(json_decode($student->custodians), 'user_id');
                    $custodians = !$student->custodians ? []
                        : User::whereIn('id', $userIds)->pluck('realname')->toArray();
                    $mobiles = !json_decode($student->user['mobiles']) ? []
                        : array_column(json_decode($student->user['mobiles']), 'mobile');
                    $results[] = [
                        'name'       => $student->user['realname'],
                        'custodian'  => $custodians,
                        'mobile'     => $mobiles,
                        'clocked_at' => '',
                        'direction'  => '',
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
                            'clocked_at' => $sa->clocked_at,
                            'direction'  => $sa->direction == 1 ? '进' : '出',
                        ];
                    }
                    break;
                case 'abnormal':
                    $saIds = [];
                    foreach ($attendances as $a) {
                        if ($a->lastest == 0) $saIds[] = $a->id;
                    }
                    $sas = $this->whereIn('id', $saIds)->get();
                    foreach ($sas as $sa) {
                        $userIds = array_column($sa->student->custodians->toArray(), 'user_id');
                        $custodians = User::whereIn('id', $userIds)->get()->pluck('realname')->toArray();
                        $results[] = [
                            'name'       => $sa->student->user->realname,
                            'custodian'  => $custodians,
                            'mobile'     => array_column($sa->student->user->mobiles->toArray(), 'mobile'),
                            'clocked_at' => $sa->clocked_at,
                            'direction'  => $sa->direction ? '进' : '出',
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
                $detail['clocked_at'],
                $detail['direction'] ? '进' : '出',
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
        $role = $user->role();
        # 禁止学生学生访问考勤记录
        abort_if(
            !$user || $role == '学生',
            HttpStatusCode::UNAUTHORIZED,
            __('messages.unauthorized')
        );
        
        return view(
            self::VIEW_NS . ($role == '监护人' ? 'custodian' : 'educator')
        );
        
    }
    
    /**
     * 返回指定学生的考勤记录
     *
     * @param null $studentId
     * @return JsonResponse|View
     * @throws ReflectionException
     */
    function wDetail($studentId = null) {
        
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
                $result->failed() || !in_array($studentId, $this->contactIds('student', Auth::user())),
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
            'id'         => $studentId,
            'data'       => $data,
            'date'       => $today,
            'ins'        => $ins,
            'outs'       => $outs,
            'schoolname' => School::find(session('schoolId'))->name,
        ]);
        
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
            ->whereBetween('clocked_at', [$start, $end])
            ->where('status', 0)->get();
        foreach ($abnormals as $a) {
            # 查询考勤异常当天的所有数据
            $date = date('Y-m-d', strtotime($a->clocked_at));
            $attendances = $this->whereDate('clocked_at', $date)
                ->whereBetween('clocked_at', [$start, $end])
                ->where('student_id', $studentId)->get();
            foreach ($attendances as $attendance) {
                $ids[] = $attendance->id;
                $aDays[] = date('Y-m-d', strtotime($attendance->clocked_at));
            }
            $aDays = array_unique($aDays);
        }
        # 查询考勤正常天数的数据
        $normals = $this->whereNotIn('id', $ids)
            ->whereBetween('clocked_at', [$start, $end])
            ->where('student_id', $studentId)->get();
        foreach ($normals as $n) {
            $nDays[] = date('Y-m-d', strtotime($n->clocked_at));
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
     * 获取指定学生指定日期的所有考勤记录
     *
     * @param $studentId
     * @param $date
     * @return array
     */
    private function attendances($studentId, $date) {
        
        $attendances = $this->whereStudentId($studentId)
            ->whereDate('clocked_at', $date)
            ->orderBy('clocked_at', 'ASC')
            ->get()->groupBy('direction')->toArray();
        
        return !empty($attendances)
            ? [$attendances[1], head($attendances)]
            : array_fill(0, 2, []);
        
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
            !$user || $user->role() == '学生',
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
            'message' => __('messages.student_attendance.authenticated'),
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
            ->whereDate('clocked_at', $date)
            ->orderBy('clocked_at', 'desc')
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
            'view'  => $this->lists($studentIds, $attendances),
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
                        : array_merge($item, ['clocked_at' => $object->clocked_at]);
                }
                
                return $list;
            },
            [
                $attendances->where('status', 1),
                $attendances->where('status', 0),
                $students,
            ]
        );
        
        return view(self::VIEW_NS . 'list', [
            'normals'   => $normals,
            'abnormals' => $abnormals,
            'missed'    => $missed,
        ])->render();
        
    }
    
    /**
     * 移除指定学生的考勤记录
     *
     * @param $studentId
     * @throws Exception
     */
    function removeStudent($studentId) {
        
        $this->whereStudentId($studentId)->delete();
        
    }
    
}
