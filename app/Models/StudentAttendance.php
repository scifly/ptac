<?php
namespace App\Models;

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
 */
class StudentAttendance extends Model {
    
    protected $table = 'student_attendances';
    protected $fillable = [
        'id', 'student_id', 'punch_time',
        'inorout', 'attendance_machine_id', 'media_id',
        'longitude', 'latitude', 'created_at',
        'updated_at',
    ];
    
    public function attendanceMachine() { return $this->belongsTo('App\Models\AttendanceMachine'); }
    
    public function student() { return $this->belongsTo('App\Models\Student'); }
    
    public function medias() { return $this->belongsTo('App\Models\Media'); }
    
    public function datatable() {
        
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
        $school = new School();
        $schoolId = $school->getSchoolId();
        $condition = 'AttendanceMachine.school_id = ' . $schoolId;
        unset($school);
        
        return Datatable::simple($this, $columns, $joins, $condition);
        
    }
    
    /**
     * @param $input
     * @return bool
     * @throws Exception
     */
    public function storeByFace($input) {
        #事务处理
        try {
            DB::transaction(function () use ($input) {
                //先处理照片
                if ($input['img']) {
                    $media = new Media();
                    $mediaData = [
                        'path'          => $input['img'],
                        'remark'        => '考勤照片',
                        'media_type_id' => 1,
                        'enabled'       => 1,
                    ];
                    $m = $media->create($mediaData);
                    $input['media_id'] = $m->id;
                    unset($media);
                }
                $stuAttendData = [
                    'student_id'            => $input['student_id'],
                    'punch_time'            => $input['punch_time'],
                    'inorout'               => $input['inorout'],
                    'attendance_machine_id' => $input['attendance_machine_id'],
                    'media_id'              => $input['media_id'],
                    'longitude'             => $input['longitude'],
                    'latitude'              => $input['latitude'],
                ];
                $studentAttendance = new StudentAttendance();
                $studentAttendance->create($stuAttendData);
                unset($student);
                unset($studentAttendance);
            });
        } catch (Exception $e) {
            throw $e;
        }
        
        return true;
    }
    
}
