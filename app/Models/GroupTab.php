<?php
namespace App\Models;

use Carbon\Carbon;
use Eloquent;
use Illuminate\Database\Eloquent\{Builder, Relations\BelongsTo, Relations\Pivot};

/**
 * App\Models\GroupTab
 *
 * @mixin Eloquent
 * @property int $id
 * @property int $group_id
 * @property int $tab_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int $enabled
 * @method static Builder|GroupTab whereCreatedAt($value)
 * @method static Builder|GroupTab whereEnabled($value)
 * @method static Builder|GroupTab whereGroupId($value)
 * @method static Builder|GroupTab whereId($value)
 * @method static Builder|GroupTab whereTabId($value)
 * @method static Builder|GroupTab whereUpdatedAt($value)
 * @method static Builder|GroupTab newModelQuery()
 * @method static Builder|GroupTab newQuery()
 * @method static Builder|GroupTab query()
 * @property-read Group $group
 * @property-read Tab $tab
 */
class GroupTab extends Pivot {
    
    protected $fillable = ['group_id', 'tab_id', 'enabled'];
    
    /** @return BelongsTo */
    function group() { return $this->belongsTo('App\Models\Group'); }
    
    /** @return BelongsTo */
    function tab() { return $this->belongsTo('App\Models\Tab'); }
    
}
