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
     * 保存审批流程类型
     *
     * @param array $data
     * @return bool
     */
    function store(array $data) {
        
        $pt = self::create($data);
        
        return $pt ? true : false;
        
    }
    
    /**
     * 更新审批流程类型
     *
     * @param array $data
     * @param $id
     * @return bool
     */
    function modify(array $data, $id) {
        
        $pt = self::find($id);
        if (!$pt) { return false; }
        
        return $pt->update($data) ? true : false;
        
    }
    
    /**
     * 删除审批流程类型
     *
     * @param $id
     * @return bool
     * @throws Exception
     */
    function remove($id) {
        
        $pt = self::find($id);
        if (!$pt) { return false; }
        
        return $pt->delete() ? true : false;
        
    }
    
    /**
     * 流程种类列表
     *
     * @return array
     */
    function datatable() {
        
        $columns = [
            ['db' => 'ProcedureType.id', 'dt' => 0],
            ['db' => 'ProcedureType.name', 'dt' => 1],
            ['db' => 'ProcedureType.remark', 'dt' => 2],
            ['db' => 'ProcedureType.created_at', 'dt' => 3],
            ['db' => 'ProcedureType.updated_at', 'dt' => 4],
            [
                'db' => 'ProcedureType.enabled', 'dt' => 5,
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
