<?php

namespace App\Models;

use App\Facades\DatatableFacade as Datatable;
use App\Helpers\ModelTrait;
use App\Helpers\Snippet;
use Carbon\Carbon;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * App\Models\AttendanceMachine 考勤机
 *
 * @property int $id
 * @property string $name 考勤机名称
 * @property string $location 考勤机位置
 * @property int $school_id 所属学校ID
 * @property string $machineid 考勤机id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int $enabled
 * @method static Builder|AttendanceMachine whereCreatedAt($value)
 * @method static Builder|AttendanceMachine whereEnabled($value)
 * @method static Builder|AttendanceMachine whereId($value)
 * @method static Builder|AttendanceMachine whereLocation($value)
 * @method static Builder|AttendanceMachine whereMachineid($value)
 * @method static Builder|AttendanceMachine whereName($value)
 * @method static Builder|AttendanceMachine whereSchoolId($value)
 * @method static Builder|AttendanceMachine whereUpdatedAt($value)
 * @mixin Eloquent
 * @property-read School $school
 * @property-read StudentAttendance[] $studentAttendances
 */
class AttendanceMachine extends Model {
    
    use ModelTrait;

    protected $table = 'attendance_machines';

    protected $fillable = [
        'name', 'location', 'school_id',
        'machineid', 'enabled',
    ];

    /**
     * 返回考勤机所属的学校对象
     *
     * @return BelongsTo
     */
    function school() { return $this->belongsTo('App\Models\School'); }

    /**
     * 获取指定考勤机的学生考勤记录对象
     *
     * @return HasMany
     */
    function studentAttendances() { return $this->hasMany('App\Models\StudentAttendance'); }
    
    /**
     * 考勤机列表
     *
     * @return array
     */
    function datatable() {
        
        $columns = [
            ['db' => 'AttendanceMachine.id', 'dt' => 0],
            ['db' => 'AttendanceMachine.name', 'dt' => 1],
            ['db' => 'AttendanceMachine.location', 'dt' => 2],
            [
                'db' => 'School.name as schoolname', 'dt' => 3,
                'formatter' => function ($d) {
                    return sprintf(Snippet::ICON, 'fa-university', '') . $d;
                }
            ],
            ['db' => 'AttendanceMachine.machineid', 'dt' => 4],
            ['db' => 'AttendanceMachine.created_at', 'dt' => 5],
            ['db' => 'AttendanceMachine.updated_at', 'dt' => 6],
            [
                'db' => 'AttendanceMachine.enabled', 'dt' => 7,
                'formatter' => function ($d, $row) {
                    return Datatable::dtOps($d, $row, false);
                },
            ],
        ];
        $joins = [
            [
                'table' => 'schools',
                'alias' => 'School',
                'type' => 'INNER',
                'conditions' => [
                    'School.id = AttendanceMachine.school_id',
                ],
            ],
        ];
        $condition = 'School.id = ' . $this->schoolId();

        return Datatable::simple(
            $this->getModel(), $columns, $joins, $condition
        );
        
    }

}