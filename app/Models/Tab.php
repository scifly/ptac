<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use App\Facades\DatatableFacade as Datatable;

/**
 * App\Models\Tab
 *
 * @property int $id
 * @property string $name 卡片名称
 * @property string|null $remark 卡片备注
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property int $enabled
 * @method static Builder|Tab whereCreatedAt($value)
 * @method static Builder|Tab whereEnabled($value)
 * @method static Builder|Tab whereId($value)
 * @method static Builder|Tab whereName($value)
 * @method static Builder|Tab whereRemark($value)
 * @method static Builder|Tab whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Action[] $actions
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Menu[] $menus
 * @property int|null $icon_id 图标ID
 * @method static Builder|Tab whereIconId($value)
 */
class Tab extends Model {
    
    protected $fillable = [
        'name', 'remark', 'icon_id', 'enabled'
    ];
    
    public function menus() {
        
        return $this->belongsToMany('App\Models\Menu')
            ->withPivot('tab_order', 'enabled')
            ->withTimestamps();
        
    }
    
    public function actions() {
        
        return $this->belongsToMany('App\Models\Action', 'tabs_actions')
            ->withPivot('default', 'enabled')
            ->withTimestamps();
        
    }
    
    public function icon() {
        
        return $this->belongsTo('App\Models\Icon');
        
    }
    
    public function datatable() {
        
        $columns = [
            ['db' => 'Tab.id', 'dt' => 0],
            ['db' => 'Tab.name', 'dt' => 1],
            [
                'db' => 'Icon.name as iconname', 'dt' => 2,
                'formatter' => function($d) {
                    return isset($d) ? '<i class="' . $d . '"></i>&nbsp;' . $d : '[n/a]';
                }
            ],
            ['db' => 'Tab.remark', 'dt' => 3],
            ['db' => 'Tab.created_at', 'dt' => 4],
            ['db' => 'Tab.updated_at', 'dt' => 5],
            [
                'db' => 'Tab.enabled', 'dt' => 6,
                'formatter' => function($d, $row) {
                    return Datatable::dtOps($this, $d, $row);
                }
            ]
        ];
        $joins = [
            [
                'table' => 'icons',
                'alias' => 'Icon',
                'type' => 'LEFT',
                'conditions' => [
                    'Icon.id = Tab.icon_id'
                ]
                
            ]
        ];
        
        return Datatable::simple($this, $columns, $joins);
        
    }
    
}
