<?php
namespace App\Models;

use App\Facades\Datatable;
use App\Helpers\ModelTrait;
use Carbon\Carbon;
use Eloquent;
use Illuminate\Database\Eloquent\{Builder, Model, Relations\BelongsTo};
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\{Request};
use Throwable;

/**
 * App\Models\Article 微网站栏目文章
 *
 * @property int $id
 * @property int $column_id 所属网站模块ID
 * @property string $name 文章名称
 * @property string $summary 文章摘要
 * @property int $media_id 缩略图多媒体ID
 * @property string $content 文章内容
 * @property string $media_ids 附件多媒体ID
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int $enabled
 * @property-read Column $column
 * @property-read Media $media
 * @method static Builder|Article whereContent($value)
 * @method static Builder|Article whereCreatedAt($value)
 * @method static Builder|Article whereEnabled($value)
 * @method static Builder|Article whereId($value)
 * @method static Builder|Article whereMediaIds($value)
 * @method static Builder|Article whereName($value)
 * @method static Builder|Article whereSummary($value)
 * @method static Builder|Article whereMediaId($value)
 * @method static Builder|Article whereUpdatedAt($value)
 * @method static Builder|Article whereColumnId($value)
 * @method static Builder|Article newModelQuery()
 * @method static Builder|Article newQuery()
 * @method static Builder|Article query()
 * @mixin Eloquent
 */
class Article extends Model {
    
    use ModelTrait;
    
    protected $fillable = [
        'id', 'column_id', 'name', 'summary', 'media_id',
        'content', 'media_ids', 'created_at', 'updated_at', 'enabled',
    ];
    
    protected $media;
    
    /** @return BelongsTo */
    function column() { return $this->belongsTo('App\Models\Column'); }
    
    /** @return BelongsTo */
    function media() { return $this->belongsTo('App\Models\Media'); }
    
    /**
     * 文章列表
     *
     * @return array
     */
    function index() {
        
        $columns = [
            ['db' => 'Article.id', 'dt' => 0],
            ['db' => 'Columns.name as cname', 'dt' => 1],
            ['db' => 'Article.name', 'dt' => 2],
            ['db' => 'Article.summary', 'dt' => 3],
            ['db' => 'Article.created_at', 'dt' => 4],
            ['db' => 'Article.updated_at', 'dt' => 5],
            [
                'db'        => 'Article.enabled', 'dt' => 6,
                'formatter' => function ($d, $row) {
                    return Datatable::status($d, $row, false);
                },
            ],
        ];
        $joins = [
            [
                'table'      => 'columns',
                'alias'      => 'Columns',
                'type'       => 'INNER',
                'conditions' => [
                    'Columns.id = Article.column_id',
                ],
            ],
            [
                'table'      => 'waps',
                'alias'      => 'Wap',
                'type'       => 'INNER',
                'conditions' => [
                    'Wap.id = Columns.wap_id',
                ],
            ],
        ];
        $condition = 'Wap.school_id = ' . $this->schoolId();
        
        return Datatable::simple(
            $this, $columns, $joins, $condition
        );
        
    }
    
    /**
     * 保存文章
     *
     * @param array $data
     * @return bool|mixed
     */
    function store(array $data) {
        
        return $this->create($data) ? true : false;
        
    }
    
    /**
     * 更新文章
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
     * 移除文章
     *
     * @param $id
     * @return bool|null
     * @throws Throwable
     */
    function remove($id = null) {
        
        return $this->purge(['Article'], 'id', 'purge', $id);
        
    }
    
    /** @return array */
    function compose() {
    
        $action = explode('/', Request::path())[1];
        if ($action == 'index') {
            $data = [
                'titles' => ['#', '所属栏目', '文章名称', '文章摘要', '创建于', '更新于', '状态 . 操作'],
            ];
        } else {
            $article = Article::find(Request::route('id'));
            $mediaIds = explode(',', $article ? $article->media_ids : null);
            $data = [
                'columns' => School::find($this->schoolId())->wap->columns->pluck('name', 'id'),
                'medias'  => Media::whereIn('id', $mediaIds)->get(),
            ];
        }
        
        return $data;
        
    }
    
    /** 微信端 ------------------------------------------------------------------------------------------------------- */
    /**
     * 上传轮播图
     *
     * @return JsonResponse
     * @throws Throwable
     */
    function import() {
        
        $media = new Media;
        foreach (Request::allFiles()['images'] as $image) {
            $uploads[] = $media->upload(
                $image, __('messages.article.title')
            );
        }
        
        return response()->json($uploads ?? []);
        
    }
    
}
