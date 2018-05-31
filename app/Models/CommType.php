<?php

namespace App\Models;

use App\Facades\DatatableFacade as Datatable;
use App\Helpers\ModelTrait;
use Carbon\Carbon;
use Doctrine\Common\Collections\Collection;
use Eloquent;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use ReflectionException;

/**
 * App\Models\CommType 通信方式
 *
 * @property int $id
 * @property string $name 通信方式名称
 * @property string $remark 通信方式备注
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int $enabled
 * @method static Builder|CommType whereCreatedAt($value)
 * @method static Builder|CommType whereEnabled($value)
 * @method static Builder|CommType whereId($value)
 * @method static Builder|CommType whereName($value)
 * @method static Builder|CommType whereRemark($value)
 * @method static Builder|CommType whereUpdatedAt($value)
 * @mixin Eloquent
 * @property-read Collection|Message[] $messages
 */
class CommType extends Model {

    use ModelTrait;
    
    protected $table = 'comm_types';

    protected $fillable = ['name', 'remark', 'enabled'];

    /**
     * 返回指定通信方式包含的所有消息对象
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    function messages() { return $this->hasMany('App\Models\Message'); }
    
    /**
     * 保存通信类型
     * 
     * @param array $data
     * @return bool
     */
    function store(array $data) {
        
        return $this->create($data) ? true : false;
        
    }
    
    /**
     * 更新通信类型
     *
     * @param array $data
     * @param $id
     * @return bool
     */
    function modify(array $data, $id) {
        
        $ct = $this->find($id);
        if (!$ct) { return false; }
        
        return $ct->update($data);
        
    }
    
    /**
     * 移除通信类型
     *
     * @param $id
     * @return bool|null
     * @throws ReflectionException
     * @throws Exception
     */
    function remove($id) {
        
        $ct = $this->find($id);
        if (!$ct) { return false; }
        
        return $this->removable($ct) ? $ct->delete() : false;
        
    }
    
    /**
     * 通信方式列表
     *
     * @return array
     */
    function datatable() {

        $columns = [
            ['db' => 'CommType.id', 'dt' => 0],
            ['db' => 'CommType.name', 'dt' => 1],
            ['db' => 'CommType.remark', 'dt' => 2],
            ['db' => 'CommType.created_at', 'dt' => 3],
            ['db' => 'CommType.updated_at', 'dt' => 4],
            [
                'db' => 'CommType.enabled', 'dt' => 5,
                'formatter' => function ($d, $row) {
                    return Datatable::dtOps($d, $row, false);
                },
            ],
        ];

        return Datatable::simple($this->getModel(), $columns);

    }

}
