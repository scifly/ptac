<?php
namespace App\Models;

use Carbon\Carbon;
use Eloquent;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Request;

/**
 * App\Models\WechatSms 微信消息详情url
 *
 * @property int $id
 * @property int $urlcode 消息详情代码
 * @property int $message_id 消息id
 * @property Carbon|null $created_at 创建于
 * @property Carbon|null $updated_at 更新于
 * @property int $enabled
 * @property-read \App\Models\Message $message
 * @method static Builder|WechatSms whereCreatedAt($value)
 * @method static Builder|WechatSms whereEnabled($value)
 * @method static Builder|WechatSms whereId($value)
 * @method static Builder|WechatSms whereMessageId($value)
 * @method static Builder|WechatSms whereUpdatedAt($value)
 * @method static Builder|WechatSms whereUrlcode($value)
 * @mixin Eloquent
 */
class WechatSms extends Model {
    
    protected $table = 'wechat_smses';
    
    protected $fillable = [
        'id', 'urlcode', 'message_id', 'enabled'
    ];
    
    /**
     * 返回所属的消息对象
     *
     * @return BelongsTo
     */
    function message() { return $this->belongsTo('App\Models\Message'); }
    
    /**
     * 保存微信消息详情url
     *
     * @param array $data
     * @return bool
     */
    function store(array $data) {
        
        return $this->create($data) ? true : false;
        
    }
    
    /**
     * （批量）删除微信消息详情url
     *
     * @param null $id
     * @return bool|null
     * @throws Exception
     */
    function remove($id = null) {
    
        return $id
            ? $this->find($id)->delete()
            : $this->whereIn('id', array_values(Request::input('ids')))->delete();
            
    }
    
}