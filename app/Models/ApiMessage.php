<?php
namespace App\Models;

use App\Facades\Datatable;
use App\Helpers\ModelTrait;
use Eloquent;
use Exception;
use Illuminate\Database\Eloquent\{Builder, Model, Relations\BelongsTo};
use Illuminate\Support\{Carbon, Collection, Facades\DB};
use Throwable;

/**
 * Class ApiMessage 接口消息发送记录
 *
 * @property int $id
 * @property int $msl_id 消息发送批次id
 * @property int $message_type_id 消息类型id
 * @property int $s_user_id 发送者用户id
 * @property string $mobile 手机号码
 * @property string $content 消息内容
 * @property int $read 是否已读
 * @property int $sent 消息是否发送成功
 * @property Carbon|null $created_at 创建于
 * @property Carbon|null $updated_at 更新于
 * @property-read MessageSendingLog $messageSendingLog
 * @property-read MessageType $messageType
 * @method static Builder|ApiMessage newModelQuery()
 * @method static Builder|ApiMessage newQuery()
 * @method static Builder|ApiMessage query()
 * @method static Builder|ApiMessage whereContent($value)
 * @method static Builder|ApiMessage whereCreatedAt($value)
 * @method static Builder|ApiMessage whereId($value)
 * @method static Builder|ApiMessage whereMessageTypeId($value)
 * @method static Builder|ApiMessage whereMobile($value)
 * @method static Builder|ApiMessage whereMslId($value)
 * @method static Builder|ApiMessage whereRead($value)
 * @method static Builder|ApiMessage whereSUserId($value)
 * @method static Builder|ApiMessage whereSent($value)
 * @method static Builder|ApiMessage whereUpdatedAt($value)
 * @mixin Eloquent
 */
class ApiMessage extends Model {
    
    use ModelTrait;
    
    protected $table = 'api_messages';
    
    protected $fillable = [
        'msl_id', 'message_type_id', 's_user_id',
        'mobile', 'content', 'read', 'sent',
    ];
    
    /** @return BelongsTo */
    function mType() { return $this->belongsTo('App\Models\MessageType', 'message_type_id'); }
    
    /** @return BelongsTo */
    function msgLog() { return $this->belongsTo('App\Models\MessageSendingLog', 'msl_id'); }
    
    /** @return BelongsTo */
    function sender() { return $this->belongsTo('App\Models\User', 's_user_id'); }
    
    /**
     * api发送的短消息列表
     *
     * @param $userId
     * @return array
     */
    function index($userId) {
    
        $columns = [
            ['db' => 'ApiMessage.id', 'dt' => 0],
            ['db' => 'User.realname', 'dt' => 1],
            ['db' => 'ApiMessage.created_at', 'dt' => 2, 'dr' => true],
            ['db' => 'ApiMessage.content', 'dt' => 3],
        ];
        $joins = [
            [
                'table'      => 'users',
                'alias'      => 'User',
                'type'       => 'INNER',
                'conditions' => [
                    'User.id = ApiMessage.s_user_id',
                ],
            ],
        ];
        $condition = 'ApiMessage.s_user_id = ' . $userId;
        
        return Datatable::simple(
            $this, $columns, $joins, $condition
        );
        
    }
    
    /**
     * 保存接口消息发送日志
     *
     * @param Collection $mobiles
     * @param array $data
     * @return bool
     * @throws Throwable
     */
    function log($mobiles, array $data) {
        
        try {
            DB::transaction(function () use ($mobiles, $data) {
                $records = [];
                foreach ($mobiles as $mobile) {
                    $data['mobile'] = $mobile;
                    $records[] = $data;
                }
                $this->insert($records);
            });
        } catch (Exception $e) {
            throw $e;
        }
        
        return true;
        
    }
    
}
