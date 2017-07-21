<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use App\Facades\DatatableFacade as Datatable;

/**
 * App\Models\Grade
 *
 * @property int $id
 * @property string $name 年级名称
 * @property int $school_id 所属学校ID
 * @property string $educator_ids 年级主任教职员工ID
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property int $enabled
 * @method static Builder|Grade whereCreatedAt($value)
 * @method static Builder|Grade whereEducatorIds($value)
 * @method static Builder|Grade whereEnabled($value)
 * @method static Builder|Grade whereId($value)
 * @method static Builder|Grade whereName($value)
 * @method static Builder|Grade whereSchoolId($value)
 * @method static Builder|Grade whereUpdatedAt($value)
 * @mixin \Eloquent
 * 年级
 */
class Grade extends Model {

    protected $fillable = [
        'name',
        'school_id',
        'educator_ids',
        'enabled',
    ];

    public function squads() {

        return $this->hasMany('App\Models\Squad');
    }

    public function school() {

            return $this->belongsTo('App\Models\School');
    }

    public function datatable() {

        $columns = [
            ['db' => 'Grade.id', 'dt' => 0],
            ['db' => 'Grade.name', 'dt' => 1],
            ['db' => 'School.name as schoolname', 'dt' => 2],
            ['db' => 'Grade.educator_ids', 'dt' => 3],
            ['db' => 'Grade.created_at', 'dt' => 4],
            ['db' => 'Grade.updated_at', 'dt' => 5],

            [
                'db' => 'Grade.enabled', 'dt' => 6,
                'formatter' => function ($d, $row) {
                    return Datatable::dtOps($this, $d, $row);
                }
            ]
        ];
        $joins = [
            [
                'table' => 'schools',
                'alias' => 'School',
                'type'  => 'INNER',
                'conditions' => [
                    'School.id = Grade.school_id'
                ]

            ]
        ];

        return Datatable::simple($this, $columns, $joins);
    }

}
