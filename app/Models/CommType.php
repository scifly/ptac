<?php

namespace App\Models;

use App\Facades\DatatableFacade as Datatable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\CommType 通信方式
 *
 * @property int $id
 * @property string $name 通信方式名称
 * @property string $remark 通信方式备注
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property int $enabled
 * @method static Builder|CommType whereCreatedAt($value)
 * @method static Builder|CommType whereEnabled($value)
 * @method static Builder|CommType whereId($value)
 * @method static Builder|CommType whereName($value)
 * @method static Builder|CommType whereRemark($value)
 * @method static Builder|CommType whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class CommType extends Model {
    
    protected $table = 'comm_types';
    
    protected $fillable = ['name', 'remark', 'enabled'];

    public function messages(){
        return $this->hasMany('App\Models\Messages');
    }
    
    public function datatable() {
        
        $columns = [
            ['db' => 'CommType.id', 'dt' => 0],
            ['db' => 'CommType.name', 'dt' => 1],
            ['db' => 'CommType.remark', 'dt' => 2],
            ['db' => 'CommType.created_at', 'dt' => 3],
            ['db' => 'CommType.updated_at', 'dt' => 4],
            [
                'db' => 'CommType.enabled', 'dt' => 5,
                'formatter' => function ($d, $row) {
                    return Datatable::dtOps($this, $d, $row);
                }
            ],
        ];
        return Datatable::simple($this, $columns);
        
    }
    
}
