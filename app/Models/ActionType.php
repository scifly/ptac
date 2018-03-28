<?php

namespace App\Models;

use Carbon\Carbon;
use App\Facades\DatatableFacade as Datatable;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\ActionType 功能HTTP请求类型
 *
 * @property int $id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string $name action类型名称
 * @property string|null $remark 备注
 * @property int $enabled
 * @method static Builder|School whereCreatedAt($value)
 * @method static Builder|School whereEnabled($value)
 * @method static Builder|School whereId($value)
 * @method static Builder|School whereName($value)
 * @method static Builder|School whereRemark($value)
 * @method static Builder|School whereUpdatedAt($value)
 * @mixin Eloquent
 */
class ActionType extends Model {

    protected $table = 'action_types';
    
    protected $fillable = ['name', 'remark', 'enabled'];
    
    /**
     * 返回指定Http请求类型包含的功能
     *
     * @param $id
     * @return Collection|\Illuminate\Support\Collection|static[]
     */
    function actions($id) {
        
        return Action::whereRaw('FIND_IN_SET(' . $id . ', action_type_ids')->get();
        
    }
    
    /**
     * 保存Http请求类型
     *
     * @param array $data
     * @return bool
     */
    function store(array $data) {
        
        return $this->create($data) ? true : false;
        
    }
    
    /**
     * 更新Http请求类型
     *
     * @param array $data
     * @param $id
     * @return bool
     */
    function modify(array $data, $id) {
        
        $at = $this->find($id);
        if (!$at) { return false; }
        
        return $at->update($data) ? true : false;
        
    }
    
    /**
     * Http请求类型列表
     *
     * @return array
     */
    function datatable() {
        
        $columns = [
            ['db' => 'ActionType.id', 'dt' => 0],
            ['db' => 'ActionType.name', 'dt' => 1],
            ['db' => 'ActionType.remark', 'dt' => 2],
            ['db' => 'ActionType.created_at', 'dt' => 3],
            ['db' => 'ActionType.updated_at', 'dt' => 4],
            [
                'db' => 'ActionType.enabled', 'dt' => 5,
                'formatter' => function ($d, $row) {
                    return Datatable::dtOps($d, $row, false);
                }
            ]
        ];
        
        return Datatable::simple(
            $this->getModel(), $columns
        );
        
    }
    
}
