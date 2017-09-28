<?php
namespace App\Models;

use App\Facades\DatatableFacade as Datatable;
use App\Http\Requests\WsmArticleRequest;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

/**
 * App\Models\WsmArticle
 *
 * @property int $id
 * @property int $wsm_id 所属网站模块ID
 * @property string $name 文章名称
 * @property string $summary 文章摘要
 * @property int $thumbnail_media_id 缩略图多媒体ID
 * @property string $content 文章内容
 * @property string $media_ids 附件多媒体ID
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
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
 * 网站内容
 * @property-read \App\Models\WapSiteModule $wapSiteModule
 * @property-read \App\Models\WapSiteModule $wapsitemodule
 * @property-read \App\Models\Media $thumbnailmedia
 */
class WsmArticle extends Model {

    protected $table = 'wsm_articles';
    protected $fillable = [
        'id',
        'wsm_id',
        'name',
        'summary',
        'thumbnail_media_id',
        'content',
        'media_ids',
        'created_at',
        'updated_at',
        'enabled',
    ];

    public function wapSiteModule() {
        return $this->belongsTo('App\Models\WapSiteModule', 'wsm_id', 'id');
    }

    public function store(WsmArticleRequest $request) {
        try {
            $exception = DB::transaction(function () use ($request) {
                //删除原有的图片
                $this->removeMedias($request);

                return $this->create($request->all());
            });

            return is_null($exception) ? true : $exception;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * @param $request
     */
    private function removeMedias(WsmArticleRequest $request) {
        //删除原有的图片
        $mediaIds = $request->input('del_ids');
        if ($mediaIds) {
            $medias = Media::whereIn('id', $mediaIds)->get(['id', 'path']);
            foreach ($medias as $media) {
                $paths = explode("/", $media->path);
                Storage::disk('uploads')->delete($paths[5]);

            }
            Media::whereIn('id', $mediaIds)->delete();
        }
    }

    public function modify(WsmArticleRequest $request, $id) {
        $wapSite = $this->find($id);
        if (!$wapSite) {
            return false;
        }
        try {
            $exception = DB::transaction(function () use ($request, $id) {
                $this->removeMedias($request);

                return $this->where('id', $id)->update($request->except('_method', '_token'));
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
            ['db' => 'WsmArticle.id', 'dt' => 0],
            ['db' => 'Wsm.name as wsmname', 'dt' => 1],
            ['db' => 'WsmArticle.name', 'dt' => 2],
            ['db' => 'WsmArticle.summary', 'dt' => 3],
            ['db' => 'WsmArticle.created_at', 'dt' => 4],
            ['db' => 'WsmArticle.updated_at', 'dt' => 5],
            [
                'db'        => 'WsmArticle.enabled', 'dt' => 6,
                'formatter' => function ($d, $row) {
                    return Datatable::dtOps($this, $d, $row);
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
        ];

        return Datatable::simple($this, $columns, $joins);
    }
}
