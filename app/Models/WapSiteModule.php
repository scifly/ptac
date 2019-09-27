<?php
namespace App\Models;

use App\Facades\Datatable;
use App\Helpers\ModelTrait;
use Carbon\Carbon;
use Eloquent;
use Illuminate\Database\Eloquent\{Builder, Collection, Model, Relations\BelongsTo, Relations\HasMany};
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Request;
use Throwable;

/**
 * App\Models\WapSiteModule 微网站栏目
 *
 * @property int $id
 * @property int $wap_site_id 所属微网站ID
 * @property string $name 模块名称
 * @property int $media_id 模块图片多媒体ID
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int $enabled
 * @property-read Media $media
 * @property-read WapSite $wapsite
 * @property-read Collection|WsmArticle[] $wsmArticles
 * @method static Builder|WapSiteModule whereCreatedAt($value)
 * @method static Builder|WapSiteModule whereEnabled($value)
 * @method static Builder|WapSiteModule whereId($value)
 * @method static Builder|WapSiteModule whereMediaId($value)
 * @method static Builder|WapSiteModule whereName($value)
 * @method static Builder|WapSiteModule whereUpdatedAt($value)
 * @method static Builder|WapSiteModule whereWapSiteId($value)
 * @method static Builder|WapSiteModule newModelQuery()
 * @method static Builder|WapSiteModule newQuery()
 * @method static Builder|WapSiteModule query()
 * @mixin Eloquent
 * @property-read int|null $wsm_articles_count
 * @property-read Collection|WsmArticle[] $articles
 * @property-read int|null $articles_count
 */
class WapSiteModule extends Model {
    
    use ModelTrait;
    
    protected $table = 'wap_site_modules';
    
    protected $fillable = ['id', 'wap_site_id', 'name', 'media_id', 'enabled'];
    
    /** @return BelongsTo */
    function wapsite() { return $this->belongsTo('App\Models\WapSite', 'wap_site_id'); }
    
    /** @return HasMany */
    function articles() { return $this->hasMany('App\Models\WsmArticle', 'wsm_id', 'id'); }
    
    /** @return BelongsTo */
    function media() { return $this->belongsTo('App\Models\Media'); }
    
    /**
     * 返回微网站栏目列表（后台）
     *
     * @return array
     */
    function index() {
        
        $columns = [
            ['db' => 'WapSiteModule.id', 'dt' => 0],
            ['db' => 'WapSiteModule.name', 'dt' => 1],
            ['db' => 'WapSite.site_title', 'dt' => 2],
            ['db' => 'WapSiteModule.created_at', 'dt' => 3],
            ['db' => 'WapSiteModule.updated_at', 'dt' => 4],
            [
                'db'        => 'WapSiteModule.enabled', 'dt' => 5,
                'formatter' => function ($d, $row) {
                    return Datatable::status($d, $row, false);
                },
            ],
        ];
        $joins = [
            [
                'table'      => 'wap_sites',
                'alias'      => 'WapSite',
                'type'       => 'INNER',
                'conditions' => [
                    'WapSite.id = WapSiteModule.wap_site_id',
                ],
            ],
        ];
        $condition = 'WapSite.school_id = ' . $this->schoolId();
        
        return Datatable::simple(
            $this, $columns, $joins, $condition
        );
        
    }
    
    /**
     * 保存网站栏目
     *
     * @param array $data
     * @return bool|mixed
     */
    function store(array $data) {
        
        return $this->create($data) ? true : false;
        
    }
    
    /**
     * @param array $data
     * @param $id
     * @return bool
     * @throws Throwable
     */
    function modify(array $data, $id) {
        
        return $this->revise(
            $this, $data, $id, null
        );
        
    }
    
    /**
     * 移除网站栏目
     *
     * @param $id
     * @return bool|null
     * @throws Throwable
     */
    function remove($id = null) {
        
        return $this->purge(
            [class_basename($this), 'WsmArticle'],
            'wsm_id', 'purge', $id
        );
        
    }
    
    /** @return array */
    function compose() {
    
        if (explode('/', Request::path())[1] == 'index') {
            $data = [
                'titles' => ['#', '栏目名称', '所属网站', '创建于', '更新于', '状态 . 操作'],
            ];
        } else {
            $module = WapSiteModule::find(Request::route('id'));
            $data = [
                'wapSites' => WapSite::whereSchoolId($this->schoolId())->pluck('site_title', 'id'),
                'media'    => $module ? $module->media : null
            ];
        }
        
        return $data;
        
    }
    
    /** 微信端 ------------------------------------------------------------------------------------------------------- */
    /**
     * 上传微网站栏目图片
     *
     * @return JsonResponse
     * @throws Throwable
     */
    function import() {
        
        $upload = (new Media)->upload(
            Request::file('file'),
            __('messages.wap_site_module.title')
        );
        
        return response()->json($upload);
        
    }
    
}
