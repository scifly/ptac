<?php
namespace App\Models;

use App\Facades\Datatable;
use App\Helpers\ModelTrait;
use Carbon\Carbon;
use Eloquent;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;
use Throwable;

/**
 * App\Models\ProcedureType 审批流程类型
 *
 * @property int $id
 * @property string $name 流程种类名称
 * @property string $remark 流程种类备注
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int $enabled
 * @method static Builder|ProcedureType whereCreatedAt($value)
 * @method static Builder|ProcedureType whereEnabled($value)
 * @method static Builder|ProcedureType whereId($value)
 * @method static Builder|ProcedureType whereName($value)
 * @method static Builder|ProcedureType whereRemark($value)
 * @method static Builder|ProcedureType whereUpdatedAt($value)
 * @mixin Eloquent
 * @property-read Collection|Procedure[] $procedures
 */
class ProcedureType extends Model {
    
    use ModelTrait;
    
    protected $table = 'procedure_types';
    
    protected $fillable = ['name', 'remark', 'enabled'];
    
    /**
     * 返回指定审批流程类型包含的所有审批流程对象
     *
     * @return HasMany
     */
    function procedures() { return $this->hasMany('App\Models\Procedure'); }
    
    /**
     * 流程种类列表
     *
     * @return array
     */
    function index() {
        
        $columns = [
            ['db' => 'ProcedureType.id', 'dt' => 0],
            ['db' => 'ProcedureType.name', 'dt' => 1],
            ['db' => 'ProcedureType.remark', 'dt' => 2],
            ['db' => 'ProcedureType.created_at', 'dt' => 3],
            ['db' => 'ProcedureType.updated_at', 'dt' => 4],
            [
                'db'        => 'ProcedureType.enabled', 'dt' => 5,
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
     * 保存审批流程类型
     *
     * @param array $data
     * @return bool
     */
    function store(array $data) {
        
        return $this->create($data) ? true : false;
        
    }
    
    /**
     * 更新审批流程类型
     *
     * @param array $data
     * @param $id
     * @return bool
     */
    function modify(array $data, $id) {
        
        return $this->find($id)->update($data);
        
    }
    
    /**
     * 删除审批流程类型
     *
     * @param $id
     * @return bool
     * @throws Throwable
     */
    function remove($id) {
        
        return $this->del($this, $id);
        
    }
    
    /**
     * 删除指定审批流程类型的所有相关数据
     *
     * @param $id
     * @throws Throwable
     */
    function purge($id) {
        
        try {
            DB::transaction(function () use ($id) {
                $this->delRelated('procedure_type_id', 'Procedure', $id);
                $this->find($id)->delete();
            });
        } catch (Exception $e) {
            throw $e;
        }
        
    }
    
}
