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
        return $this->hasMany('App\Models\SubjectModule','subject_id','id');
    }



    public function school()
    {
        return $this->belongsTo('App\Models\School');
    }

    public function datatable(Request $request)
    {

        $columns = [

            ['db' => 'Subject.id', 'dt' => 0],
            ['db' => 'Subject.name', 'dt'=> 1],
            ['db' => 'Subject.isaux', 'dt'=> 2],
            ['db' => 'Subject.max_score', 'dt'=> 3],
            ['db' => 'Subject.pass_score', 'dt'=> 4],
            ['db' => 'Subject.created_at', 'dt' => 5],
            ['db' => 'Subject.updated_at', 'dt' => 6],
            [
                'db' => 'Subject.enabled', 'dt' => 7,
                'formmatter' => function($d, $row)
                {
                    return Datatable::dtOps($this, $d ,$row);
                }
            ]
        ];
        return Datatable::simple($this, $request, $columns);
    }



}
