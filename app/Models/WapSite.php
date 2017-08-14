<?php

namespace App\Models;

use App\Facades\DatatableFacade as Datatable;
use Illuminate\Database\Eloquent\Builder;
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
 * @method static Builder|WapSite whereCreatedAt($value)
 * @method static Builder|WapSite whereEnabled($value)
 * @method static Builder|WapSite whereId($value)
 * @method static Builder|WapSite whereMediaIds($value)
 * @method static Builder|WapSite whereSchoolId($value)
 * @method static Builder|WapSite whereSiteTitle($value)
 * @method static Builder|WapSite whereUpdatedAt($value)
 * @mixin \Eloquent
 * 网站
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\WapSiteModule[] $hasManyWsm
 * @property-read \App\Models\School $school
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\WapSiteModule[] $wapsiteModules
 */
class WapSite extends Model {
    //
    protected $fillable = [
        'id',
        'school_id',
        'site_title',
        'media_ids',
        'created_at',
        'updated_at',
    ];
    
    public function wapsiteModules() {
        return $this->hasMany('App\Models\WapSiteModule', 'wap_site_id', 'id');
    }
    
    public function school() {
        return $this->belongsTo('App\Models\School');
    }
    
    /**
     * @return array
     */
    public function datatable() {
        
        $columns = [
            ['db' => 'WapSite.id', 'dt' => 0],
            ['db' => 'School.name', 'dt' => 1],
            ['db' => 'WapSite.site_title', 'dt' => 2],
            ['db' => 'WapSite.created_at', 'dt' => 3],
            ['db' => 'WapSite.updated_at', 'dt' => 4],
            
            [
                'db' => 'WapSite.enabled', 'dt' => 5,
                'formatter' => function ($d, $row) {
                    return Datatable::dtOps($this, $d, $row);
                }
            ]
        ];
        $joins = [
            [
                'table' => 'schools',
                'alias' => 'School',
                'type' => 'INNER',
                'conditions' => [
                    'School.id = WapSite.school_id'
                ]
            ]
        ];
        
        return Datatable::simple($this, $columns, $joins);
    }
}
