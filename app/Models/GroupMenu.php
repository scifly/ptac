<?php
namespace App\Models;

use Carbon\Carbon;
use Eloquent;
use Illuminate\Database\Eloquent\{Builder, Model, Relations\BelongsTo};

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
 */
class GroupMenu extends Model {
    
    protected $table = 'groups_menus';
    
    protected $fillable = ['group_id', 'menu_id', 'enabled'];
    
    /**
     * 返回指定记录所属的菜单对象
     *
     * @return BelongsTo
     */
    function menu() { return $this->belongsTo('App\Models\Menu'); }
    
}
