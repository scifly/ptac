<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use App\Facades\DatatableFacade as Datatable;

/**
 * App\Models\DepartmentType
 *
 * @property int $id
 * @property string $name 部门类型名称
 * @property string|null $remark 备注
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property int $enabled
 * @property-read Collection|\App\Models\Department[] $departments
 * @method static Builder|DepartmentType whereCreatedAt($value)
 * @method static Builder|DepartmentType whereEnabled($value)
 * @method static Builder|DepartmentType whereId($value)
 * @method static Builder|DepartmentType whereName($value)
 * @method static Builder|DepartmentType whereRemark($value)
 * @method static Builder|DepartmentType whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class DepartmentType extends Model {
    
    protected $fillable = ['name', 'remark', 'enabled'];
    
    /**
     * 获取指定部门类型包含的所有部门对象
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function departments() { return $this->hasMany('App\Models\Department'); }
    
    /**
     * 创建部门类型
     *
     * @param array $data
     * @return $this|Model
     */
    public function store(array $data) {
        
        return $this->create($data);
        
    }
    
    /**
     * 更新不猛类型
     *
     * @param array $data
     * @param $id
     * @return bool
     */
    public function modify(array $data, $id) {
        
        return $this->find($id)->update($data);
        
    }
    
    /**
     * 删除部门类型
     *
     * @param $id
     * @return bool|null
     */
    public function remove($id) {
        
        return $this->find($id)->delete();
        
    }
    
    public function datatable() {
        
        $columns = [
            ['db' => 'DepartmentType.id', 'dt' => 0],
            ['db' => 'DepartmentType.name', 'dt' => 1],
            ['db' => 'DepartmentType.remark', 'dt' => 2],
            ['db' => 'DepartmentType.created_at', 'dt' => 3],
            ['db' => 'DepartmentType.updated_at', 'dt' => 4],
            [
                'db' => 'DepartmentType.enabled', 'dt' => 5,
                'formatter' => function($d, $row) {
                    return Datatable::dtOps($this, $d, $row);
                }
            ],
        ];
        
        return Datatable::simple($this, $columns);
        
    }
    
}
