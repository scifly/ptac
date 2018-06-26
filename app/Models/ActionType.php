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
use Illuminate\Support\Facades\DB;

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
    
    use ModelTrait;
    
    protected $table = 'action_types';
    
    protected $fillable = ['name', 'remark', 'enabled'];
    
    /**
     * Http请求类型列表
     *
     * @return array
     */
    function index() {
        
        $columns = [
            ['db' => 'ActionType.id', 'dt' => 0],
            ['db' => 'ActionType.name', 'dt' => 1],
            ['db' => 'ActionType.remark', 'dt' => 2],
            ['db' => 'ActionType.created_at', 'dt' => 3],
            ['db' => 'ActionType.updated_at', 'dt' => 4],
            [
                'db'        => 'ActionType.enabled', 'dt' => 5,
                'formatter' => function ($d, $row) {
                    return Datatable::dtOps($d, $row, false);
                },
            ],
        ];
        
        return Datatable::simple($this->getModel(), $columns);
        
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
        
        return $this->find($id)->update($data);
        
    }
    
    /**
     * 删除Http请求类型
     *
     * @param $id
     * @return bool|null
     * @throws Exception
     */
    function remove($id = null) {
        
        return $this->del($this, $id);
        
    }
    
    /**
     * 删除指定HTTP请求类型的所有数据
     *
     * @param $id
     * @return bool
     * @throws Exception
     */
    function purge($id) {
        
        try {
            DB::transaction(function () use ($id) {
                (new Action)->removeActionType($id);
                $this->find($id)->delete();
            });
        } catch (Exception $e) {
            throw $e;
        }
        
        return true;
        
    }
    
    /**
     * 返回指定Http请求类型包含的功能
     *
     * @param $id
     * @return Collection|\Illuminate\Support\Collection|static[]
     */
    function actions($id) {
        
        return Action::whereRaw('FIND_IN_SET(' . $id . ', action_type_ids)')->get();
        
    }
    
}
