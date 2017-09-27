<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\AttachmentType 附件类型
 *
 * @property int $id
 * @property string $name 附件类型名称
 * @property string $remark 附件类型备注
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property int $enabled
 * @method static Builder|AttachmentType whereCreatedAt($value)
 * @method static Builder|AttachmentType whereEnabled($value)
 * @method static Builder|AttachmentType whereId($value)
 * @method static Builder|AttachmentType whereName($value)
 * @method static Builder|AttachmentType whereRemark($value)
 * @method static Builder|AttachmentType whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property-read Attachment[] $attachments
 */
class AttachmentType extends Model {
    
    protected $table = 'attachment_types';
    
    protected $fillable = ['name', 'remark', 'enabled'];
    
    /**
     * 获取包含的所有附件对象
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function attachments() { return $this->hasMany('App\Models\Attachment'); }
    
}
