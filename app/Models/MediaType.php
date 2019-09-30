<?php
namespace App\Models;

use App\Helpers\ModelTrait;
use Carbon\Carbon;
use Eloquent;
use Illuminate\Database\Eloquent\{Builder, Model, Relations\HasMany};
use Throwable;

/**
 * App\Models\MediaType 媒体类型
 *
 * @property int $id
 * @property string $name 媒体类型名称
 * @property string|null $color 图标颜色
 * @property string|null $icon 图标class
 * @property string $remark 媒体类型备注
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int $enabled
 * @property-read Media[] $medias
 * @property-read int|null $medias_count
 * @method static Builder|MediaType whereCreatedAt($value)
 * @method static Builder|MediaType whereEnabled($value)
 * @method static Builder|MediaType whereId($value)
 * @method static Builder|MediaType whereName($value)
 * @method static Builder|MediaType whereColor($value)
 * @method static Builder|MediaType whereIcon($value)
 * @method static Builder|MediaType whereRemark($value)
 * @method static Builder|MediaType whereUpdatedAt($value)
 * @method static Builder|MediaType newModelQuery()
 * @method static Builder|MediaType newQuery()
 * @method static Builder|MediaType query()
 * @mixin Eloquent
 */
class MediaType extends Model {
    
    use ModelTrait;
    
    protected $fillable = ['name', 'color', 'icon', 'remark', 'enabled'];
    
    /** @return HasMany */
    function medias() { return $this->hasMany('App\Models\Media'); }
    
    /**
     * 删除媒体类型
     *
     * @param $id
     * @return bool|null
     * @throws Throwable
     */
    function remove($id) {
        
        return $this->purge($id, [
            'reset.media_type_id' => ['Message', 'Media']
        ]);
        
    }
    
}
