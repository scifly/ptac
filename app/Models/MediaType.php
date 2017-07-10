<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\MediaType
 *
 * @property int $id
 * @property string $name 媒体类型名称
 * @property string $remark 媒体类型备注
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property int $enabled
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\MediaType whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\MediaType whereEnabled($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\MediaType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\MediaType whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\MediaType whereRemark($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\MediaType whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class MediaType extends Model
{
    //
}
