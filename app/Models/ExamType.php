<?php

namespace App\Models;

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
 */
class ExamType extends Model {
    //
    protected $table='exam_types';

    protected $fillable=['name','remark','created_at','updated_at','enabled'];

    public function Exam()
    {
        return $this->hasOne('App\Models\Exam');
    }
}
