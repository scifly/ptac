<?php
namespace App\Models;

use App\Facades\Datatable;
use App\Helpers\Constant;
use App\Helpers\ModelTrait;
use Carbon\Carbon;
use Eloquent;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;
use ReflectionClass;
use ReflectionException;
use Throwable;

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
    
    /**
     * 获取指定菜单类型所包含的所有菜单对象
     *
     * @return HasMany
     */
    function menus() { return $this->hasMany('App\Models\Menu'); }
    
    /**
     * 菜单类型列表
     *
     * @return array
     */
    function index() {
        
        $columns = [
            ['db' => 'MenuType.id', 'dt' => 0],
            ['db' => 'MenuType.name', 'dt' => 1],
            ['db' => 'MenuType.remark', 'dt' => 2],
            ['db' => 'MenuType.created_at', 'dt' => 3],
            ['db' => 'MenuType.updated_at', 'dt' => 4],
            [
                'db'        => 'MenuType.enabled', 'dt' => 5,
                'formatter' => function ($d, $row) {
                    return Datatable::status($d, $row, false);
                },
            ],
        ];
        
        return Datatable::simple(
            $this->getModel(), $columns
        );
        
    }
    
    /**
     * 保存菜单类型
     *
     * @param array $data
     * @return bool
     */
    function store(array $data) {
        
        return $this->create($data) ? true : false;
        
    }
    
    /**
     * 更新菜单类型
     *
     * @param array $data
     * @param $id
     * @return bool
     */
    function modify(array $data, $id) {
        
        return $this->find($id)->update($data);
        
    }
    
    /**
     * （批量）删除菜单类型
     *
     * @param $id
     * @return bool|null
     * @throws Throwable
     */
    function remove($id = null) {
        
        return $this->del($this, $id);
        
    }
    
    /**
     * 删除指定菜单类型的所有相关数据
     *
     * @param $id
     * @throws Throwable
     */
    function purge($id) {
        
        try {
            DB::transaction(function () use ($id) {
                $this->delRelated('menu_type_id', 'Menu', $id);
                $this->find($id)->delete();
            });
        } catch (Exception $e) {
            throw $e;
        }
        
    }
    
    /**
     * 返回指定model（运营/企业/学校)对应的菜单类型id和图标id
     *
     * @param Model $model
     * @return array
     * @throws ReflectionException
     */
    function mtIds(Model $model): array {
        
        $icons = [
            'company' => Icon::whereName('fa fa-building')->first()->id,
            'corp'    => Icon::whereName('fa fa-weixin')->first()->id,
            'school'  => Icon::whereName('fa fa-university')->first()->id,
        ];
        $iconType = lcfirst((new ReflectionClass(get_class($model)))->getShortName());
        $mtType = array_search($iconType, Constant::MENU_TYPES);
        
        return [
            $icons[$iconType],
            $this->where('name', $mtType)->first()->id,
        ];
        
    }
    
}
