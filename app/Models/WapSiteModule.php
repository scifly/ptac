<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use App\Facades\DatatableFacade as Datatable;

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
 * @property-read \App\Models\WapSite $belongsToWs
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\WsmArticle[] $hasManyArticle
 * @property-read \App\Models\WapSite $wapsite
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\WsmArticle[] $wsmarticles
 */
class WapSiteModule extends Model {
    //
    protected $table = 'wap_site_modules';
    protected $fillable = [
        'id',
        'wap_site_id',
        'name',
        'media_id',
        'created_at',
        'updated_at',
        'enabled',
    ];
    public function wsmarticles()
    {
        return $this->hasMany('App\Models\WsmArticle','wsm_id','id');
    }
    public function wapsite()
    {
        return $this->belongsTo('App\Models\WapSite');

    }
    public function media(){
        return $this->belongsTo('App\Models\Media');

    }

    /**
     * @return array
     */
    public function datatable() {

        $columns = [
            ['db' => 'WapSiteModule.id', 'dt' => 0],
            ['db' => 'WapSiteModule.name', 'dt' => 1],
            ['db' => 'WapSite.site_title', 'dt' => 2],
            ['db' => 'WapSiteModule.created_at', 'dt' => 3],
            ['db' => 'WapSiteModule.updated_at', 'dt' => 4],

            [
                'db' => 'WapSiteModule.enabled', 'dt' => 5,
                'formatter' => function ($d, $row) {
                    return Datatable::dtOps($this, $d, $row);
                }
            ]
        ];
        $joins = [
            [
                'table' => 'wap_sites',
                'alias' => 'WapSite',
                'type'  => 'INNER',
                'conditions' => [
                    'WapSite.id = WapSiteModule.wap_site_id'
                ]
            ]
        ];

        return Datatable::simple($this,  $columns, $joins);
    }

}
