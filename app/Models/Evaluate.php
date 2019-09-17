<?php
namespace App\Models;

use App\Helpers\ModelTrait;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * App\Models\Evaluate
 *
 * @property int $id
 * @property int $student_id 学生id
 * @property int $indicator_id 考核项id
 * @property int $semester_id 学期id
 * @property int $educator_id 考核人教职员工id
 * @property int $amount 加/减分值
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder|Evaluate newModelQuery()
 * @method static Builder|Evaluate newQuery()
 * @method static Builder|Evaluate query()
 * @method static Builder|Evaluate whereAmount($value)
 * @method static Builder|Evaluate whereCreatedAt($value)
 * @method static Builder|Evaluate whereEducatorId($value)
 * @method static Builder|Evaluate whereId($value)
 * @method static Builder|Evaluate whereIndicatorId($value)
 * @method static Builder|Evaluate whereSemesterId($value)
 * @method static Builder|Evaluate whereStudentId($value)
 * @method static Builder|Evaluate whereUpdatedAt($value)
 * @mixin Eloquent
 * @property-read Educator $educator
 * @property-read Indicator $indicator
 * @property-read Semester $semester
 * @property-read Student $student
 */
class Evaluate extends Model {
    
    use ModelTrait;
    
    protected $fillable = [
        'student_id', 'indicator_id', 'semester_id',
        'educator_id', 'amount'
    ];
    
    /** @return BelongsTo */
    function student() { return $this->belongsTo('App\Models\Student'); }
    
    /** @return BelongsTo */
    function indicator() { return $this->belongsTo('App\Models\Indicator'); }
    
    /** @return BelongsTo */
    function semester() { return $this->belongsTo('App\Models\Semester'); }
    
    /** @return BelongsTo */
    function educator() { return $this->belongsTo('App\Models\Educator'); }
    
}
