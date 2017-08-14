<?php

namespace App\Models;

use App\Facades\DatatableFacade as Datatable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Icon
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
 */
class Icon extends Model {
    
    protected $fillable = ['name', 'remark', 'icon_type_id', 'enabled'];
    
    public function iconType() {
        
        return $this->belongsTo('App\Models\IconType');
        
    }
    
    /**
     * 返回Icon包含的菜单
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function menus() {
        
        return $this->hasMany('App\Models\Menu');
        
    }
    
    /**
     * 返回Icon列表
     *
     * @return array
     */
    public function icons() {
    
        $data = $this->whereEnabled(1)->get();
        $icons = [];
        foreach ($data as $icon) {
            $icons[$icon->iconType->name][$icon->id] = $icon->name;
        }
        
        return $icons;
        
    }
    
    public function datatable() {
        
        $columns = [
            ['db' => 'Icon.id', 'dt' => 0],
            [
                'db' => 'Icon.name', 'dt' => 1,
                'formatter' => function ($d) {
                    return '<i class="' . $d . '"></i>&nbsp;' . $d;
                }
            ],
            ['db' => 'IconType.name as icontypename', 'dt' => 2],
            ['db' => 'Icon.remark', 'dt' => 3],
            ['db' => 'Icon.created_at', 'dt' => 4],
            ['db' => 'Icon.updated_at', 'dt' => 5],
            [
                'db' => 'Icon.enabled', 'dt' => 6,
                'formatter' => function($d, $row) {
                    return Datatable::dtOps($this, $d, $row);
                }
            ]
        ];
        $joins = [
            [
                'table' => 'icon_types',
                'alias' => 'IconType',
                'type' => 'INNER',
                'conditions' => [
                    'IconType.id = Icon.icon_type_id'
                ]
            ]
        ];
        return Datatable::simple($this, $columns, $joins);
        
    }
    
}
