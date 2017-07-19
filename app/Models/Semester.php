<?php

namespace App\Models;

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
 */
class Semester extends Model {
    //
    protected $table = 'semesters';
    protected $fillable =[
        'school_id',
        'name',
        'end_date',
        'created_at',
        'updated_at',
    ];
    public function belongsToSchool()
    {
        return $this->belongsTo('App\Models\School', 'school_id', 'id');

    }

}
