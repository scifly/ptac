<?php
namespace App\Models;

use App\Events\StudentAttendanceCreate;
use App\Facades\DatatableFacade as Datatable;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * App\Models\StudentAttendance
 *
 * @property int $id
 * @property int $student_id 学生ID
 * @property string $punch_time 打卡时间
 * @property int $inorout 进或出
 * @property int $attendance_machine_id 考勤机ID
 * @property int $media_id 考勤照片多媒体ID
 * @property float $longitude 打卡时所处经度
 * @property float $latitude 打卡时所处纬度
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @method static Builder|StudentAttendance whereAttendanceMachineId($value)
 * @method static Builder|StudentAttendance whereCreatedAt($value)
 * @method static Builder|StudentAttendance whereId($value)
 * @method static Builder|StudentAttendance whereInorout($value)
 * @method static Builder|StudentAttendance whereLatitude($value)
 * @method static Builder|StudentAttendance whereLongitude($value)
 * @method static Builder|StudentAttendance whereMediaId($value)
 * @method static Builder|StudentAttendance wherePunchTime($value)
 * @method static Builder|StudentAttendance whereStudentId($value)
 * @method static Builder|StudentAttendance whereUpdatedAt($value)
 * @mixin \Eloquent 考勤
 * @property-read \App\Models\AttendanceMachine $attendanceMachine
 * @property-read \App\Models\Media $medias
 * @property-read \App\Models\Student $student
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
        $condition = 'AttendanceMachine.school_id = ' . School::schoolId();
        
        return Datatable::simple(self::getModel(), $columns, $joins, $condition);
        
    }
    
    /**
     * @param $input
     */
    static function storeByFace($input) {
        #触发事件调用队列
        event(new StudentAttendanceCreate($input));
    }
    
}
