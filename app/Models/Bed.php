<?php
namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\{Builder, Model, Relations\BelongsTo};
use Illuminate\Support\Carbon;

/**
 * Class Bed
 *
 * @package App\Models
 * @property int $id
 * @property int $room_id 所属寝室id
 * @property int $student_id 所属学生id
 * @property string $name 床位号
 * @property int|null $position 0 - 下铺，1 - 上铺
 * @property string|null $remark 备注
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int $enabled 状态
 * @property-read Room $room
 * @property-read Student $student
 * @method static Builder|Bed newModelQuery()
 * @method static Builder|Bed newQuery()
 * @method static Builder|Bed query()
 * @method static Builder|Bed whereCreatedAt($value)
 * @method static Builder|Bed whereEnabled($value)
 * @method static Builder|Bed whereId($value)
 * @method static Builder|Bed whereName($value)
 * @method static Builder|Bed wherePosition($value)
 * @method static Builder|Bed whereRemark($value)
 * @method static Builder|Bed whereRoomId($value)
 * @method static Builder|Bed whereStudentId($value)
 * @method static Builder|Bed whereUpdatedAt($value)
 * @mixin Eloquent
 */
class Bed extends Model {
    
    protected $fillable = [
        'room_id', 'student_id', 'name',
        'position', 'remark', 'enabled'
    ];
    
    /** @return BelongsTo */
    function student() { return $this->belongsTo('App\Models\Student'); }
    
    /** @return BelongsTo */
    function room() { return $this->belongsTo('App\Models\Room'); }
    
    function index() { }
    
    function store() { }
    
    function modify() { }
    
    function remove() { }
    
}