<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\WsmArticle
 *
 * @property int $id
 * @property int $wsm_id 所属网站模块ID
 * @property string $name 文章名称
 * @property string $summary 文章摘要
 * @property int $thumbnail_media_id 缩略图多媒体ID
 * @property string $content 文章内容
 * @property string $media_ids 附件多媒体ID
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property int $enabled
 * @method static Builder|WsmArticle whereContent($value)
 * @method static Builder|WsmArticle whereCreatedAt($value)
 * @method static Builder|WsmArticle whereEnabled($value)
 * @method static Builder|WsmArticle whereId($value)
 * @method static Builder|WsmArticle whereMediaIds($value)
 * @method static Builder|WsmArticle whereName($value)
 * @method static Builder|WsmArticle whereSummary($value)
 * @method static Builder|WsmArticle whereThumbnailMediaId($value)
 * @method static Builder|WsmArticle whereUpdatedAt($value)
 * @method static Builder|WsmArticle whereWsmId($value)
 * @mixin \Eloquent
 */
class WsmArticle extends Model {
    //
    protected $table='wsm_artilces';
    protected $fillable=[
        'id','wsm_id'
    ];
}
