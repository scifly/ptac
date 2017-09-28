<?php
namespace App\Models;

use App\Facades\DatatableFacade as Datatable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Semester
 *
 * @property int $id
 * @property int $school_id 所属学校ID
 * @property string $name 学期名称
 * @property string $start_date 学期开始日期
 * @property string $end_date 学期截止日期
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property int $enabled
 * @method static Builder|Semester whereCreatedAt($value)
 * @method static Builder|Semester whereEnabled($value)
 * @method static Builder|Semester whereEndDate($value)
 * @method static Builder|Semester whereId($value)
 * @method static Builder|Semester whereName($value)
 * @method static Builder|Semester whereSchoolId($value)
 * @method static Builder|Semester whereStartDate($value)
 * @method static Builder|Semester whereUpdatedAt($value)
 * @mixin \Eloquent
 * 学期
 * @property string|null $remark 备注
 * @property-read \App\Models\School $school
 * @method static Builder|Semester whereRemark($value)
 * @property-read \App\Models\StudentAttendanceSetting $studentAttendanceSetting
 */
class Semester extends Model {

    protected $fillable = [
        'school_id',
        'name',
        'remark',
        'start_date',
        'end_date',
        'enabled',
    ];

    public function school() {

        return $this->belongsTo('App\Models\School');

    }

    public function studentAttendanceSetting() {
        return $this->hasOne('App\Models\StudentAttendanceSetting', 'semester_id', 'id');
    }

    public function datatable() {

        $columns = [
            ['db' => 'Semester.id', 'dt' => 0],
            ['db' => 'Semester.name as semestername', 'dt' => 1],
            ['db' => 'Semester.name as schoolname', 'dt' => 2],
            ['db' => 'Semester.start_date', 'dt' => 3],
            ['db' => 'Semester.end_date', 'dt' => 4],
            ['db' => 'Semester.created_at', 'dt' => 5],
            ['db' => 'Semester.updated_at', 'dt' => 6],
            [
                'db'        => 'Semester.enabled', 'dt' => 7,
                'formatter' => function ($d, $row) {
                    return Datatable::dtOps($this, $d, $row);
                },
            ],
        ];
        $joins = [
            [
                'table'      => 'schools',
                'alias'      => 'School',
                'type'       => 'INNER',
                'conditions' => [
                    'School.id = Semester.school_id',
                ],
            ],
        ];

        return Datatable::simple($this, $columns, $joins);

    }

}
