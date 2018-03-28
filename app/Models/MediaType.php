<?php

namespace App\Models;

use App\Helpers\ModelTrait;
use App\Facades\DatatableFacade as Datatable;
use Carbon\Carbon;
use Eloquent;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
     * 保存媒体类型
     *
     * @param array $data
     * @return bool
     */
    function store(array $data) {

        $mediaType = $this->create($data);

        return $mediaType ? true : false;

    }

    /**
     * 更新媒体类型
     *
     * @param array $data
     * @param $id
     * @return bool
     */
    function modify(array $data, $id) {

        $mediaType = $this->find($id);
        if (!$mediaType) {
            return false;
        }

        return $mediaType->update($data) ? true : false;

    }
    
    /**
     * 删除媒体类型
     *
     * @param $id
     * @return bool|null
     * @throws Exception
     */
    function remove($id) {

        $mediaType = $this->find($id);
        if (!$mediaType) { return false; }

        return $mediaType->removable($mediaType) ? $mediaType->delete() : false;

    }
    
    /**
     * 媒体类型列表
     *
     * @return array
     */
    function datatable() {
        
        $columns = [
            ['db' => 'MediaType.id', 'dt' => 0],
            ['db' => 'MediaType.name', 'dt' => 1],
            ['db' => 'MediaType.remark', 'dt' => 2],
            ['db' => 'MediaType.created_at', 'dt' => 3],
            ['db' => 'MediaType.updated_at', 'dt' => 4],
            [
                'db' => 'MediaType.enabled', 'dt' => 5,
                'formatter' => function ($d, $row) {
                    return Datatable::dtOps($d, $row, false);
                }
            ]
        ];
        
        return Datatable::simple(
            $this->getModel(), $columns
        );
        
    }

}
