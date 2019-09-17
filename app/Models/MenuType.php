<?php
namespace App\Models;

use App\Helpers\ModelTrait;
use Carbon\Carbon;
use Eloquent;
use Exception;
use Illuminate\Database\Eloquent\{Builder, Collection, Model, Relations\HasMany};
use Illuminate\Support\Facades\{DB, Request};
use Throwable;

/**
 * App\Models\MenuType 菜单类型
 *
 * @property int $id
 * @property string $name 菜单类型
 * @property string $color 图标颜色
 * @property string $icon 图标class
 * @property string $remark 备注
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int $enabled
 * @property-read Collection|Menu[] $menus
 * @property-read int|null $menus_count
 * @method static Builder|MenuType whereCreatedAt($value)
 * @method static Builder|MenuType whereEnabled($value)
 * @method static Builder|MenuType whereId($value)
 * @method static Builder|MenuType whereName($value)
 * @method static Builder|MenuType whereColor($value)
 * @method static Builder|MenuType whereIcon($value)
 * @method static Builder|MenuType whereRemark($value)
 * @method static Builder|MenuType whereUpdatedAt($value)
 * @method static Builder|MenuType newModelQuery()
 * @method static Builder|MenuType newQuery()
 * @method static Builder|MenuType query()
 * @mixin Eloquent
 */
class MenuType extends Model {
    
    use ModelTrait;
    
    protected $fillable = ['name', 'color', 'icon', 'remark', 'enabled'];
    
    /** @return HasMany */
    function menus() { return $this->hasMany('App\Models\Menu'); }
    
    /**
     * （批量）删除菜单类型
     *
     * @param $id
     * @return bool|null
     * @throws Throwable
     */
    function remove($id = null) {
        
        try {
            DB::transaction(function () use ($id) {
                $ids = $id ? [$id] : array_values(Request::input('ids'));
                $menuIds = Menu::whereIn('menu_type_id', $ids)
                    ->pluck('id')->toArray();
                Request::replace(['ids' => $menuIds]);
                (new Menu)->remove();
                Request::replace(['ids' => $ids]);
                $this->purge(['MenuType'], 'id');
            });
        } catch (Exception $e) {
            throw $e;
        }
        
        return true;
        
    }
    
}
