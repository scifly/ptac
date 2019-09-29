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
 * App\Models\Column 微网站栏目
 *
 * @property int $id
 * @property int $wap_id 所属微网站ID
 * @property string $name 模块名称
 * @property int $media_id 模块图片多媒体ID
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int $enabled
 * @property-read Media $media
 * @property-read Wap $wap
 * @property-read Collection|Article[] $articles
 * @property-read int|null $articles_count
 * @method static Builder|Column whereCreatedAt($value)
 * @method static Builder|Column whereEnabled($value)
 * @method static Builder|Column whereId($value)
 * @method static Builder|Column whereMediaId($value)
 * @method static Builder|Column whereName($value)
 * @method static Builder|Column whereUpdatedAt($value)
 * @method static Builder|Column whereWapId($value)
 * @method static Builder|Column newModelQuery()
 * @method static Builder|Column newQuery()
 * @method static Builder|Column query()
 * @mixin Eloquent
 */
class Column extends Model {
    
    use ModelTrait;
    
    protected $fillable = ['id', 'wap_id', 'name', 'media_id', 'enabled'];
    
    /** @return BelongsTo */
    function wap() { return $this->belongsTo('App\Models\Wap'); }
    
    /** @return HasMany */
    function articles() { return $this->hasMany('App\Models\Article'); }
    
    /** @return BelongsTo */
    function media() { return $this->belongsTo('App\Models\Media'); }
    
    /**
     * 返回微网站栏目列表（后台）
     *
     * @return array
     */
    function index() {
        
        $columns = [
            ['db' => 'Columns.id', 'dt' => 0],
            ['db' => 'Columns.name', 'dt' => 1],
            ['db' => 'Wap.name as wname', 'dt' => 2],
            ['db' => 'Columns.created_at', 'dt' => 3],
            ['db' => 'Columns.updated_at', 'dt' => 4],
            [
                'db'        => 'Columns.enabled', 'dt' => 5,
                'formatter' => function ($d, $row) {
                    return Datatable::status($d, $row, false);
                },
            ],
        ];
        $joins = [
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
            ['Column', 'Article'], 'column_id', 'purge', $id
        );
        
    }
    
    /** @return array */
    function compose() {
        
        if (explode('/', Request::path())[1] == 'index') {
            $data = [
                'titles' => ['#', '栏目名称', '所属网站', '创建于', '更新于', '状态 . 操作'],
            ];
        } else {
            $module = Column::find(Request::route('id'));
            $data = [
                'waps'  => Wap::whereSchoolId($this->schoolId())->pluck('name', 'id'),
                'media' => $module ? $module->media : null,
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
            __('messages.column.title')
        );
        
        return response()->json($upload);
        
    }
    
}
