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
 */
class Tab extends Model {
    
    protected $fillable = ['name', 'remark', 'enabled'];
    
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
    
    public function datatable() {
        
        $columns = [
            ['db' => 'Tab.id', 'dt' => '0'],
            ['db' => 'Tab.name', 'dt' => '1'],
            ['db' => 'Tab.remark', 'dt' => '2'],
            ['db' => 'Tab.created_at', 'dt' => '3'],
            ['db' => 'Tab.updated_at', 'dt' => '4'],
            [
                'db' => 'Tab.enabled', 'dt' => '5',
                'formatter' => function($d, $row) {
                    return Datatable::dtOps($this, $d, $row);
                }
            ]
        ];
        
        return Datatable::simple($this, $columns);
        
    }
    
}
