<?php
namespace App\Models;

use App\Facades\Datatable;
use App\Helpers\ModelTrait;
use Carbon\Carbon;
use Eloquent;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Database\Eloquent\{Builder, Model, Relations\BelongsTo};
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\{Request};
use Throwable;

/**
 * App\Models\WsmArticle 微网站栏目文章
 *
 * @property int $id
 * @property int $wsm_id 所属网站模块ID
 * @property string $name 文章名称
 * @property string $summary 文章摘要
 * @property int $thumbnail_media_id 缩略图多媒体ID
 * @property string $content 文章内容
 * @property string $media_ids 附件多媒体ID
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int $enabled
 * @property-read WapSiteModule $wapSiteModule
 * @property-read WapSiteModule $wapsitemodule
 * @property-read Media $thumbnailmedia
 * @property-read Media $thumbnailMedia
 * @property-read Media $media
 * @property-read WapSiteModule $module
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
 * @method static Builder|WsmArticle newModelQuery()
 * @method static Builder|WsmArticle newQuery()
 * @method static Builder|WsmArticle query()
 * @mixin Eloquent
 */
class WsmArticle extends Model {
    
    use ModelTrait;
    
    protected $table = 'wsm_articles';
    
    protected $fillable = [
        'id', 'wsm_id', 'name', 'summary', 'thumbnail_media_id',
        'content', 'media_ids', 'created_at', 'updated_at', 'enabled',
    ];
    
    protected $media;
    
    /**
     * WsmArticle constructor.
     * @param array $attributes
     * @throws BindingResolutionException
     */
    function __construct(array $attributes = []) {
        
        parent::__construct($attributes);
        $this->media = app()->make('App\Models\Media');
        
    }
    
    /** @return BelongsTo */
    function module() {
        
        return $this->belongsTo('App\Models\WapSiteModule', 'wsm_id', 'id');
    
    }
    
    /** @return BelongsTo */
    function media() {
        
        return $this->belongsTo('App\Models\Media', 'thumbnail_media_id', 'id');
    
    }
    
    /**
     * 微网站文章列表
     *
     * @return array
     */
    function index() {
        
        $columns = [
            ['db' => 'WsmArticle.id', 'dt' => 0],
            ['db' => 'Wsm.name as wsmname', 'dt' => 1],
            ['db' => 'WsmArticle.name', 'dt' => 2],
            ['db' => 'WsmArticle.summary', 'dt' => 3],
            ['db' => 'WsmArticle.created_at', 'dt' => 4],
            ['db' => 'WsmArticle.updated_at', 'dt' => 5],
            [
                'db'        => 'WsmArticle.enabled', 'dt' => 6,
                'formatter' => function ($d, $row) {
                    return Datatable::status($d, $row, false);
                },
            ],
        ];
        $joins = [
            [
                'table'      => 'wap_site_modules',
                'alias'      => 'Wsm',
                'type'       => 'INNER',
                'conditions' => [
                    'Wsm.id = WsmArticle.wsm_id',
                ],
            ],
            [
                'table'      => 'wap_sites',
                'alias'      => 'WapSite',
                'type'       => 'INNER',
                'conditions' => [
                    'WapSite.id = Wsm.wap_site_id',
                ],
            ],
        ];
        $condition = 'WapSite.school_id = ' . $this->schoolId();
        
        return Datatable::simple(
            $this, $columns, $joins, $condition
        );
        
    }
    
    /**
     * 保存新建的网站文章
     *
     * @param array $data
     * @return bool|mixed
     */
    function store(array $data) {
        
        return $this->create($data) ? true : false;
        
    }
    
    /**
     * 更新网站文章
     *
     * @param array $data
     * @param $id
     * @return bool|mixed
     * @throws Throwable
     */
    function modify(array $data, $id) {
        
        return $this->revise(
            $this, $data, $id, null
        );
        
    }
    
    /**
     * 移除网站文章
     *
     * @param $id
     * @return bool|null
     * @throws Throwable
     */
    function remove($id = null) {
        
        return $this->purge(['WsmArticle'], 'id', 'purge', $id);
        
    }
    
    /** @return array */
    function compose() {
    
        $action = explode('/', Request::path())[1];
        if ($action == 'index') {
            $data = [
                'titles' => ['#', '所属栏目', '文章名称', '文章摘要', '创建于', '更新于', '状态 . 操作'],
            ];
        } else {
            $article = WsmArticle::find(Request::route('id'));
            $mediaIds = explode(',', $article ? $article->media_ids : null);
            $data = [
                'wsms'   => School::find($this->schoolId())->wapSite->modules->pluck('name', 'id'),
                'medias' => Media::whereIn('id', $mediaIds)->get(),
            ];
        }
        
        return $data;
        
    }
    
    /** 微信端 ------------------------------------------------------------------------------------------------------- */
    /**
     * 上传微网站文章轮播图
     *
     * @return JsonResponse
     * @throws Throwable
     */
    function import() {
        
        $media = new Media;
        foreach (Request::allFiles()['images'] as $image) {
            $uploads[] = $media->upload(
                $image, __('messages.wsm_article.title')
            );
        }
        
        return response()->json($uploads ?? []);
        
    }
    
}
