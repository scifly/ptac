<?php
namespace App\Models;

use App\Facades\Datatable;
use App\Helpers\ModelTrait;
use App\Helpers\Snippet;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
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
 * @method static Builder|Icon whereCreatedAt($value)
 * @method static Builder|Icon whereEnabled($value)
 * @method static Builder|Icon whereIconTypeId($value)
 * @method static Builder|Icon whereId($value)
 * @method static Builder|Icon whereName($value)
 * @method static Builder|Icon whereRemark($value)
 * @method static Builder|Icon whereUpdatedAt($value)
 * @mixin \Eloquent
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
        
        $data = self::whereEnabled(1)->get();
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
                    return sprintf(Snippet::ICON, $d, '') . $d;
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
        
        return Datatable::simple($this->getModel(), $columns, $joins);
        
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
    function remove($id) {
        
        return $this->del($this, $id);
        
    }
    
    /**
     * 删除指定图标的所有相关数据
     *
     * @param $id
     * @throws Throwable
     */
    function purge($id) {
        
        try {
            DB::transaction(function () use ($id) {
                Tab::whereIconId($id)->update(['icon_id' => null]);
                $this->find($id)->delete();
            });
        } catch (Exception $e) {
            throw $e;
        }
        
    }
    
}
