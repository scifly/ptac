<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\WapSite
 *
 * @property int $id
 * @property int $school_id 所属学校ID
 * @property string $site_title 首页抬头
 * @property string $media_ids 首页幻灯片图片多媒体ID
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property int $enabled
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\WapSite whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\WapSite whereEnabled($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\WapSite whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\WapSite whereMediaIds($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\WapSite whereSchoolId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\WapSite whereSiteTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\WapSite whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class WapSite extends Model
{
    //
}
