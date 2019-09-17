<?php
namespace App\Models;

use App\Helpers\ModelTrait;
use Eloquent;
use Illuminate\Database\Eloquent\{Builder, Collection, Model, Relations\BelongsTo, Relations\HasMany};
use Illuminate\Support\Carbon;

/**
 * App\Models\Indicator
 *
 * @property int $id
 * @property int $school_id 所属学校id
 * @property string $name 名称
 * @property int $sign 0 - 减分，1 - 加分
 * @property string|null $remark 备注
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int $enabled
 * @method static Builder|Indicator newModelQuery()
 * @method static Builder|Indicator newQuery()
 * @method static Builder|Indicator query()
 * @method static Builder|Indicator whereCreatedAt($value)
 * @method static Builder|Indicator whereEnabled($value)
 * @method static Builder|Indicator whereId($value)
 * @method static Builder|Indicator whereName($value)
 * @method static Builder|Indicator whereRemark($value)
 * @method static Builder|Indicator whereSchoolId($value)
 * @method static Builder|Indicator whereSign($value)
 * @method static Builder|Indicator whereUpdatedAt($value)
 * @mixin Eloquent
 * @property-read Collection|Evaluate[] $evals
 * @property-read int|null $evals_count
 * @property-read School $school
 */
class Indicator extends Model {
    
    use ModelTrait;
    
    protected $fillable = ['school_id', 'name', 'sign', 'remark', 'enabled'];
    
    /** @return BelongsTo */
    function school() { return $this->belongsTo('App\Models\School'); }
    
    /** @return HasMany */
    function evals() { return $this->hasMany('App\Models\Evaluate'); }
    
}
