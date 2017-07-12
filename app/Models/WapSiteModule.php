<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\WapSiteModule
 *
 * @property int $id
 * @property int $wap_site_id 所属微网站ID
 * @property string $name 模块名称
 * @property int $media_id 模块图片多媒体ID
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property int $enabled
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\WapSiteModule whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\WapSiteModule whereEnabled($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\WapSiteModule whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\WapSiteModule whereMediaId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\WapSiteModule whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\WapSiteModule whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\WapSiteModule whereWapSiteId($value)
 * @mixin \Eloquent
 */
class WapSiteModule extends Model
{
    //
}
