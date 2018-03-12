<?php

namespace App\Models;

use App\Facades\DatatableFacade as Datatable;
use App\Helpers\ModelTrait;
use Carbon\Carbon;
use Eloquent;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * App\Models\DepartmentType 部门类型
 *
 * @property int $id
 * @property string $name 部门类型名称
 * @property string|null $remark 备注
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int $enabled
 * @property-read Collection|Department[] $departments
 * @method static Builder|DepartmentType whereCreatedAt($value)
 * @method static Builder|DepartmentType whereEnabled($value)
 * @method static Builder|DepartmentType whereId($value)
 * @method static Builder|DepartmentType whereName($value)
 * @method static Builder|DepartmentType whereRemark($value)
 * @method static Builder|DepartmentType whereUpdatedAt($value)
 * @mixin Eloquent
 */
class DepartmentType extends Model {

    use ModelTrait;

    protected $fillable = ['name', 'remark', 'enabled'];

    /**
     * 获取指定部门类型包含的所有部门对象
     *
     * @return HasMany
     */
    function departments() { return $this->hasMany('App\Models\Department'); }

    /**
     * 创建部门类型
     *
     * @param array $data
     * @return $this|Model
     */
    function store(array $data) {

        return self::create($data);

    }

    /**
     * 更新部门类型
     *
     * @param array $data
     * @param $id
     * @return bool
     */
    function modify(array $data, $id) {

        return self::find($id)->update($data);

    }
    
    /**
     * 删除部门类型
     *
     * @param $id
     * @return bool|null
     * @throws Exception
     */
    function remove($id) {

        $dt = self::find($id);
        if (!$dt) { return false; }
        $removed = self::removable($dt) ? $dt->delete() : false;

        return $removed ? true : false;

    }
    
    /**
     * 部门类型列表
     *
     * @return array
     */
    function datatable() {

        $columns = [
            ['db' => 'DepartmentType.id', 'dt' => 0],
            ['db' => 'DepartmentType.name', 'dt' => 1],
            ['db' => 'DepartmentType.remark', 'dt' => 2],
            ['db' => 'DepartmentType.created_at', 'dt' => 3],
            ['db' => 'DepartmentType.updated_at', 'dt' => 4],
            [
                'db' => 'DepartmentType.enabled', 'dt' => 5,
                'formatter' => function ($d, $row) {
                    return Datatable::dtOps($d, $row, false);
                },
            ],
        ];

        return Datatable::simple(
            $this->getModel(), $columns
        );

    }

}
