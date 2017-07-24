<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
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
 * @method static Builder|WapSiteModule whereCreatedAt($value)
 * @method static Builder|WapSiteModule whereEnabled($value)
 * @method static Builder|WapSiteModule whereId($value)
 * @method static Builder|WapSiteModule whereMediaId($value)
 * @method static Builder|WapSiteModule whereName($value)
 * @method static Builder|WapSiteModule whereUpdatedAt($value)
 * @method static Builder|WapSiteModule whereWapSiteId($value)
 * @mixin \Eloquent
 * 网站类型
 */
class WapSiteModule extends Model {
    //
    protected $table = 'wap_site_modules';
    protected $fillable = [
        'id',
        'wap_site_id',
        'name',
        'summary',
        'media_id',
        'created_at',
        'updated_at',
    ];
    public function hasManyArticle()
    {
        return $this->hasMany('App\Models\WsmArticle','wsm_id','id');
    }
    public function belongsToWs()
    {
        return $this->belongsTo('App\Models\WapSite', 'wap_site_id', 'id');

    }

}
