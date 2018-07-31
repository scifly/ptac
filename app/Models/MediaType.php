<?php
namespace App\Models;

use App\Facades\Datatable;
use App\Helpers\ModelTrait;
use Carbon\Carbon;
use Eloquent;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;
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
 * @method static Builder|MediaType whereCreatedAt($value)
 * @method static Builder|MediaType whereEnabled($value)
 * @method static Builder|MediaType whereId($value)
 * @method static Builder|MediaType whereName($value)
 * @method static Builder|MediaType whereRemark($value)
 * @method static Builder|MediaType whereUpdatedAt($value)
 * @mixin Eloquent
 * @property-read Media[] $medias
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
                    return Datatable::dtOps($d, $row, false);
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
        
        return $this->del($this, $id);
        
    }
    
    /**
     * 删除指定媒体类型的所有相关数据
     *
     * @param $id
     * @throws Throwable
     */
    function purge($id) {
        
        try {
            DB::transaction(function () use ($id) {
                Media::whereMediaTypeId($id)->update(['media_type_id' => 0]);
                $this->find($id)->delete();
            });
        } catch (Exception $e) {
            throw $e;
        }
        
    }
    
}
