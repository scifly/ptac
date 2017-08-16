<?php

namespace App\Models;

use App\Facades\DatatableFacade as Datatable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\ScoreRange
 *
 * @property int $id
 * @property string $name 成绩统计项名称
 * @property string $subject_ids 成绩统计项包含的科目IDs
 * @property int $school_id 成绩统计项所属学校ID
 * @property float $start_score 成绩统计项起始分数
 * @property float $end_score 成绩统计项截止分数
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property int $enabled 是否统计
 * @method static Builder|ScoreRange whereCreatedAt($value)
 * @method static Builder|ScoreRange whereEnabled($value)
 * @method static Builder|ScoreRange whereEndScore($value)
 * @method static Builder|ScoreRange whereId($value)
 * @method static Builder|ScoreRange whereName($value)
 * @method static Builder|ScoreRange whereSchoolId($value)
 * @method static Builder|ScoreRange whereStartScore($value)
 * @method static Builder|ScoreRange whereSubjectIds($value)
 * @method static Builder|ScoreRange whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property-read \App\Models\School $school
 */
class ScoreRange extends Model {
    //
    protected $table = 'score_ranges';
    protected $fillable = [
        'name',
        'subject_ids',
        'school_id',
        'start_score',
        'end_score',
        'created_at',
        'updated_at',
        'enabled'
    ];
    
    /**
     * 获取拥有该统计项的学校。
     */
    public function school() {
        return $this->belongsTo('App\Models\School');
    }
    
    public function datatable() {
        $columns = [
            ['db' => 'ScoreRange.id', 'dt' => 0],
            ['db' => 'ScoreRange.name', 'dt' => 1],
            ['db' => 'School.name as schoolname', 'dt' => 2],
            ['db' => 'ScoreRange.start_score', 'dt' => 3],
            ['db' => 'ScoreRange.end_score', 'dt' => 4],
            ['db' => 'ScoreRange.created_at', 'dt' => 5],
            ['db' => 'ScoreRange.updated_at', 'dt' => 6],
            [
                'db' => 'ScoreRange.enabled', 'dt' => 7,
                'formatter' => function ($d, $row) {
                    return Datatable::dtOps($this, $d, $row);
                }
            ]
        ];
        
        $joins = [
            [
                'table' => 'schools',
                'alias' => 'School',
                'type' => 'LEFT',
                'conditions' => [
                    'School.id = ScoreRange.school_id'
                ]
            ]
        
        
        ];
        return Datatable::simple($this, $columns, $joins);
    }
}
