<?php

namespace App\Models;

use Eloquent;
use Exception;
use Throwable;
use Carbon\Carbon;
use Illuminate\View\View;
use App\Helpers\ModelTrait;
use App\Helpers\HttpStatusCode;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\View\Factory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\WsmArticleRequest;
use Illuminate\Database\Eloquent\Builder;
use App\Facades\DatatableFacade as Datatable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
 * @mixin Eloquent
 * @property-read WapSiteModule $wapSiteModule
 * @property-read WapSiteModule $wapsitemodule
 * @property-read Media $thumbnailmedia
 */
class WsmArticle extends Model {

    use ModelTrait;
    
    protected $table = 'wsm_articles';

    protected $fillable = [
        'id', 'wsm_id', 'name',
        'summary', 'thumbnail_media_id', 'content',
        'media_ids', 'created_at', 'updated_at',
        'enabled',
    ];
    
    protected $media;
    
    function __construct(array $attributes = []) {
        
        parent::__construct($attributes);
        $this->media = app()->make('App\Models\Media');
        
    }
    
    /**
     * 返回网站文章所属的微网站模块对象
     *
     * @return BelongsTo
     */
    function wapSiteModule() {

        return $this->belongsTo('App\Models\WapSiteModule', 'wsm_id', 'id');

    }

    function thumbnailmedia() {

        return $this->belongsTo('App\Models\Media', 'thumbnail_media_id', 'id');

    }
    
    /**
     * 保存新建的网站文章
     *
     * @param WsmArticleRequest $request
     * @return bool|mixed
     * @throws Exception
     * @throws Throwable
     */
    function store(WsmArticleRequest $request) {

        try {
            //删除原有的图片
            DB::transaction(function () use ($request) {
                self::removeMedias($request);
                return self::create($request->all());
            });
        } catch (Exception $e) {
            throw $e;
        }
        
        return true;

    }
    
    /**
     * 移除关联的媒体文件
     *
     * @param $request
     * @throws Exception
     */
    private function removeMedias(WsmArticleRequest $request) {
        
        //删除原有的图片
        $mediaIds = $request->input('del_ids');
        if ($mediaIds) {
            $medias = Media::whereIn('id', $mediaIds)->get(['id', 'path']);
            foreach ($medias as $media) {
                $paths = explode("/", $media->path);
                Storage::disk('uploads')->delete($paths[sizeof($paths) - 1]);
            }
            try {
                Media::whereIn('id', $mediaIds)->delete();
            } catch (Exception $e) {
                throw $e;
            }
        }
        
    }
    
    /**
     * 更新网站文章
     *
     * @param WsmArticleRequest $request
     * @param $id
     * @return bool|mixed
     * @throws Exception
     * @throws Throwable
     */
    function modify(WsmArticleRequest $request, $id) {
        
        try {
            DB::transaction(function () use ($request, $id) {
                $this->removeMedias($request);
                return $this->find($id)->update(
                    $request->except('_method', '_token', 'del_ids')
                );
            });
        } catch (Exception $e) {
            throw $e;
        }
        
        return true;
        
    }
    
    /**
     * 移除网站文章
     *
     * @param $id
     * @return bool|null
     * @throws Exception
     */
    function remove($id = null) {

        return $id
            ? $this->find($id)->delete()
            : $this->whereIn('id', array_values(Request::input('ids')))->delete();
        
    }
    
    /**
     * 上传微网站文章轮播图
     *
     * @return \Illuminate\Http\JsonResponse
     */
    function upload() {
    
        $files = Request::allFiles();
        $media = new Media();
        $uploadedFiles = [];
        foreach ($files['images'] as $image) {
            abort_if(
                empty($image),
                HttpStatusCode::NOT_ACCEPTABLE,
                __('messages.empty_file')
            );
            $uploadedFile = $media->upload(
                $image, __('messages.wsm_article.title')
            );
            abort_if(
                !$uploadedFile,
                HttpStatusCode::INTERNAL_SERVER_ERROR,
                __('messages.file_upload_failed')
            );
            $uploadedFiles[] = $uploadedFile;
        }
        
        return response()->json($uploadedFiles);
    
    }
    
    /**
     * 微网站文章列表
     *
     * @return array
     */
    function datatable() {

        $columns = [
            ['db' => 'WsmArticle.id', 'dt' => 0],
            ['db' => 'Wsm.name as wsmname', 'dt' => 1],
            ['db' => 'WsmArticle.name', 'dt' => 2],
            ['db' => 'WsmArticle.summary', 'dt' => 3],
            ['db' => 'WsmArticle.created_at', 'dt' => 4],
            ['db' => 'WsmArticle.updated_at', 'dt' => 5],
            [
                'db' => 'WsmArticle.enabled', 'dt' => 6,
                'formatter' => function ($d, $row) {
                    return Datatable::dtOps($d, $row, false);
                },
            ],
        ];
        $joins = [
            [
                'table' => 'wap_site_modules',
                'alias' => 'Wsm',
                'type' => 'INNER',
                'conditions' => [
                    'Wsm.id = WsmArticle.wsm_id',
                ],
            ],
            [
                'table' => 'wap_sites',
                'alias' => 'WapSite',
                'type' => 'INNER',
                'conditions' => [
                    'WapSite.id = Wsm.wap_site_id',
                ],
            ],
        ];
        $condition = 'WapSite.school_id = ' . $this->schoolId();
        
        return Datatable::simple(
            $this->getModel(), $columns, $joins, $condition
        );
        
    }
    
    /**
     * 返回指定栏目文章
     *
     * @return Factory|View
     */
    function wIndex() {
    
        $id = Request::input('id');
        $article = $this->find($id);
    
        return view('wechat.wapsite.article', [
            'article' => $article,
            'medias'  => $this->media->medias(
                explode(',', $article->media_ids)
            ),
        ]);
        
    }
    
}
