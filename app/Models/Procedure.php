<?php

namespace App\Models;

use App\Facades\DatatableFacade as Datatable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Procedure
 *
 * @property int $id
 * @property int $procedure_type_id 流程类型ID
 * @property int $school_id 流程所属学校ID
 * @property string $name 流程名称
 * @property string $remark 流程备注
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property int $enabled
 * @method static Builder|Procedure whereCreatedAt($value)
 * @method static Builder|Procedure whereEnabled($value)
 * @method static Builder|Procedure whereId($value)
 * @method static Builder|Procedure whereName($value)
 * @method static Builder|Procedure whereProcedureTypeId($value)
 * @method static Builder|Procedure whereRemark($value)
 * @method static Builder|Procedure whereSchoolId($value)
 * @method static Builder|Procedure whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property-read \App\Models\ProcedureType $procedureType
 * @property-read \App\Models\School $school
 */
class Procedure extends Model {
    //
    protected $table = 'procedures';

    protected $fillable = [
        'procedure_type_id',
        'school_id',
        'name',
        'remark',
        'created_at',
        'updated_at',
        'enabled',
    ];

    /**
     * 流程与学校
     */
    public function school() {
        return $this->belongsTo('App\Models\School');
    }

    /**
     * 流程与流程类型
     */
    public function procedureType() {
        return $this->belongsTo('App\Models\ProcedureType');
    }


    public function datatable() {

        $columns = [
            ['db' => 'Procedures.id', 'dt' => 0],
            ['db' => 'ProcedureType.name as proceduretypename', 'dt' => 1],
            ['db' => 'School.name as schoolname', 'dt' => 2],
            ['db' => 'Procedures.name', 'dt' => 3],
            ['db' => 'Procedures.remark', 'dt' => 4],
            ['db' => 'Procedures.created_at', 'dt' => 5],
            ['db' => 'Procedures.updated_at', 'dt' => 6],
            [
                'db' => 'Procedures.enabled', 'dt' => 7,
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
                    'School.id = Procedures.school_id'
                ]
            ],
            [
                'table' => 'procedure_types',
                'alias' => 'ProcedureType',
                'type' => 'INNER',
                'conditions' => [
                    'ProcedureType.id = Procedures.procedure_type_id'
                ]
            ]
        ];

        return Datatable::simple($this, $columns, $joins);
    }
}

