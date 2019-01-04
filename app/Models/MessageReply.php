<?php
namespace App\Models;

use App\Helpers\ModelTrait;
use Carbon\Carbon;
use Eloquent;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Request;

/**
 * App\Models\MessageReply
 *
 * @property-read User $user
 * @mixin Eloquent
 * @property int $id
 * @property int $msl_id 所属消息批次
 * @property int $user_id 消息回复者id
 * @property string $content 回复内容
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder|MessageReply whereContent($value)
 * @method static Builder|MessageReply whereCreatedAt($value)
 * @method static Builder|MessageReply whereId($value)
 * @method static Builder|MessageReply whereMslId($value)
 * @method static Builder|MessageReply whereUpdatedAt($value)
 * @method static Builder|MessageReply whereUserId($value)
 * @method static Builder|MessageReply newModelQuery()
 * @method static Builder|MessageReply newQuery()
 * @method static Builder|MessageReply query()
 */
class MessageReply extends Model {
    
    // todo: needs to be optimized
    use ModelTrait;
    
    protected $fillable = ['msl_id', 'user_id', 'content'];
    
    /**
     * 返回所属的用户对象
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    function user() { return $this->belongsTo('App\Models\User', 'user_id', 'id'); }
    
    /**
     * @param $data
     * @return bool
     */
    function store($data) {
        
        return $this->create($data) ? true : false;
        
    }
    
    /**
     * （批量）删除消息回复
     *
     * @param null $id
     * @return bool
     * @throws Exception
     */
    function remove($id = null) {
        
        return $id
            ? $this->find($id)->delete()
            : $this->whereIn('id', array_values(Request::input('ids')))->delete();
        
    }
    
}
