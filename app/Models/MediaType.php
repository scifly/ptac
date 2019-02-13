<?php
namespace App\Models;

use App\Facades\Datatable;
use App\Helpers\ModelTrait;
use Carbon\Carbon;
use Eloquent;
use Exception;
use Illuminate\Database\Eloquent\{Builder, Model, Relations\HasMany};
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;
use Throwable;

/**
 * App\Models\MediaType 媒体类型
 *
 * @property int $id
 * @property string $name 媒体类型名称
 * @property string $remark 媒体类型备注
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int $enabled
 * @property-read Media[] $medias
 * @method static Builder|MediaType whereCreatedAt($value)
 * @method static Builder|MediaType whereEnabled($value)
 * @method static Builder|MediaType whereId($value)
 * @method static Builder|MediaType whereName($value)
 * @method static Builder|MediaType whereRemark($value)
 * @method static Builder|MediaType whereUpdatedAt($value)
 * @method static Builder|MediaType newModelQuery()
 * @method static Builder|MediaType newQuery()
 * @method static Builder|MediaType query()
 * @mixin Eloquent
 */
class MediaType extends Model {
    
    use ModelTrait;
    
    protected $table = 'media_types';
    
    protected $fillable = ['name', 'remark', 'enabled'];
    
    /**
     * 获取指定媒体类型包含的所有媒体对象
     *
     * @return HasMany
     */
    function medias() { return $this->hasMany('App\Models\Media'); }
    
    /**
     * 媒体类型列表
     *
     * @return array
     */
    function index() {
        
        $columns = [
            ['db' => 'MediaType.id', 'dt' => 0],
            ['db' => 'MediaType.name', 'dt' => 1],
            ['db' => 'MediaType.remark', 'dt' => 2],
            ['db' => 'MediaType.created_at', 'dt' => 3],
            ['db' => 'MediaType.updated_at', 'dt' => 4],
            [
                'db'        => 'MediaType.enabled', 'dt' => 5,
                'formatter' => function ($d, $row) {
                    return Datatable::status($d, $row, false);
                },
            ],
        ];
        
        return Datatable::simple(
            $this->getModel(), $columns
        );
        
    }
    
    /**
     * 保存媒体类型
     *
     * @param array $data
     * @return bool
     */
    function store(array $data) {
        
        return $this->create($data) ? true : false;
        
    }
    
    /**
     * 更新媒体类型
     *
     * @param array $data
     * @param $id
     * @return bool
     */
    function modify(array $data, $id) {
        
        return $this->find($id)->update($data);
        
    }
    
    /**
     * 删除媒体类型
     *
     * @param $id
     * @return bool|null
     * @throws Throwable
     */
    function remove($id) {
    
        try {
            DB::transaction(function () use ($id) {
                $ids = $id ? [$id] : array_values(Request::input('ids'));
                $mediaIds = Media::whereIn('media_type_id', $ids)
                    ->pluck('id')->toArray();
                Request::replace(['ids' => $mediaIds]);
                (new Media)->remove();
                Request::replace(['ids' => $ids]);
                $this->purge(['MediaType'], 'id');
            });
        } catch (Exception $e) {
            throw $e;
        }
    
        return true;
        
    }
    
}
