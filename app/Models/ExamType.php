<?php

namespace App\Models;

use App\Facades\DatatableFacade as Datatable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\ExamType
 *
 * @property int $id
 * @property string $name 考试类型名称
 * @property string $remark 考试类型备注
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property int $enabled
 * @method static Builder|ExamType whereCreatedAt($value)
 * @method static Builder|ExamType whereEnabled($value)
 * @method static Builder|ExamType whereId($value)
 * @method static Builder|ExamType whereName($value)
 * @method static Builder|ExamType whereRemark($value)
 * @method static Builder|ExamType whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property-read \App\Models\Exam $Exam
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Exam[] $exams
 */
class ExamType extends Model {

    protected $fillable = ['name', 'remark', 'enabled'];
    
    public function exams() {
        
        return $this->hasMany('App\Models\Exam');
        
    }
    
    public function datatable() {
        
        $columns = [
            ['db' => 'ExamType.id', 'dt' => 0],
            ['db' => 'ExamType.name', 'dt' => 1],
            ['db' => 'ExamType.remark', 'dt' => 2],
            ['db' => 'ExamType.created_at', 'dt' => 3],
            ['db' => 'ExamType.updated_at', 'dt' => 4],
            
            [
                'db' => 'ExamType.enabled', 'dt' => 5,
                'formatter' => function ($d, $row) {
                    return Datatable::dtOps($this, $d, $row);
                }
            ]
        ];
        
        return Datatable::simple($this, $columns);
    }
    
}
