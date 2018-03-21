<?php
namespace App\Models;

use App\Events\StudentAttendanceCreate;
use App\Facades\DatatableFacade as Datatable;
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
            [
                'db'        => 'StudentAttendance.inorout', 'dt' => 4,
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
            ['db' => 'AttendanceMachine.name as attendname', 'dt' => 5],
            [
                'db'        => 'StudentAttendance.longitude', 'dt' => 6,
                'formatter' => function ($d) {
                    return $d == 0 ? ' ' : $d;
                },
            ],
            [
                'db'        => 'StudentAttendance.latitude', 'dt' => 7,
                'formatter' => function ($d) {
                    return $d == 0 ? ' ' : $d;
                },
            ],
            ['db' => 'StudentAttendance.created_at', 'dt' => 8],
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
     * 考勤统计
     *
     * @return array
     */
    function stat() {
    
        $classId = Request::input('class_id');
        $startTime = Request::input('start_time');
        $endTime = Request::input('end_time');
        $days = Request::input('days');
        
        if (!$classId) {
            $classId = $this->classIds()[0];
        }
        if (!$startTime) {
            $startTime = date('Y-m-d', strtotime('-7 day'));
        }
        if (!$endTime) {
            $endTime = date('Y-m-d');
        }
        $attendances = [];
        # 指定班级所有学生的id
        $studentIds = Student::whereClassId($classId)->get()->pluck('id')->toArray();
        if (empty($studentIds)) {
            return $attendances;
        }
        $attendances = $this->latestAttendances(implode(',', $studentIds), $startTime, $endTime);
        $normals = [];
        $abnormals = [];
        if (!empty($attendances)) {
            foreach ($attendances as $key => &$val) {
                if ($val->lastest) {
                    if (isset($normals[$val->day])) {
                        $normals[$val->day] = ($normals[$val->day] + 1);
                    } else {
                        $normals[$val->day] = 1;
                    }
                } else {
                    if (isset($abnormals[$val->day])) {
                        $abnormals[$val->day] = ($abnormals[$val->day] + 1);
                    } else {
                        $abnormals[$val->day] = 1;
                    }
                }
                
            }
        }
        for ($i = 0; $i < $days; $i++) {
            $date = strtotime($startTime);
            $date = date("Y-m-d", $date + (86400 * $i));
            $all = sizeof($studentIds);
            $normal = $normals[$date] ?? 0;
            $abnormal = $abnormals[$date] ?? 0;
            $attendances[$i] = [
                'date' => $date,
                'all' => $all,
                'normal' => $normal,
                'abnormal' => $abnormal,
                'missed' => $all - $normal - $abnormal
            ];
        }
        
        return $attendances;
        
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
            !session('details'),
            HttpStatusCode::BAD_REQUEST,
            __('messages.bad_request')
        );
        $details = session('details');
        Session::forget('details');
    
        return $this->excel(
            $details,
            '学生考勤明细',
            '考勤明细'
        );
    
    }
    
    /**
     * @param $input
     * @return bool
     */
    function storeByFace($input) {
        
        # 触发事件调用队列，这个是异步处理的因此错误信息不能返回
        event(new StudentAttendanceCreate($input));
        
        return true;
        
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
        $startTime = date('Y-m-d H:i:s', strtotime($date));
        $endTime = date('Y-m-d H:i:s', strtotime($date) + 24 * 3600 - 1);
        $studentIds = Student::whereClassId($classId)->get()->pluck('id')->toArray();
        $result = [];
        $attendances = $this->latestAttendances(implode(',', $studentIds), $startTime, $endTime);
        if ($type == 'missed') {
            # 打过考勤的学生ids
            $aStudentIds = $this->whereIn('student_id', $studentIds)
                ->whereBetween('punch_time', [$startTime, $endTime])
                ->get()->pluck('student_id');
            # 未打考勤的学生
            $nStudents = Student::whereClassId($classId)
                ->whereNotIn('id', $aStudentIds)
                ->get();
            if ($nStudents) {
                foreach ($nStudents as $student) {
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
                    $result[] = [
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
                    $n = [];
                    foreach ($attendances as $a) {
                        if ($a->lastest) {
                            $n[] = $a->id;
                        }
                    }
                    $aStudentIds = $this->whereIn('id', $n)->get();
                    foreach ($aStudentIds as $student) {
                        $userIds = array_column($student->student->custodians->toArray(), 'user_id');
                        $result[] = [
                            'name'       => $student->student->user->realname,
                            'custodian'  => User::whereIn('id', $userIds)->get()->pluck('realname')->toArray(),
                            'mobile'     => array_column($student->student->user->mobiles->toArray(), 'mobile'),
                            'punch_time' => $student->punch_time,
                            'inorout'    => $student->inorout == 1 ? '进' : '出',
                        ];
                    }
                    break;
                case 'abnormal':
                    $student = [];
                    foreach ($attendances as $a) {
                        if ($a->lastest == 0) {
                            $student[] = $a->id;
                        }
                    }
                    $aStudentIds = $this->whereIn('id', $student)->get();
                    foreach ($aStudentIds as $student) {
                        $userIds = array_column($student->student->custodians->toArray(), 'user_id');
                        $custodians = User::whereIn('id', $userIds)->get()->pluck('realname')->toArray();
                        $result[] = [
                            'name'       => $student->student->user->realname,
                            'custodian'  => $custodians,
                            'mobile'     => array_column($student->student->user->mobiles->toArray(), 'mobile'),
                            'punch_time' => $student->punch_time,
                            'inorout'    => $student->inorout == 1 ? '进' : '出',
                        ];
                    }
                    break;
                default:
                    break;
            }
        }

        return $result;
        
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
        
        session(['details' => $rows]);
        
    }
    
}
