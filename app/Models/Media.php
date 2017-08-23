<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
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
 * @method static Builder|Media whereCreatedAt($value)
 * @method static Builder|Media whereEnabled($value)
 * @method static Builder|Media whereId($value)
 * @method static Builder|Media whereMediaTypeId($value)
 * @method static Builder|Media wherePath($value)
 * @method static Builder|Media whereRemark($value)
 * @method static Builder|Media whereUpdatedAt($value)
 * @property-read \App\Models\WapSiteModule $wapsitemoudle
 */
class Media extends Model {
    //
    protected $table = 'medias';
    
    protected $fillable = ['path', 'remark', 'media_type_id', 'created_at', 'updated_at', 'enabled'];
    
    public function mediaType() {
        return $this->belongsTo('App\Models\MediaType');
    }
    
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function wapsitemoudle() {
        
        return $this->hasOne('App\Models\WapSiteModule');
        
    }
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function wasmarticle() {

        return $this->hasOne('App\Models\WsmArticle', 'thumbnail_media_id', 'id');

    }
}
