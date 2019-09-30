<?php
namespace App\Models;

use App\Facades\Datatable;
use App\Helpers\ModelTrait;
use Carbon\Carbon;
use Eloquent;
use Illuminate\Database\Eloquent\{Builder, Collection, Model, Relations\HasMany};
use Throwable;

/**
 * App\Models\MessageType 消息类型
 *
 * @property int $id
 * @property string $name 消息类型名称
 * @property string $color
 * @property string $icon
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
 * @method static Builder|MessageType whereColor($value)
 * @method static Builder|MessageType whereIcon($value)
 * @method static Builder|MessageType whereUserId($value)
 * @method static Builder|MessageType whereRemark($value)
 * @method static Builder|MessageType whereUpdatedAt($value)
 * @method static Builder|MessageType newModelQuery()
 * @method static Builder|MessageType newQuery()
 * @method static Builder|MessageType query()
 * @mixin Eloquent
 * @property-read int|null $api_messages_count
 * @property-read int|null $messages_count
 */
class MessageType extends Model {
    
    use ModelTrait;
    
    protected $fillable = ['name', 'user_id', 'remark', 'enabled'];
    
    /** @return HasMany */
    function messages() { return $this->hasMany('App\Models\Message'); }
    
    /** @return HasMany */
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
        
        return Datatable::simple($this, $columns);
        
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
     * @throws Throwable
     */
    function modify(array $data, $id) {
        
        return $this->revise(
            $this, $data, $id, null
        );
        
    }
    
    /**
     * 删除消息类型
     *
     * @param $id
     * @return bool|null
     * @throws Throwable
     */
    function remove($id = null) {
        
        return $this->purge($id, [
            'reset.message_type_id' => ['Message', 'ApiMessage']
        ]);
        
    }
    
}
