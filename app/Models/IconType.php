<?php
namespace App\Models;

use App\Helpers\ModelTrait;
use Carbon\Carbon;
use Eloquent;
use Exception;
use Illuminate\Database\Eloquent\{Builder, Model, Relations\HasMany};
use Illuminate\Support\Facades\DB;
use Throwable;

/**
 * App\Models\IconType 图标类型
 *
 * @property int $id
 * @property string $name 图标类型名称
 * @property string|null $remark 备注
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int $enabled
 * @property-read Icon[] $icons
 * @method static Builder|IconType whereCreatedAt($value)
 * @method static Builder|IconType whereEnabled($value)
 * @method static Builder|IconType whereId($value)
 * @method static Builder|IconType whereName($value)
 * @method static Builder|IconType whereRemark($value)
 * @method static Builder|IconType whereUpdatedAt($value)
 * @method static Builder|IconType newModelQuery()
 * @method static Builder|IconType newQuery()
 * @method static Builder|IconType query()
 * @mixin Eloquent
 */
class IconType extends Model {
    
    use ModelTrait;
    
    protected $fillable = ['name', 'remark', 'enabled'];
    
    /**
     * 获取指定图标类型包含的所有图标对象
     *
     * @return HasMany
     */
    function icons() { return $this->hasMany('App\Models\Icon'); }
    
    /**
     * 删除图标类型
     *
     * @param $id
     * @return bool|null
     * @throws Throwable
     */
    function remove($id = null) {
    
        try {
            DB::transaction(function () use ($id) {
                $this->purge(['Icon'], 'icon_type_id', 'reset', $id);
                $this->purge(['IconType'], 'id', 'purge', $id);
            });
        } catch (Exception $e) {
            throw $e;
        }
    
        return true;
        
    }
    
}
