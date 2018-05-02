<?php

namespace App\Models;

use App\Helpers\Constant;
use App\Helpers\ModelTrait;
use Carbon\Carbon;
use App\Facades\DatatableFacade as Datatable;
use Eloquent;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use ReflectionClass;
use ReflectionException;

/**
 * App\Models\MenuType 菜单类型
 *
 * @property int $id
 * @property string $name 菜单类型
 * @property string $remark 备注
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int $enabled
 * @method static Builder|MenuType whereCreatedAt($value)
 * @method static Builder|MenuType whereEnabled($value)
 * @method static Builder|MenuType whereId($value)
 * @method static Builder|MenuType whereName($value)
 * @method static Builder|MenuType whereRemark($value)
 * @method static Builder|MenuType whereUpdatedAt($value)
 * @mixin Eloquent
 * @property-read Collection|\App\Models\Menu[] $menus
 */
class MenuType extends Model {

    use ModelTrait;

    protected $fillable = ['name', 'remark', 'enabled'];
 
    protected $icons;
    
    function __construct(array $attributes = []) {
        
        parent::__construct($attributes);
        $this->icons = [
            'company' => Icon::whereName('fa fa-building')->first()->id,
            'corp'    => Icon::whereName('fa fa-weixin')->first()->id,
            'school'  => Icon::whereName('fa fa-university')->first()->id,
        ];
        
    }
    
    /**
     * 获取指定菜单类型所包含的所有菜单对象
     *
     * @return HasMany
     */
    function menus() { return $this->hasMany('App\Models\Menu'); }
    
    /**
     * 保存菜单类型
     *
     * @param array $data
     * @return bool
     */
    function store(array $data) {
        
        $menuType = $this->create($data);

        return $menuType ? true : false;

    }

    /**
     * 更新菜单类型
     *
     * @param array $data
     * @param $id
     * @return bool
     */
    function modify(array $data, $id) {
        
        $menuType = $this->find($id);
        if (!$menuType) { return false; }

        return $menuType->update($data) ? true : false;

    }
    
    /**
     * 删除删除菜单类型
     *
     * @param $id
     * @return bool|null
     * @throws Exception
     */
    function remove($id) {
        
        $menuType = $this->find($id);
        if (!$menuType) { return false; }

        return $menuType->removable($menuType) ? $menuType->delete() : false;

    }
    
    /**
     * 返回指定model（运营/企业/学校)对应的菜单类型id和图标id
     *
     * @param Model $model
     * @return array
     * @throws ReflectionException
     */
    function mtIds(Model $model): array {
        
        $mtType = array_search(
            lcfirst((new ReflectionClass(get_class($model)))->getShortName()),
            Constant::MENU_TYPES
        );
        
        return [
            $this->icons[$mtType],
            $this->where('name', $mtType)->first()->id
        ];
        
    }
    
    function datatable() {
    
        $columns = [
            ['db' => 'MenuType.id', 'dt' => 0],
            ['db' => 'MenuType.name', 'dt' => 1],
            ['db' => 'MenuType.remark', 'dt' => 2],
            ['db' => 'MenuType.created_at', 'dt' => 3],
            ['db' => 'MenuType.updated_at', 'dt' => 4],
            [
                'db' => 'MenuType.enabled', 'dt' => 5,
                'formatter' => function ($d, $row) {
                    return Datatable::dtOps($d, $row, false);
                }
            ]
        ];
    
        return Datatable::simple(
            $this->getModel(), $columns
        );
        
    }

}
