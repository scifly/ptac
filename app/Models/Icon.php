<?php

namespace App\Models;

use App\Facades\DatatableFacade as Datatable;
use App\Helpers\ModelTrait;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * App\Models\Icon 图标
 *
 * @property int $id
 * @property string $name 图标的css类名
 * @property int $icon_type_id 所属图标类型ID
 * @property string|null $remark 备注
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property int $enabled
 * @property-read \App\Models\IconType $iconType
 * @method static Builder|Icon whereCreatedAt($value)
 * @method static Builder|Icon whereEnabled($value)
 * @method static Builder|Icon whereIconTypeId($value)
 * @method static Builder|Icon whereId($value)
 * @method static Builder|Icon whereName($value)
 * @method static Builder|Icon whereRemark($value)
 * @method static Builder|Icon whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property-read Collection|Menu[] $menus
 * @property-read Collection|Tab[] $tabs
 */
class Icon extends Model {

    use ModelTrait;

    protected $fillable = ['name', 'remark', 'icon_type_id', 'enabled'];

    /**
     * 返回指定图标所属的图标类型对象
     *
     * @return BelongsTo
     */
    public function iconType() { return $this->belongsTo('App\Models\IconType'); }

    /**
     * 返回Icon包含的菜单对象
     *
     * @return HasMany
     */
    public function menus() { return $this->hasMany('App\Models\Menu'); }

    /**
     * 返回指定图标包含的所有卡片对象
     *
     * @return HasMany
     */
    public function tabs() { return $this->hasMany('App\Models\Tab'); }

    /**
     * 返回Icon列表
     *
     * @return array
     */
    static function icons() {

        $data = self::whereEnabled(1)->get();
        $icons = [];
        foreach ($data as $datum) {
            $icons[$datum->iconType->name][$datum->id] = $datum->name;
        }
        
        return $icons;

    }

    /**
     * 保存图标
     *
     * @param array $data
     * @return bool
     */
    static function store(array $data) {

        $icon = self::create($data);

        return $icon ? true : false;

    }

    /**
     * 更新图标
     *
     * @param array $data
     * @param $id
     * @return bool
     */
    static function modify(array $data, $id) {

        $icon = self::find($id);
        if (!$icon) {
            return false;
        }

        return $icon->update($data) ? true : false;

    }
    
    /**
     * 删除图标
     *
     * @param $id
     * @return bool|null
     * @throws Exception
     */
    static function remove($id) {

        $icon = self::find($id);
        if (!$icon) { return false; }
        
        return $icon->removable($icon) ? $icon->delete() : false;

    }
    
    /**
     * 图标列表
     *
     * @return array
     */
    static function datatable() {

        $columns = [
            ['db' => 'Icon.id', 'dt' => 0],
            [
                'db' => 'Icon.name', 'dt' => 1,
                'formatter' => function ($d) {
                    return '<i class="' . $d . '"></i>&nbsp;' . $d;
                },
            ],
            ['db' => 'IconType.name as icontypename', 'dt' => 2],
            ['db' => 'Icon.created_at', 'dt' => 3],
            ['db' => 'Icon.updated_at', 'dt' => 4],
            [
                'db' => 'Icon.enabled', 'dt' => 5,
                'formatter' => function ($d, $row) {
                    return Datatable::dtOps($d, $row);
                },
            ],
        ];
        $joins = [
            [
                'table' => 'icon_types',
                'alias' => 'IconType',
                'type' => 'INNER',
                'conditions' => [
                    'IconType.id = Icon.icon_type_id',
                ],
            ],
        ];

        return Datatable::simple(self::getModel(), $columns, $joins);

    }

}
