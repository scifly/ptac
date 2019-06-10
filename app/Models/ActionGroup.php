<?php
namespace App\Models;

use Carbon\Carbon;
use Eloquent;
use Illuminate\Database\Eloquent\{Builder, Relations\Pivot};

/**
 * App\Models\ActionGroup
 *
 * @property int $id
 * @property int $action_id
 * @property int $group_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int $enabled
 * @method static Builder|ActionGroup whereActionId($value)
 * @method static Builder|ActionGroup whereCreatedAt($value)
 * @method static Builder|ActionGroup whereEnabled($value)
 * @method static Builder|ActionGroup whereGroupId($value)
 * @method static Builder|ActionGroup whereId($value)
 * @method static Builder|ActionGroup whereUpdatedAt($value)
 * @method static Builder|ActionGroup newModelQuery()
 * @method static Builder|ActionGroup newQuery()
 * @method static Builder|ActionGroup query()
 * @mixin Eloquent
 */
class ActionGroup extends Pivot {
    
    protected $fillable = ['action_id', 'group_id', 'enabled'];
    
}
