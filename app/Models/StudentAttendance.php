<?php
namespace App\Models;

use App\Facades\DatatableFacade as Datatable;
use App\Facades\Wechat;
use App\Helpers\Constant;
use App\Helpers\HttpStatusCode;
use App\Helpers\ModelTrait;
use App\Helpers\Snippet;
use Carbon\Carbon;
use Eloquent;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Session;

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
    function medias() { return $this->belongsTo('App\Models\Media'); }
    
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    function studentAttendancesetting() {
        
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
        abort_if(!$student, HttpStatusCode::NOT_FOUND, __('messages.student_not_found'));
        $class = $student->squad;
        abort_if(!$class, HttpStatusCode::NOT_FOUND, __('messages.student_not_found'));
        $grade = $class->grade;
        abort_if(!$grade, HttpStatusCode::NOT_FOUND, __('messages.student_not_found'));
        $school = $grade->school;
        abort_if(!$school, HttpStatusCode::NOT_FOUND, __('messages.student_not_found'));
        $dateTime = strtotime($data['punch_time']);
        $day = Constant::DAYS[date('w', $dateTime)];
        $strDateTime = date('Y-m-d', $dateTime);
        $semester = Semester::where('start_date', '<=', $strDateTime)
            ->where('end_date', '>=', $strDateTime)
            ->where('enabled', 1)
            ->first();
        abort_if(!$semester, HttpStatusCode::NOT_FOUND, __('messages.semester_not_found'));
        $machine = AttendanceMachine::whereMachineid($data['machineid'])
            ->where('school_id', $school->id)->first();
        abort_if(!$machine, HttpStatusCode::NOT_FOUND, __('messages.machine_not_found'));
        $sases = StudentAttendanceSetting::whereGradeId($grade->id)
            ->where('semester_id', $semester->id)
            ->where('day', $day)->get();
        abort_if(!$sases, HttpStatusCode::NOT_FOUND, __('messages.sas_not_found'));
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
     * 考勤统计
     *
     * @return array
     */
    function stat() {
    
        Request::validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|greater_than_or_equal_to:start_date'
        ]);
        $classId = Request::input('class_id') ?? $this->classIds()[0];
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
                        return ' ';
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

    function wIndex() {
    
        
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
        foreach ($students as $k => $s) {
            $data = $this->getDays($s->id, $beginTime, $endTime);
            $s->abnormal = count($data['adays']);
            $s->normal = count($data['ndays']);
            $s->schoolname = Squad::whereId($s->class_id)->first()->grade->school->name;
            $s->studentname = User::whereId($s->user_id)->first()->realname;
            $s->class_id = Squad::whereId($s->class_id)->first()->name;
        }
        
        return $students;
        
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
    
}
