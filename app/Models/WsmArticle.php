<?php

namespace App\Models;

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
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\WsmArticle whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\WsmArticle whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\WsmArticle whereEnabled($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\WsmArticle whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\WsmArticle whereMediaIds($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\WsmArticle whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\WsmArticle whereSummary($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\WsmArticle whereThumbnailMediaId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\WsmArticle whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\WsmArticle whereWsmId($value)
 * @mixin \Eloquent
 */
class WsmArticle extends Model
{
    //
}
