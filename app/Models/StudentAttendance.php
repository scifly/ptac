<?php
namespace App\Models;

use App\Events\StudentAttendanceCreate;
use App\Facades\DatatableFacade as Datatable;
use Carbon\Carbon;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


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
    public function attendanceMachine() { return $this->belongsTo('App\Models\AttendanceMachine'); }
    
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function student() { return $this->belongsTo('App\Models\Student'); }
    
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function medias() { return $this->belongsTo('App\Models\Media'); }
    
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function studentAttendancesetting() { return $this->belongsTo('App\Models\StudentAttendanceSetting', 'sas_id', 'id'); }
    
    /**
     * 学生考勤记录列表
     *
     * @return array
     */
    static function datatable() {
        
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
                        return $d == 1 ? '<span class="badge bg-green">进</span>' : '<span class="badge bg-red">出</span>';
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
        // todo: 增加角色过滤条件
        $condition = 'AttendanceMachine.school_id = ' . School::schoolId();
        
        return Datatable::simple(self::getModel(), $columns, $joins, $condition);
        
    }

    /**
     * @param null $classId
     * @param null $startTime
     * @param null $endTime
     * @param null $days
     * @return array
     */
    public function getData($classId = null, $startTime = null, $endTime = null, $days = null) {
        if (!$classId) { $classId = $this->getClass(); }
        if (!$startTime) { $startTime = date('Y-m-d',strtotime('-7 day')); }
        if (!$endTime) { $endTime = date('Y-m-d'); }
        $item = [];
        if ($classId && $startTime && $endTime) {
            $all = Student::whereClassId($classId)->get()->pluck('id')->toArray();
            if (empty($all)) {
                return $item;
            }
            $normal = $this->where('status', 1)
                ->select(
                    array(
                        DB::Raw('student_id'),
                        DB::Raw('count(*) as total'),
                        DB::Raw('count(student_id) count'),
                        DB::Raw('DATE(punch_time) day')
                        )
                    )
                ->whereIn('student_id', $all)
                ->where('punch_time', '>=', $startTime)
                ->where('punch_time', '<', $endTime)
                ->groupBy(['day','student_id'])
                ->get();
            $abnormal = $this->where('status', 0)
                ->select(
                    array(
                        DB::Raw('student_id'),
                        DB::Raw('count(*) as total'),
                        DB::Raw('count(student_id) count'),
                        DB::Raw('DATE(punch_time) day')
                        )
                    )
                ->whereIn('student_id', $all)
                ->where('punch_time', '>=', $startTime)
                ->where('punch_time', '<', $endTime)
                ->groupBy(['day','student_id'])
                ->get();
            $n = [];
            $a = [];
            if ($normal) {
                foreach ($normal as $key => &$val)
                {
                    if(isset($n[$val['day']])) {

                        $n[$val['day']] = ($n[$val['day']]+1);
                    }else{
                        $n[$val['day']] = 1;
                    }
                }
            }
            if ($abnormal) {
                foreach ($abnormal as $key => &$val)
                {
                    if(isset($n[$val['day']])) {
                        $n[$val['day']] = ($n[$val['day']]+1);
                    }else{
                        $n[$val['day']] = 1;
                    }
                }
            }
            Log::debug($normal);
            Log::debug($n);

            for ($i = 1;$i < $days+1; $i++)
            {
                $date = strtotime($startTime);
                $date = date("Y-m-d",$date+(86400*$i));
                $item[$i]['date'] = $date;
                $item[$i]['all'] = count($all);
                $item[$i]['normal'] = isset($n[$date]) ? $n[$date] : 0;
                $item[$i]['abnormal'] = isset($a[$date]) ? $a[$date] : 0;
                $item[$i]['surplus'] = $item[$i]['all']-$item[$i]['normal']-$item[$i]['abnormal'];
            }
            return $item;
        }
        return $item;
    }
    public function getStudentData($date , $type, $classId) {
        $startTime = date('Y-m-d H:i:s', strtotime($date));
        $endTime = date('Y-m-d H:i:s', strtotime($date)+24*3600-1);
        $all = Student::whereClassId($classId)->get()->pluck('id')->toArray();
        $result = [];
        switch ($type) {
            case 'normal':
                $data = $this->where('status', 1)
                    ->whereIn('student_id', $all)
                    ->where('punch_time', '>=', $startTime)
                    ->where('punch_time', '<', $endTime)
                    ->groupBy(['student_id'])
                    ->get();
                foreach ($data as $datum) {
                    $result[] = [
                        'name' => $datum->student->user->realname,
                        'custodian' => $datum->student->custodians,
//                        'custodian' => $datum->student->custodians-,
                    ];
                }
                break;
            case 'abnormal':
                $data = $this->where('status', 0)
                    ->whereIn('student_id', $all)
                    ->where('punch_time', '>=', $startTime)
                    ->where('punch_time', '<', $endTime)
                    ->groupBy(['student_id'])
                    ->get();
                break;
            case  'surplus':
                $items = $this->whereIn('student_id', $all)
                    ->where('punch_time', '>=', $startTime)
                    ->where('punch_time', '<', $endTime)
                    ->get()
                    ->pluck('student_id');
                $data = Student::whereNotIn('id', $items)->get();
                break;
        }
    }
    private function getClass() {
        $schools = null;
        $grades = null;
        $classes = null;

        $schoolId = School::schoolId();
        $schools = School::whereId($schoolId)
            ->where('enabled', 1)
            ->pluck('name', 'id');
        if ($schools) {
            $grades = Grade::whereSchoolId($schoolId)
                ->where('enabled', 1)
                ->pluck('name', 'id');
        }
        if ($grades) {
            $classes = Squad::whereGradeId($grades->keys()->first())
                ->where('enabled', 1)
                ->pluck('name', 'id');
            return $classes->keys()->first();
        }

    }
    /**
     * @param $input
     * @return bool
     */
    static function storeByFace($input) {

        #触发事件调用队列，这个是异步处理的因此错误信息不能返回
        event(new StudentAttendanceCreate($input));
        return true;
    }


    
}
