<?php
namespace App\Models;

use Carbon\Carbon;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

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
 */
class GroupTab extends Model {
    
    protected $table = 'groups_tabs';
    
    protected $fillable = ['group_id', 'tab_id', 'enabled'];
    
}
