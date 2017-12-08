<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

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

    //
    protected $table = 'student_attendance';
    protected $fillable = [
        'id', 'student_id', 'punch_time',
        'inorout', 'attendance_machine_id', 'media_id',
        'longitude', 'latitude', 'created_at',
        'updated_at',
    ];

}
