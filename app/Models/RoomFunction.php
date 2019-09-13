<?php
namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\{Builder, Collection, Model, Relations\HasMany};
use Illuminate\Support\Carbon;

/**
 * Class RoomFunction
 *
 * @package App\Models
 * @property int $id
 * @property string $name 名称
 * @property string|null $remark 备注
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int $enabled 状态
 * @property-read Collection|RoomType[] $roomTypes
 * @property-read int|null $room_types_count
 * @method static Builder|RoomFunction newModelQuery()
 * @method static Builder|RoomFunction newQuery()
 * @method static Builder|RoomFunction query()
 * @method static Builder|RoomFunction whereCreatedAt($value)
 * @method static Builder|RoomFunction whereEnabled($value)
 * @method static Builder|RoomFunction whereId($value)
 * @method static Builder|RoomFunction whereName($value)
 * @method static Builder|RoomFunction whereRemark($value)
 * @method static Builder|RoomFunction whereUpdatedAt($value)
 * @mixin Eloquent
 */
class RoomFunction extends Model {

    protected $fillable = [
        'name', 'remark', 'enabeld'
    ];
    
    /** @return HasMany */
    function roomTypes() { return $this->hasMany('App\Models\RoomType'); }
    
}
