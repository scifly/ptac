<?php

namespace App\Models;

use App\Facades\DatatableFacade as Datatable;
use App\Helpers\ModelTrait;
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
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property int $enabled
 * @method static Builder|ProcedureType whereCreatedAt($value)
 * @method static Builder|ProcedureType whereEnabled($value)
 * @method static Builder|ProcedureType whereId($value)
 * @method static Builder|ProcedureType whereName($value)
 * @method static Builder|ProcedureType whereRemark($value)
 * @method static Builder|ProcedureType whereUpdatedAt($value)
 * @mixin \Eloquent
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
    public function procedures() { return $this->hasMany('App\Models\Procedure'); }
    
    /**
     * 流程种类列表
     *
     * @return array
     */
    static function datatable() {
        
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

        return Datatable::simple(self::getModel(), $columns);

    }
}
