<?php
namespace App\Models;

use App\Helpers\ModelTrait;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Prize - 奖励/处罚
 *
 * @package App\Models
 * @property int $id
 * @property int $school_id 所属学校id
 * @property string $name 名称
 * @property int $score 分数
 * @property string|null $remark 备注（奖惩内容）
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int $enabled
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Prize newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Prize newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Prize query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Prize whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Prize whereEnabled($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Prize whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Prize whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Prize whereRemark($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Prize whereSchoolId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Prize whereScore($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Prize whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Prize extends Model {
    
    use ModelTrait;
    
    protected $fillable = ['school_id', 'name', 'score', 'remark', 'enabled'];
    
}
