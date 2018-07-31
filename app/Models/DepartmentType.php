<?php
namespace App\Models;

use App\Facades\Datatable;
use App\Helpers\Constant;
use App\Helpers\ModelTrait;
use Carbon\Carbon;
use Eloquent;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;
use ReflectionClass;
use Throwable;

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
     * 部门类型列表
     *
     * @return array
     */
    function index() {
        
        $columns = [
            ['db' => 'DepartmentType.id', 'dt' => 0],
            ['db' => 'DepartmentType.name', 'dt' => 1],
            ['db' => 'DepartmentType.remark', 'dt' => 2],
            ['db' => 'DepartmentType.created_at', 'dt' => 3],
            ['db' => 'DepartmentType.updated_at', 'dt' => 4],
            [
                'db'        => 'DepartmentType.enabled', 'dt' => 5,
                'formatter' => function ($d, $row) {
                    return Datatable::dtOps($d, $row, false);
                },
            ],
        ];
        
        return Datatable::simple(
            $this->getModel(), $columns
        );
        
    }
    
    /**
     * 创建部门类型
     *
     * @param array $data
     * @return bool
     */
    function store(array $data) {
        
        return $this->create($data) ? true : false;
        
    }
    
    /**
     * 更新部门类型
     *
     * @param array $data
     * @param $id
     * @return bool
     */
    function modify(array $data, $id) {
        
        return $this->find($id)->update($data);
        
    }
    
    /**
     * 删除部门类型
     *
     * @param $id
     * @return bool|null
     * @throws Exception
     */
    function remove($id = null) {
        
        return $this->del($this, $id);
        
    }
    
    /**
     * 删除指定部门类型的所有数据
     *
     * @param $id
     * @return bool
     * @throws Throwable
     */
    function purge($id) {
        
        try {
            DB::transaction(function () use ($id) {
                $this->delRelated('department_type_id', 'Department', $id);
                $this->find($id)->delete();
            });
        } catch (Exception $e) {
            throw $e;
        }
        
        return true;
        
    }
    
    /**
     * 返回指定model（运营/企业/学校/年级/班级)对应的部门类型id
     *
     * @param Model $model
     * @return int|mixed
     * @throws \ReflectionException
     */
    function dtId(Model $model) {
        
        $dtType = array_search(
            lcfirst((new ReflectionClass(get_class($model)))->getShortName()),
            Constant::DEPARTMENT_TYPES
        );
        $dtType = $dtType ? $dtType : '班级';
        
        return [
            $dtType,
            $this->where('name', $dtType)->first()->id,
        ];
        
    }
    
}
