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
 * App\Models\MessageType 消息类型
 *
 * @property int $id
 * @property string $name 消息类型名称
 * @property string $user_id 合作伙伴用户id
 * @property string $remark 消息类型备注
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int $enabled
 * @property-read Collection|Message[] $messages
 * @property-read Collection|ApiMessage[] $apiMessages
 * @method static Builder|MessageType whereCreatedAt($value)
 * @method static Builder|MessageType whereEnabled($value)
 * @method static Builder|MessageType whereId($value)
 * @method static Builder|MessageType whereName($value)
 * @method static Builder|MessageType whereUserId($value)
 * @method static Builder|MessageType whereRemark($value)
 * @method static Builder|MessageType whereUpdatedAt($value)
 * @method static Builder|MessageType newModelQuery()
 * @method static Builder|MessageType newQuery()
 * @method static Builder|MessageType query()
 * @mixin Eloquent
 */
class MessageType extends Model {
    
    use ModelTrait;
    
    protected $table = 'message_types';
    
    protected $fillable = ['name', 'user_id', 'remark', 'enabled'];
    
    /**
     * 获取指定消息类型包含的所有消息对象
     *
     * @return HasMany
     */
    function messages() { return $this->hasMany('App\Models\Message'); }
    
    /**
     * 返回指定消息类型包含的所有接口消息对象
     *
     * @return HasMany
     */
    function apiMessages() { return $this->hasMany('App\Models\ApiMessage'); }
    
    /**
     * 消息类型列表
     *
     * @return array
     */
    function index() {
        
        $columns = [
            ['db' => 'MessageType.id', 'dt' => 0],
            ['db' => 'MessageType.name', 'dt' => 1],
            ['db' => 'MessageType.remark', 'dt' => 2],
            ['db' => 'MessageType.created_at', 'dt' => 3],
            ['db' => 'MessageType.updated_at', 'dt' => 4],
            [
                'db'        => 'MessageType.enabled', 'dt' => 5,
                'formatter' => function ($d, $row) {
                    return Datatable::status($d, $row, false);
                },
            ],
        ];
        
        return Datatable::simple(
            $this->getModel(), $columns
        );
        
    }
    
    /**
     * 保存消息类型
     *
     * @param array $data
     * @return bool
     */
    function store(array $data) {
        
        return $this->create($data) ? true : false;
        
    }
    
    /**
     * 更新消息类型
     *
     * @param array $data
     * @param $id
     * @return bool
     */
    function modify(array $data, $id) {
        
        return $this->find($id)->update($data);
        
    }
    
    /**
     * 删除消息类型
     *
     * @param $id
     * @return bool|null
     * @throws Throwable
     */
    function remove($id = null) {
    
        try {
            DB::transaction(function () use ($id) {
                $this->purge(['Message'], 'message_type_id', 'reset', $id);
                $this->purge(['MessageType'], 'id', 'purge', $id);
            });
        } catch (Exception $e) {
            throw $e;
        }
    
        return true;
        
    }
    
}
