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
            $data =$this->getSqlData(implode(',',$all), $startTime, $endTime);

            $n = [];
            $a = [];
            if ($data) {
                foreach ($data as $key => &$val)
                {
                    if ($val->lastest) {
                        if(isset($n[$val->day])) {
                            $n[$val->day] = ($n[$val->day]+1);
                        }else{
                            $n[$val->day] = 1;
                        }
                    }else{
                        if(isset($a[$val->day])) {
                            $a[$val->day] = ($a[$val->day]+1);
                        }else{
                            $a[$val->day] = 1;
                        }
                    }

                }
            }
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
//        $date = '2018-01-07';
//        $type = 'surplus';
//        $classId = '1';
        $startTime = date('Y-m-d H:i:s', strtotime($date));
        $endTime = date('Y-m-d H:i:s', strtotime($date)+24*3600-1);
        $all = Student::whereClassId($classId)->get()->pluck('id')->toArray();
        $result = [];
        $data =$this->getSqlData(implode(',',$all), $startTime, $endTime);
        if ($type == 'surplus') {
            $items = $this->whereIn('student_id', $all)
                ->where('punch_time', '>=', $startTime)
                ->where('punch_time', '<', $endTime)
                ->get()
                ->pluck('student_id');
            $s = Student::whereClassId($classId)
                ->whereNotIn('id', $items)
                ->get();
            if ($s) {
                foreach ($s as $datum) {
                    if ($datum->custodians) {
                        $cu = User::whereIn('id', array_column(json_decode($datum->custodians), 'user_id'))
                            ->get()
                            ->pluck('realname');
                    }else{
                        $cu = [];
                    }
                    if (json_decode($datum->user['mobiles'])) {
                        $mo = array_column(json_decode($datum->user['mobiles']), 'mobile');
                    }else{
                        $mo = [];
                    }
                    $result[] = [
                        'name' => $datum->user['realname'],
                        'custodian' => $cu,
                        'moblie' => $mo,
                        'punch_time' => '',
                        'inorout' => '',
                    ];
                }
            }
        }
        if ($data) {
            switch ($type) {
                case 'normal':
                    $n = [];
                    foreach ($data as $d) {
                        if ($d->lastest) {
                            $n[] = $d->id;
                        }
                    }
                    $items = $this::whereIn('id', $n)->get();
                    foreach ($items as $datum) {
                        $result[] = [
                            'name' => $datum->student->user->realname,
                            'custodian' => User::whereIn('id', array_column($datum->student->custodians->toArray(), 'user_id'))
                                ->get()
                                ->pluck('realname'),
                            'moblie' => array_column($datum->student->user->mobiles->toArray(), 'mobile'),
                            'punch_time' => $datum->punch_time,
                            'inorout' => $datum->inorout,
                        ];
                    }

                    break;
                case 'abnormal':
                    $a = [];
                    foreach ($data as $d) {
                        if ($d->lastest == 0) {
                            $a[] = $d->id;
                        }
                    }
                    $items = $this::whereIn('id', $a)->get();
                    foreach ($items as $datum) {
                        $result[] = [
                            'name' => $datum->student->user->realname,
                            'custodian' => User::whereIn('id', array_column($datum->student->custodians->toArray(), 'user_id'))
                                ->get()
                                ->pluck('realname'),
                            'moblie' => array_column($datum->student->user->mobiles->toArray(), 'mobile'),
                            'punch_time' => $datum->punch_time,
                            'inorout' => $datum->inorout,
                        ];
                    }
                    break;
            }
        }

        return $result;
    }

    /**
     * @param $studentIds
     * @param $start
     * @param $end
     * @return array
     */
    private function getSqlData($studentIds, $start, $end) {
        $data = DB::select("
          select max(t.id) id,t.student_id, substring_index(group_concat( t.status order by t.punch_time DESC),',',1) lastest, DATE(t.punch_time) day 
          from (
            select ord.id,ord.student_id, ord.inorout,ord.status, ord.punch_time 
              from student_attendances ord where ord.punch_time >= '". $start."' and ord.punch_time < '". $end."' order by ord.student_id asc , ord.punch_time desc) t 
              where t.student_id in (" . $studentIds . ") 
          group by t.student_id,day"
        );
//        foreach ($data as $d) {
//            Log::debug("
//          select max(t.id) id,t.student_id, substring_index(group_concat( t.status order by t.punch_time DESC),',',1) lastest, DATE(t.punch_time) day
//          from (
//            select ord.id,ord.student_id, ord.inorout,ord.status, ord.punch_time
//              from student_attendances ord where ord.punch_time >= '". $start."' and ord.punch_time < '". $end."' order by ord.student_id asc , ord.punch_time desc) t
//              where t.student_id in (" . $studentIds . ")
//          group by t.student_id,day"
//            );
//        }
        return $data;
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
