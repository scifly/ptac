<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Media
 *
 * @mixin \Eloquent
 * @property int $id
 * @property string $path 媒体文件路径
 * @property string $remark 媒体文件备注
 * @property int $media_type_id 媒体类型ID
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property int $enabled
 * @property-read \App\Models\MediaType $mediaType
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Media whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Media whereEnabled($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Media whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Media whereMediaTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Media wherePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Media whereRemark($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Media whereUpdatedAt($value)
 */
class Media extends Model {
    //
    protected $table='medias';

    protected $fillable=['path','remark','media_type_id','created_at','updated_at','enabled'];

    public function mediaType()
    {
        return $this->belongsTo('App\Models\MediaType');
    }
}
