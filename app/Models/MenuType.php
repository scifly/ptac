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
use Illuminate\Support\Facades\Request;
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
 * @property-read Collection|Menu[] $menus
 * @method static Builder|MenuType whereCreatedAt($value)
 * @method static Builder|MenuType whereEnabled($value)
 * @method static Builder|MenuType whereId($value)
 * @method static Builder|MenuType whereName($value)
 * @method static Builder|MenuType whereRemark($value)
 * @method static Builder|MenuType whereUpdatedAt($value)
 * @method static Builder|MenuType newModelQuery()
 * @method static Builder|MenuType newQuery()
 * @method static Builder|MenuType query()
 * @mixin Eloquent
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
