<?php

namespace App\Models;

use App\Facades\DatatableFacade as Datatable;
use App\Helpers\ModelTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
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
 * @property-read Collection|User[] $users
 */
class Group extends Model {
    
    use ModelTrait;
    
    protected $table = 'groups';
    
    protected $fillable = [
        'name', 'school_id', 'remark', 'enabled'
    ];
    
    /**
     * 获取指定角色下的所有用户对象
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function users() { return $this->hasMany('App\Models\User'); }
    
    /**
     * 返回指定角色所属的学校对象
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function school() { return $this->belongsTo('App\Models\School'); }
    
    /**
     * 保存角色
     *
     * @param array $data
     * @return bool
     */
    public function store(array $data) {
        
        $group = $this->create($data);
        return $group ? true : false;
        
    }
    
    /**
     * 更新角色
     *
     * @param array $data
     * @param $id
     * @return bool
     */
    public function modify(array $data, $id) {
        
        $group = $this->find($id);
        if (!$group) {
            return false;
        }
        return $group->update($data) ? true : false;
        
    }
    
    /**
     * 删除角色
     *
     * @param $id
     * @return bool
     */
    public function remove($id) {
        
        $group = $this->find($id);
        if (!$group) { return false; }
        return $this->removable($group) ? $group->delete() : false;
        
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
