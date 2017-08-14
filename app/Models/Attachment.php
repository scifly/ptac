<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Attachment
 *
 * @property int $id
 * @property int $attachment_type_id 附件类型id
 * @property string $url 附件url
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property int $enabled
 * @property-read \App\Models\AttachmentType $attachmentType
 * @method static Builder|Attachment whereAttachmentTypeId($value)
 * @method static Builder|Attachment whereCreatedAt($value)
 * @method static Builder|Attachment whereEnabled($value)
 * @method static Builder|Attachment whereId($value)
 * @method static Builder|Attachment whereUpdatedAt($value)
 * @method static Builder|Attachment whereUrl($value)
 * @mixin \Eloquent
 */
class Attachment extends Model {
    
    protected $fillable = [
        'accachment_type_id',
        'url',
        'enabled'
    ];
    
    public function attachmentType() {
        return $this->belongsTo('App\Models\AttachmentType');
    }
    
}
