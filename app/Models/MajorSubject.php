<?php
namespace App\Models;

use Carbon\Carbon;
use Eloquent;
use Illuminate\Database\Eloquent\{Builder, Relations\BelongsTo, Relations\Pivot};

/**
 * App\Models\MajorSubject 专业与科目关系
 *
 * @property int $id
 * @property int $major_id 专业ID
 * @property int $subject_id 科目ID
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Major $major
 * @property-read Subject $subject
 * @method static Builder|MajorSubject whereCreatedAt($value)
 * @method static Builder|MajorSubject whereId($value)
 * @method static Builder|MajorSubject whereMajorId($value)
 * @method static Builder|MajorSubject whereSubjectId($value)
 * @method static Builder|MajorSubject whereUpdatedAt($value)
 * @method static Builder|MajorSubject newModelQuery()
 * @method static Builder|MajorSubject newQuery()
 * @method static Builder|MajorSubject query()
 * @method static Builder|MajorSubject whereEnabled($value)
 * @mixin Eloquent
 * @property int $enabled
 */
class MajorSubject extends Pivot {
    
    protected $fillable = ['major_id', 'subject_id', 'enabled'];
    
    /** @return BelongsTo */
    function major() { return $this->belongsTo('App\Models\Major'); }
    
    /** @return BelongsTo */
    function subject() { return $this->belongsTo('App\Models\Subject'); }
    
}
