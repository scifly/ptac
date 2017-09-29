<?php
namespace App\Models;

use App\Facades\DatatableFacade as Datatable;
use App\Http\Requests\WapSiteRequest;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Mockery\Exception;

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
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\WapSiteModule[] $wapSiteModules
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
        'enabled',
    ];
    
    public function wapSiteModules() {
        
        return $this->hasMany('App\Models\WapSiteModule');
        
    }
    
    public function school() {
        
        return $this->belongsTo('App\Models\School');
        
    }
    
    public function store(WapSiteRequest $request) {
        try {
            $exception = DB::transaction(function () use ($request) {
                //删除原有的图片
                $this->removeMedias($request);
                $this->create($request->all());
            });
            
            return is_null($exception) ? true : $exception;
        } catch (Exception $e) {
            return false;
        }
    }
    
    /**
     * @param $request
     */
    private function removeMedias(WapSiteRequest $request) {
        //删除原有的图片
        $mediaIds = $request->input('del_ids');
        if ($mediaIds) {
            $medias = Media::whereIn('id', $mediaIds)->get(['id', 'path']);
            foreach ($medias as $media) {
                $paths = explode("/", $media->path);
                dd($paths);die;
                Storage::disk('public')->delete($paths[6]);
                
            }
            Media::whereIn('id', $mediaIds)->delete();
        }
    }
    
    public function modify(WapSiteRequest $request, $id) {
        $wapSite = $this->find($id);
        if (!$wapSite) {
            return false;
        }
        try {
            $exception = DB::transaction(function () use ($request, $id) {
                $this->removeMedias($request);
                // dd($request->except('_method', '_token'));
                return $this->where('id', $id)->update($request->except('_method', '_token', 'del_ids'));
            });
            
            return is_null($exception) ? true : $exception;
        } catch (Exception $e) {
            return false;
        }
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
                'db'        => 'WapSite.enabled', 'dt' => 5,
                'formatter' => function ($d, $row) {
                    return Datatable::dtOps($this, $d, $row);
                },
            ],
        ];
        $joins = [
            [
                'table'      => 'schools',
                'alias'      => 'School',
                'type'       => 'INNER',
                'conditions' => [
                    'School.id = WapSite.school_id',
                ],
            ],
        ];
        return Datatable::simple($this, $columns, $joins);
    }
}
