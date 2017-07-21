<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use App\Facades\DatatableFacade as Datatable;
use Illuminate\Http\Request;
use Symfony\Component\VarDumper\Cloner\Data;

/**
 * App\Models\Subject
 *
 * @property int $id
 * @property int $school_id 所属学校ID
 * @property string $name 科目名称
 * @property int $isaux 是否为副科
 * @property int $max_score 科目满分
 * @property int $pass_score 及格分数
 * @property string $grade_ids 年级ID
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property int $enabled
 * @method static Builder|Subject whereCreatedAt($value)
 * @method static Builder|Subject whereEnabled($value)
 * @method static Builder|Subject whereGradeIds($value)
 * @method static Builder|Subject whereId($value)
 * @method static Builder|Subject whereIsaux($value)
 * @method static Builder|Subject whereMaxScore($value)
 * @method static Builder|Subject whereName($value)
 * @method static Builder|Subject wherePassScore($value)
 * @method static Builder|Subject whereSchoolId($value)
 * @method static Builder|Subject whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Subject extends Model {

    protected $fillable=[
        'school_id',
        'name',
        'isaux',
        'max_score',
        'pass_score',
        'grade_ids',

    ];

    public function subjectModules()
    {
        return $this->hasMany('App\Models\SubjectModule');
    }


    public function school()
    {
        return $this->belongsTo('App\Models\School');
    }

    public function datatable()
    {

        $columns = [

            ['db' => 'Subject.id', 'dt' => 0],
            ['db' => 'Subject.name', 'dt'=> 1],
            ['db' => 'School.name as schoolname', 'dt' => 2],
            ['db' => 'Subject.isaux', 'dt'=> 3],
            ['db' => 'Subject.max_score', 'dt'=> 4],
            ['db' => 'Subject.pass_score', 'dt'=> 5],
            ['db' => 'Subject.created_at', 'dt' => 6],
            ['db' => 'Subject.updated_at', 'dt' => 7],
            [
                'db' => 'Subject.enabled', 'dt' => 8,
                'formatter' => function($d, $row)
                {
                    return Datatable::dtOps($this, $d ,$row);
                }
            ]
        ];

        $joins = [
            [
                'table' => 'schools',
                'alias' => 'School',
                'type' => 'INNER',
                'conditions' => [
                    'School.id = Subject.school_id'
                ]
            ]

        ];
        return Datatable::simple($this, $columns, $joins);
    }



}
