<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Group
 *
 * @property int $id
 * @property string $name 角色名称
 * @property string $remark 角色备注
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property int $enabled
 * @method static Builder|Group whereCreatedAt($value)
 * @method static Builder|Group whereEnabled($value)
 * @method static Builder|Group whereId($value)
 * @method static Builder|Group whereName($value)
 * @method static Builder|Group whereRemark($value)
 * @method static Builder|Group whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Group extends Model {

    protected $table='groups';
    
    protected $fillable = [
        'name', 'remark', 'enabled'
    ];
    
    public function users() { return $this->hasMany('App\Model\User'); }


    public function datatable() {

        $columns = [
            ['db' => 'Group.id', 'dt' => 0],
            ['db' => 'Group.name', 'dt' => 1],
            ['db' => 'Group.remark', 'dt' => 2],
            ['db' => 'Group.created_at', 'dt' => 3],
            ['db' => 'Group.updated_at', 'dt' => 4],
            [
                'db' => 'Group.enabled', 'dt' => 5,
                'formatter' => function($d, $row) {
                    return Datatable::dtOps($this, $d, $row);
                }
            ]
        ];

        return Datatable::simple($this, $columns);

    }
}
