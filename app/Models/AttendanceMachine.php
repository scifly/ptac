<?php

namespace App\Models;

use App\Facades\DatatableFacade as Datatable;
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
        'enabled'
    ];

    /**
     * 考勤机与学校
     */
    public function school()
    {
        return $this->belongsTo('App\Models\School');
    }

    /**
     * 考勤机与学生考勤
     */
    public function studentAttendances()
    {
        return $this->hasMany('App\Models\StudentAttendance');
    }

    public function datatable()
    {

        $columns = [
            ['db' => 'AttendanceMachine.id', 'dt' => 0],
            ['db' => 'AttendanceMachine.name', 'dt' => 1],
            ['db' => 'AttendanceMachine.location', 'dt' => 2],
            ['db' => 'School.name as schoolname', 'dt' => 3],
            ['db' => 'AttendanceMachine.machineid', 'dt' => 4],
            ['db' => 'AttendanceMachine.created_at', 'dt' => 5],
            ['db' => 'AttendanceMachine.updated_at', 'dt' => 6],
            [
                'db' => 'AttendanceMachine.enabled', 'dt' => 7,
                'formatter' => function ($d, $row) {
                    return Datatable::dtOps($this, $d, $row);
                }
            ],
        ];
        $joins = [
            [
                'table' => 'schools',
                'alias' => 'School',
                'type' => 'INNER',
                'conditions' => [
                    'School.id = AttendanceMachine.school_id'
                ]
            ]
        ];
        return Datatable::simple($this, $columns, $joins);
    }
}
