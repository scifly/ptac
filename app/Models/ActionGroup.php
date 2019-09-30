<?php
namespace App\Models;

use App\Helpers\ModelTrait;
use Eloquent;
use Illuminate\Database\Eloquent\{Builder, Relations\BelongsTo, Relations\Pivot};
use Illuminate\Support\Carbon;
use Throwable;

/**
 * App\Models\ActionGroup
 *
 * @property int $id
 * @property int $action_id
 * @property int $group_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int $enabled
 * @property-read Action $action
 * @property-read Group $group
 * @method static Builder|ActionGroup newModelQuery()
 * @method static Builder|ActionGroup newQuery()
 * @method static Builder|ActionGroup query()
 * @method static Builder|ActionGroup whereActionId($value)
 * @method static Builder|ActionGroup whereCreatedAt($value)
 * @method static Builder|ActionGroup whereEnabled($value)
 * @method static Builder|ActionGroup whereGroupId($value)
 * @method static Builder|ActionGroup whereId($value)
 * @method static Builder|ActionGroup whereUpdatedAt($value)
 * @mixin Eloquent
 */
class ActionGroup extends Pivot {
    
    use ModelTrait;
    
    protected $fillable = ['action_id', 'group_id', 'enabled'];
    
    /** @return BelongsTo */
    function action() { return $this->belongsTo('App\Models\Action'); }
    
    /** @return BelongsTo */
    function group() { return $this->belongsTo('App\Models\Group'); }
    
    /**
     * @param null $id
     * @return bool
     * @throws Throwable
     */
    function remove($id = null) {
        
        return $this->purge($id);
        
    }
    
}
