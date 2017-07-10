<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\AttachmentType
 *
 * @property int $id
 * @property string $name 附件类型名称
 * @property string $remark 附件类型备注
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property int $enabled
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AttachmentType whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AttachmentType whereEnabled($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AttachmentType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AttachmentType whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AttachmentType whereRemark($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AttachmentType whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class AttachmentType extends Model
{
    //
}
