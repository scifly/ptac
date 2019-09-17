<?php
namespace App\Models;

use App\Helpers\ModelTrait;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * Class Prize - 奖励/处罚
 *
 * @package App\Models
 * @property int $id
 * @property int $school_id 所属学校id
 * @property string $name 名称
 * @property int $score 分数
 * @property string|null $remark 备注（奖惩内容）
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int $enabled
 * @method static Builder|Prize newModelQuery()
 * @method static Builder|Prize newQuery()
 * @method static Builder|Prize query()
 * @method static Builder|Prize whereCreatedAt($value)
 * @method static Builder|Prize whereEnabled($value)
 * @method static Builder|Prize whereId($value)
 * @method static Builder|Prize whereName($value)
 * @method static Builder|Prize whereRemark($value)
 * @method static Builder|Prize whereSchoolId($value)
 * @method static Builder|Prize whereScore($value)
 * @method static Builder|Prize whereUpdatedAt($value)
 * @mixin Eloquent
 * @property-read School $school
 */
class Prize extends Model {
    
    use ModelTrait;
    
    protected $fillable = ['school_id', 'name', 'score', 'remark', 'enabled'];
    
    /** @return BelongsTo */
    function school() { return $this->belongsTo('App\Models\School'); }
    
}
