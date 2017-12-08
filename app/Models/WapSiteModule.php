<?php

namespace App\Models;

use App\Facades\DatatableFacade as Datatable;
use App\Http\Requests\WapSiteModuleRequest;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

/**
 * 网站类型
 * App\Models\WapSiteModule
 *
 * @property int $id
 * @property int $wap_site_id 所属微网站ID
 * @property string $name 模块名称
 * @property int $media_id 模块图片多媒体ID
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int $enabled
 * @method static Builder|WapSiteModule whereCreatedAt($value)
 * @method static Builder|WapSiteModule whereEnabled($value)
 * @method static Builder|WapSiteModule whereId($value)
 * @method static Builder|WapSiteModule whereMediaId($value)
 * @method static Builder|WapSiteModule whereName($value)
 * @method static Builder|WapSiteModule whereUpdatedAt($value)
 * @method static Builder|WapSiteModule whereWapSiteId($value)
 * @mixin \Eloquent
 * @property-read WapSite $belongsToWs
 * @property-read Collection|WsmArticle[] $hasManyArticle
 * @property-read WapSite $wapsite
 * @property-read Collection|WsmArticle[] $wsmarticles
 * @property-read Media $media
 */
class WapSiteModule extends Model {

    protected $table = 'wap_site_modules';
    protected $fillable = [
        'id', 'wap_site_id', 'name',
        'media_id', 'created_at', 'updated_at',
        'enabled',
    ];
    
    public function wsmArticles() {
        
        return $this->hasMany('App\Models\WsmArticle', 'wsm_id', 'id');
        
    }

    public function media() { return $this->belongsTo('App\Models\Media'); }

    public function wapsite() { return $this->belongsTo('App\Models\WapSite'); }

    /**
     * @param WapSiteModuleRequest $request
     * @return bool|mixed
     * @throws Exception
     */
    public function store(WapSiteModuleRequest $request) {
        
        try {
            DB::transaction(function () use ($request) {
                //删除原有的图片
                $this->removeMedias($request);
                $this->create($request->all());
            });
        } catch (Exception $e) {
            throw $e;
        }
        
        return true;

    }
    
    /**
     * @param $request
     * @throws Exception
     */
    private function removeMedias(WapSiteModuleRequest $request) {
        
        //删除原有的图片
        $mediaIds = $request->input('del_id');
        if ($mediaIds) {
            $medias = Media::whereIn('id', $mediaIds)->get(['id', 'path']);
            foreach ($medias as $media) {
                $paths = explode("/", $media->path);
                Storage::disk('public')->delete($paths[5]);
            }
            try {
                Media::whereIn('id', $mediaIds)->delete();
            } catch (Exception $e) {
                throw $e;
            }
        }
        
    }

    /**
     * @param WapSiteModuleRequest $request
     * @param $id
     * @return bool
     * @throws Exception
     */
    public function modify(WapSiteModuleRequest $request, $id) {
        
        $wapSite = $this->find($id);
        if (!$wapSite) { return false; }
        try {
            DB::transaction(function () use ($request, $id) {
                $this->removeMedias($request);
                return $this->where('id', $id)->update($request->except('_method', '_token', 'del_id'));
            });
        } catch (Exception $e) {
            throw $e;
        }
        
        return true;
        
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
                    return Datatable::dtOps($d, $row);
                },
            ],
        ];
        $joins = [
            [
                'table' => 'wap_sites',
                'alias' => 'WapSite',
                'type' => 'INNER',
                'conditions' => [
                    'WapSite.id = WapSiteModule.wap_site_id',
                ],
            ],
        ];
        
        return Datatable::simple($this, $columns, $joins);
        
    }

}
