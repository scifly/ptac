<?php

namespace App\Models;

use Carbon\Carbon;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
 * @mixin Eloquent
 */
class Attachment extends Model {

    protected $fillable = ['accachment_type_id', 'url', 'enabled'];

    /**
     * 返回所属附件类型对象
     *
     * @return BelongsTo
     */
    function attachmentType() { return $this->belongsTo('App\Models\AttachmentType'); }

}
