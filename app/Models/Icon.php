<?php
namespace App\Models;

use App\Facades\Datatable;
use App\Helpers\ModelTrait;
use Carbon\Carbon;
use Eloquent;
use Exception;
use Illuminate\Database\Eloquent\{Builder, Collection, Model, Relations\BelongsTo, Relations\HasMany};
use Illuminate\Support\Facades\DB;
use Throwable;

/**
 * App\Models\Icon 图标
 *
 * @property int $id
 * @property string $name 图标的css类名
 * @property int $icon_type_id 所属图标类型ID
 * @property string|null $remark 备注
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int $enabled
 * @property-read IconType $iconType
 * @property-read Collection|Menu[] $menus
 * @property-read Collection|Tab[] $tabs
 * @property-read int|null $menus_count
 * @property-read int|null $tabs_count
 * @method static Builder|Icon whereCreatedAt($value)
 * @method static Builder|Icon whereEnabled($value)
 * @method static Builder|Icon whereIconTypeId($value)
 * @method static Builder|Icon whereId($value)
 * @method static Builder|Icon whereName($value)
 * @method static Builder|Icon whereRemark($value)
 * @method static Builder|Icon whereUpdatedAt($value)
 * @method static Builder|Icon newModelQuery()
 * @method static Builder|Icon newQuery()
 * @method static Builder|Icon query()
 * @mixin Eloquent
 */
class Icon extends Model {
    
    use ModelTrait;
    
    protected $fillable = ['name', 'remark', 'icon_type_id', 'enabled'];
    
    /**
     * 返回指定图标所属的图标类型对象
     *
     * @return BelongsTo
     */
    function iconType() { return $this->belongsTo('App\Models\IconType'); }
    
    /**
     * 返回Icon包含的菜单对象
     *
     * @return HasMany
     */
    function menus() { return $this->hasMany('App\Models\Menu'); }
    
    /**
     * 返回指定图标包含的所有卡片对象
     *
     * @return HasMany
     */
    function tabs() { return $this->hasMany('App\Models\Tab'); }
    
    /**
     * 返回Icon列表
     *
     * @return array
     */
    function icons() {
        
        $data = $this->whereEnabled(1)->get();
        $icons = [];
        foreach ($data as $datum) {
            $icons[$datum->iconType->name][$datum->id] = $datum->name;
        }
        
        return $icons;
        
    }
    
    /**
     * 图标列表
     *
     * @return array
     */
    function index() {
        
        $columns = [
            ['db' => 'Icon.id', 'dt' => 0],
            [
                'db'        => 'Icon.name', 'dt' => 1,
                'formatter' => function ($d) {
                    return $this->iconHtml($d) . $d;
                },
            ],
            ['db' => 'IconType.name as icontypename', 'dt' => 2],
            ['db' => 'Icon.created_at', 'dt' => 3],
            ['db' => 'Icon.updated_at', 'dt' => 4],
            [
                'db'        => 'Icon.enabled', 'dt' => 5,
                'formatter' => function ($d, $row) {
                    return Datatable::status($d, $row, false);
                },
            ],
        ];
        $joins = [
            [
                'table'      => 'icon_types',
                'alias'      => 'IconType',
                'type'       => 'INNER',
                'conditions' => [
                    'IconType.id = Icon.icon_type_id',
                ],
            ],
        ];
        
        return Datatable::simple($this, $columns, $joins);
        
    }
    
    /**
     * 保存图标
     *
     * @param array $data
     * @return bool
     */
    function store(array $data) {
        
        return $this->create($data) ? true : false;
        
    }
    
    /**
     * 更新图标
     *
     * @param array $data
     * @param $id
     * @return bool
     */
    function modify(array $data, $id = null) {
        
        return $id
            ? $this->find($id)->update($data)
            : $this->batch($this);
        
    }
    
    /**
     * 删除图标
     *
     * @param $id
     * @return bool|null
     * @throws Throwable
     */
    function remove($id = null) {
        
        try {
            DB::transaction(function () use ($id) {
                $this->purge(['Tab'], 'icon_id', 'reset', $id);
                $this->purge(['Icon'], 'id', 'purge', $id);
            });
        } catch (Exception $e) {
            throw $e;
        }
        
        return true;
    }
    
}
