<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\AttendanceMachine
 *
 * @property int $id
 * @property string $name 考勤机名称
 * @property string $location 考勤机位置
 * @property int $school_id 所属学校ID
 * @property string $machineid 考勤机id
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property int $enabled
 * @method static Builder|AttendanceMachine whereCreatedAt($value)
 * @method static Builder|AttendanceMachine whereEnabled($value)
 * @method static Builder|AttendanceMachine whereId($value)
 * @method static Builder|AttendanceMachine whereLocation($value)
 * @method static Builder|AttendanceMachine whereMachineid($value)
 * @method static Builder|AttendanceMachine whereName($value)
 * @method static Builder|AttendanceMachine whereSchoolId($value)
 * @method static Builder|AttendanceMachine whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class AttendanceMachine extends Model
{
    protected $table = 'attendance_machines';
    protected $fillable = [
        'name',
        'location',
        'school_id',
        'machineid',
        'created_at',
        'updated_at',
        'enabled'
    ];

    /**
     * 考勤机与学校
     */
    public function schools()
    {
        return $this->belongsTo('App\Medoles\School');
    }

    /**
     * 考勤机与学生考勤 一对多
     */
    public function studentattendance()
    {
        return $this->hasMany('App\Models\StudentAttendance');
    }
}
