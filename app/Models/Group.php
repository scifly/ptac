<?php

namespace App\Models;

use App\Facades\DatatableFacade as Datatable;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use App\Http\Requests\GroupRequest;

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
 * @property-read Collection|User[] $users
 */
class Group extends Model {
    
    protected $table = 'groups';
    
    protected $fillable = [
        'name', 'remark', 'enabled'
    ];
    
    public function users() { return $this->hasMany('App\Models\User'); }

    public function existed(GroupRequest $request, $id = NULL) {

        if (!$id) {
            $group = $this->where('name', $request->input('name'))
                ->first();
        } else {
            $group = $this->where('name', $request->input('name'))
                ->where('id', '<>' , $id)
                ->first();
        }
        return $group ? true : false;

    }
    
    /**
     * 根据角色名称获取角色对象
     * @param $groupName
     * @return Model|null|static
     */
    public function group($groupName) {
        
        return $this->where('name', $groupName)->first();
        
    }
    
    public function datatable() {
        
        $columns = [
            ['db' => 'Groups.id', 'dt' => 0],
            ['db' => 'Groups.name', 'dt' => 1],
            ['db' => 'Groups.remark', 'dt' => 2],
            ['db' => 'Groups.created_at', 'dt' => 3],
            ['db' => 'Groups.updated_at', 'dt' => 4],
            [
                'db' => 'Groups.enabled', 'dt' => 5,
                'formatter' => function ($d, $row) {
                    return Datatable::dtOps($this, $d, $row);
                }
            ]
        
        ];
        
        return Datatable::simple($this, $columns);
        
    }
}
