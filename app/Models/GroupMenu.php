<?php
namespace App\Models;

use App\Helpers\ModelTrait;
use Carbon\Carbon;
use Eloquent;
use Illuminate\Database\Eloquent\{Builder, Relations\BelongsTo, Relations\Pivot};
use Throwable;

/**
 * App\Models\GroupMenu
 *
 * @mixin Eloquent
 * @property int $id
 * @property int $group_id
 * @property int $menu_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int|null $enabled
 * @property-read Menu $menu
 * @method static Builder|GroupMenu whereCreatedAt($value)
 * @method static Builder|GroupMenu whereEnabled($value)
 * @method static Builder|GroupMenu whereGroupId($value)
 * @method static Builder|GroupMenu whereId($value)
 * @method static Builder|GroupMenu whereMenuId($value)
 * @method static Builder|GroupMenu whereUpdatedAt($value)
 * @method static Builder|GroupMenu newModelQuery()
 * @method static Builder|GroupMenu newQuery()
 * @method static Builder|GroupMenu query()
 * @property-read Group $group
 */
class GroupMenu extends Pivot {
    
    use ModelTrait;
    
    protected $fillable = ['group_id', 'menu_id', 'enabled'];
    
    /** @return BelongsTo */
    function menu() { return $this->belongsTo('App\Models\Menu'); }
    
    /** @return BelongsTo */
    function group() { return $this->belongsTo('App\Models\Group'); }
    
    /**
     * @param null $id
     * @throws Throwable
     */
    function remove($id = null) {
        
        $this->purge($id);
        
    }
    
}
