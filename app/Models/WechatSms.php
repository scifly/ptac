<?php
namespace App\Models;

use Carbon\Carbon;
use Eloquent;
use Illuminate\Contracts\View\Factory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\View\View;

/**
 * App\Models\WechatSms 微信消息详情url
 *
 * @property int $id
 * @property int $urlcode 消息详情代码
 * @property int $message_id 消息id
 * @property Carbon|null $created_at 创建于
 * @property Carbon|null $updated_at 更新于
 * @property int $enabled
 * @property-read Message $message
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
     * 返回对应的消息对象
     *
     * @param $urlcode
     * @return Factory|View
     */
    function show($urlcode) {
    
        $message = WechatSms::whereUrlcode($urlcode)->first()->message;
    
        return view('wechat.message_center.show_sms', [
            'message' => $message,
            'content' => $message->detail($message->id)
        ]);
        
    }
    
}