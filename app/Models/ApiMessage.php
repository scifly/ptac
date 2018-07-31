<?php
namespace App\Models;

use App\Helpers\ModelTrait;
use Carbon\Carbon;
use Eloquent;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Throwable;

/**
 * Class ApiMessage 接口消息发送记录
 *
 * @package App\Models
 * @property int $id
 * @property int $msl_id 消息发送批次id
 * @property int $message_type_id 消息类型id
 * @property string $mobile 手机号码
 * @property string $content 消息内容
 * @property int $read 是否已读
 * @property int $sent 消息是否发送成功
 * @property Carbon|null $created_at 创建于
 * @property Carbon|null $updated_at 更新于
 * @property-read MessageSendingLog $messageSendingLog
 * @property-read MessageType $messageType
 * @method static Builder|ApiMessage whereContent($value)
 * @method static Builder|ApiMessage whereCreatedAt($value)
 * @method static Builder|ApiMessage whereId($value)
 * @method static Builder|ApiMessage whereMessageTypeId($value)
 * @method static Builder|ApiMessage whereMobile($value)
 * @method static Builder|ApiMessage whereMslId($value)
 * @method static Builder|ApiMessage whereRead($value)
 * @method static Builder|ApiMessage whereSent($value)
 * @method static Builder|ApiMessage whereUpdatedAt($value)
 * @mixin Eloquent
 * @property int $s_user_id 发送者用户id
 * @method static Builder|ApiMessage whereSUserId($value)
 */
class ApiMessage extends Model {

    use ModelTrait;
    
    protected $table = 'api_messages';
    
    protected $fillable = [
        'mobile', 'content', 'read',
        'sent', 'message_type_id'
    ];
    
    /**
     * 返回所属的消息类型对象
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    function messageType() {
        
        return $this->belongsTo('App\Models\MessageType');
        
    }
    
    /**
     * 返回所属的消息发送批次对象
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    function messageSendingLog() {
        
        return $this->belongsTo('App\Models\MessageSendingLog');
        
    }
    
    /**
     * 保存接口消息发送日志
     *
     * @param array $mobiles
     * @param array $data
     * @return bool
     * @throws Throwable
     */
    function log(array $mobiles, array $data) {
    
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
