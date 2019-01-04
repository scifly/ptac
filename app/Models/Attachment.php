<?php
namespace App\Models;

use App\Helpers\ModelTrait;
use Carbon\Carbon;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Throwable;

/**
 * App\Models\Attachment 附件
 *
 * @property int $id
 * @property int $attachment_type_id 附件类型id
 * @property string $url 附件url
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int $enabled
 * @property-read AttachmentType $attachmentType
 * @method static Builder|Attachment whereAttachmentTypeId($value)
 * @method static Builder|Attachment whereCreatedAt($value)
 * @method static Builder|Attachment whereEnabled($value)
 * @method static Builder|Attachment whereId($value)
 * @method static Builder|Attachment whereUpdatedAt($value)
 * @method static Builder|Attachment whereUrl($value)
 * @method static Builder|Attachment newModelQuery()
 * @method static Builder|Attachment newQuery()
 * @method static Builder|Attachment query()
 * @mixin Eloquent
 */
class Attachment extends Model {
    
    use ModelTrait;
    
    protected $fillable = ['accachment_type_id', 'url', 'enabled'];
    
    /**
     * 返回所属附件类型对象
     *
     * @return BelongsTo
     */
    function attachmentType() { return $this->belongsTo('App\Models\AttachmentType'); }
    
    /**
     * 保存附件
     *
     * @param array $data
     * @return bool
     */
    function store(array $data) {
        
        return $this->create($data) ? true : false;
        
    }
    
    /**
     * 更新附件
     *
     * @param array $data
     * @param null $id
     * @return bool
     */
    function modify(array $data, $id = null) {
        
        return $id
            ? $this->find($id)->update($data)
            : $this->batch($this);
        
    }
    
    /**
     * （批量）删除附件
     *
     * @param null $id
     * @return bool
     * @throws Throwable
     */
    function remove($id = null) {
        
        return $this->del($this, $id);
        
    }
    
}
