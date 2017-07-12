<?php

namespace App\Models;

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
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\StudentAttendance whereAttendanceMachineId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\StudentAttendance whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\StudentAttendance whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\StudentAttendance whereInorout($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\StudentAttendance whereLatitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\StudentAttendance whereLongitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\StudentAttendance whereMediaId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\StudentAttendance wherePunchTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\StudentAttendance whereStudentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\StudentAttendance whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class StudentAttendance extends Model
{
    //
}
