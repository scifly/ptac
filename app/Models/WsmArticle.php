<?php

namespace App\Models;

use App\Facades\DatatableFacade as Datatable;
use App\Http\Requests\WsmArticleRequest;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

/**
 * App\Models\WsmArticle 网站内容
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
 * @mixin \Eloquent
 * @property-read WapSiteModule $wapSiteModule
 * @property-read WapSiteModule $wapsitemodule
 * @property-read Media $thumbnailmedia
 */
class WsmArticle extends Model {

    protected $table = 'wsm_articles';
    protected $fillable = [
        'id', 'wsm_id', 'name',
        'summary', 'thumbnail_media_id', 'content',
        'media_ids', 'created_at', 'updated_at',
        'enabled',
    ];
    
    /**
     * 返回网站文章所属的微网站模块对象
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function wapSiteModule() {

        return $this->belongsTo('App\Models\WapSiteModule', 'wsm_id', 'id');

    }

    public function thumbnailmedia() {

        return $this->belongsTo('App\Models\Media', 'thumbnail_media_id', 'id');

    }
    
    /**
     * 保存新建的网站文章
     *
     * @param WsmArticleRequest $request
     * @return bool|mixed
     * @throws Exception
     * @throws \Throwable
     */
    static function store(WsmArticleRequest $request) {

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
    private static function removeMedias(WsmArticleRequest $request) {
        
        //删除原有的图片
        $mediaIds = $request->input('del_ids');
        if ($mediaIds) {
            $medias = Media::whereIn('id', $mediaIds)->get(['id', 'path']);
            foreach ($medias as $media) {
                $paths = explode("/", $media->path);
                Storage::disk('uploads')->delete($paths[5]);
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
     * @throws \Throwable
     */
    static function modify(WsmArticleRequest $request, $id) {
        
        $wsma = self::find($id);
        if (!$wsma) { return false; }
        try {
            DB::transaction(function () use ($request, $id) {
                self::removeMedias($request);
                return self::find($id)->update($request->except('_method', '_token', 'del_ids'));
            });
        } catch (Exception $e) {
            throw $e;
        }
        
        return true;
        
    }

    /**
     * 微网站文章列表
     *
     * @return array
     */
    static function datatable() {

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
                    return Datatable::dtOps($d, $row);
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
        $condition = 'WapSite.school_id = ' . School::schoolId();
        
        return Datatable::simple(self::getModel(), $columns, $joins, $condition);
        
    }

}
