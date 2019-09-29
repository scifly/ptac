<?php
namespace App\Models;

use App\Facades\Datatable;
use App\Helpers\{Constant, ModelTrait};
use Carbon\Carbon;
use Eloquent;
use Exception;
use Illuminate\Database\Eloquent\{Builder,
    Collection,
    Model,
    Relations\BelongsTo,
    Relations\HasMany,
    Relations\HasManyThrough};
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\{DB, Request};
use Throwable;

/**
 * App\Models\Wap 微网站
 *
 * @property int $id
 * @property int $school_id 所属学校ID
 * @property string $name 首页抬头
 * @property string $media_ids 首页幻灯片图片多媒体ID
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int $enabled
 * @property-read School $school
 * @property-read Collection|Article[] $articles
 * @property-read int|null $articles_count
 * @property-read Collection|Column[] $columns
 * @property-read int|null $columns_count
 * @method static Builder|Wap whereCreatedAt($value)
 * @method static Builder|Wap whereEnabled($value)
 * @method static Builder|Wap whereId($value)
 * @method static Builder|Wap whereMediaIds($value)
 * @method static Builder|Wap whereSchoolId($value)
 * @method static Builder|Wap whereName($value)
 * @method static Builder|Wap whereUpdatedAt($value)
 * @method static Builder|Wap newModelQuery()
 * @method static Builder|Wap newQuery()
 * @method static Builder|Wap query()
 * @mixin Eloquent
 */
class Wap extends Model {
    
    use ModelTrait;
    
    protected $fillable = ['id', 'school_id', 'name', 'media_ids', 'enabled'];
    
    /** @return HasMany */
    function columns() { return $this->hasMany('App\Models\Column'); }
    
    /** @return HasManyThrough */
    function articles() { return $this->hasManyThrough('App\Models\Article', 'App\Models\Column'); }
    
    /** @return BelongsTo */
    function school() { return $this->belongsTo('App\Models\School'); }
    
    /**
     * 微网站列表
     *
     * @return array
     */
    function datatable() {
        
        $columns = [
            ['db' => 'Wap.id', 'dt' => 0],
            [
                'db'        => 'School.name as sname', 'dt' => 1,
                'formatter' => function ($d) {
                    return $this->iconHtml($d, 'school');
                },
            ],
            ['db' => 'Wap.name', 'dt' => 2],
            ['db' => 'Wap.created_at', 'dt' => 3],
            ['db' => 'Wap.updated_at', 'dt' => 4],
            [
                'db'        => 'Wap.enabled', 'dt' => 5,
                'formatter' => function ($d, $row) {
                    return Datatable::status($d, $row);
                },
            ],
        ];
        $joins = [
            [
                'table'      => 'schools',
                'alias'      => 'School',
                'type'       => 'INNER',
                'conditions' => [
                    'School.id = Wap.school_id',
                ],
            ],
        ];
        $condition = 'Wap.school_id = ' . $this->schoolId();
        
        return Datatable::simple(
            $this, $columns, $joins, $condition
        );
        
    }
    
    /**
     * 返回微网站基本信息
     *
     * @return array
     */
    function index() {
        
        $condition = [
            'school_id' => $this->schoolId(),
        ];
        if (!$wap = $this->where($condition)->first()) {
            $wap = $this->create([
                'school_id'  => $schoolId = $this->schoolId(),
                'name' => School::find($schoolId)->name,
                'media_ids'  => '',
                'enabled'    => Constant::ENABLED,
            ]);
        }
        
        return ['wap' => $wap];
        
    }
    
    /**
     * 上传微网站首页轮播图
     *
     * @return JsonResponse
     * @throws Throwable
     */
    function import() {
        
        $media = new Media;
        foreach (Request::allFiles()['images'] as $image) {
            $uploads[] = $media->upload(
                $image, __('messages.wap.title')
            );
        }
        
        return response()->json($uploads ?? []);
        
    }
    
    /**
     * 更新微网站
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
     * 删除微网站
     *
     * @param null $id
     * @return bool
     * @throws Throwable
     */
    function remove($id = null) {
        
        try {
            DB::transaction(function () use ($id) {
                $ids = $id ? [$id] : array_values(Request::input('ids'));
                $columnIds = Column::whereIn('wap_id', $ids)
                    ->pluck('id')->toArray();
                Request::replace(['ids' => $columnIds]);
                (new Column)->remove();
                Request::replace(['ids' => $ids]);
                $this->purge(['Wap'], 'id');
            });
        } catch (Exception $e) {
            throw $e;
        }
        
        return true;
        
    }
    
    /** @return array */
    function compose() {
        
        $ws = Wap::find(Request::route('id'));
        $mediaIds = explode(',', $ws ? $ws->media_ids : null);
        $medias = Media::whereIn('id', $mediaIds)->get();
        
        return ['medias' => $medias];
        
    }
    
}
