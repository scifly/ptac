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
use Throwable;

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
     * 通信方式列表
     *
     * @return array
     */
    function index() {
        
        $columns = [
            ['db' => 'CommType.id', 'dt' => 0],
            ['db' => 'CommType.name', 'dt' => 1],
            ['db' => 'CommType.remark', 'dt' => 2],
            ['db' => 'CommType.created_at', 'dt' => 3],
            ['db' => 'CommType.updated_at', 'dt' => 4],
            [
                'db'        => 'CommType.enabled', 'dt' => 5,
                'formatter' => function ($d, $row) {
                    return Datatable::status($d, $row, false);
                },
            ],
        ];
        
        return Datatable::simple($this->getModel(), $columns);
        
    }
    
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
        
        return $this->find($id)->update($data);
        
    }
    
    /**
     * 移除通信类型
     *
     * @param $id
     * @return bool|null
     * @throws Throwable
     */
    function remove($id = null) {
        
        return $this->del($this, $id);
        
    }
    
    /**
     * 删除指定通信类型的所有数据
     *
     * @param $id
     * @throws Throwable
     */
    function purge($id) {
        
        try {
            DB::transaction(function () use ($id) {
                Message::whereCommTypeId($id)->update([
                    'comm_type_id' => 0,
                ]);
                $this->find($id)->delete();
            });
        } catch (Exception $e) {
            throw $e;
        }
        
    }
    
}
