<?php

namespace App\Models;

use App\Facades\DatatableFacade as Datatable;
use App\Helpers\ModelTrait;
use Carbon\Carbon;
use Eloquent;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * App\Models\IconType 图标类型
 *
 * @property int $id
 * @property string $name 图标类型名称
 * @property string|null $remark 备注
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int $enabled
 * @property-read Icon[] $icons
 * @method static Builder|IconType whereCreatedAt($value)
 * @method static Builder|IconType whereEnabled($value)
 * @method static Builder|IconType whereId($value)
 * @method static Builder|IconType whereName($value)
 * @method static Builder|IconType whereRemark($value)
 * @method static Builder|IconType whereUpdatedAt($value)
 * @mixin Eloquent
 */
class IconType extends Model {

    use ModelTrait;

    protected $fillable = ['name', 'remark', 'enabled'];

    /**
     * 获取指定图标类型包含的所有图标对象
     *
     * @return HasMany
     */
    function icons() { return $this->hasMany('App\Models\Icon'); }

    /**
     * 保存图标类型
     *
     * @param array $data
     * @return bool
     */
    function store(array $data) {

        $iconType = self::create($data);

        return $iconType ? true : false;

    }

    /**
     * 更新图标类型
     *
     * @param array $data
     * @param $id
     * @return bool
     */
    function modify(array $data, $id) {

        $iconType = self::find($id);
        if (!$iconType) { return false; }

        return $iconType->update($data) ? true : false;

    }
    
    /**
     * 删除图标类型
     *
     * @param $id
     * @return bool|null
     * @throws Exception
     */
    function remove($id) {

        $iconType = self::find($id);
        if (!$iconType) { return false; }

        return $iconType->removable($iconType) ? $iconType->delete() : false;

    }
    
    /**
     * 图标类型列表
     *
     * @return array
     */
    function datatable() {

        $columns = [
            ['db' => 'IconType.id', 'dt' => 0],
            ['db' => 'IconType.name', 'dt' => 1],
            ['db' => 'IconType.remark', 'dt' => 2],
            ['db' => 'IconType.created_at', 'dt' => 3],
            ['db' => 'IconType.updated_at', 'dt' => 4],
            [
                'db' => 'IconType.enabled', 'dt' => 5,
                'formatter' => function ($d, $row) {
                    return Datatable::dtOps($d, $row);
                },
            ],
        ];
        
        return Datatable::simple(
            $this->getModel(), $columns
        );
        
    }

}
