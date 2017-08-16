<?php

namespace App\Models;

use App\Facades\DatatableFacade as Datatable;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\IconType
 *
 * @property int $id
 * @property string $name 图标类型名称
 * @property string|null $remark 备注
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property int $enabled
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Icon[] $icons
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\IconType whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\IconType whereEnabled($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\IconType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\IconType whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\IconType whereRemark($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\IconType whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class IconType extends Model {
    
    protected $fillable = ['name', 'remark', 'enabled'];
    
    public function icons() {
        
        return $this->hasMany('App\Models\Icon');
        
    }
    
    public function datatable() {
        
        $columns = [
            ['db' => 'IconType.id', 'dt' => 0],
            ['db' => 'IconType.name', 'dt' => 1],
            ['db' => 'IconType.remark', 'dt' => 2],
            ['db' => 'IconType.created_at', 'dt' => 3],
            ['db' => 'IconType.updated_at', 'dt' => 4],
            [
                'db' => 'IconType.enabled', 'dt' => 5,
                'formatter' => function ($d, $row) {
                    return Datatable::dtOps($this, $d, $row);
                }
            ]
        ];
        
        return Datatable::simple($this, $columns);
    }
    
}
