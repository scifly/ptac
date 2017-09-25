<?php

namespace App\Models;

use App\Facades\DatatableFacade as Datatable;
use App\Helpers\ModelTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\IconType
 *
 * @property int $id
 * @property string $name 图标类型名称
 * @property string|null $remark 备注
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property int $enabled
 * @property-read Icon[] $icons
 * @method static Builder|IconType whereCreatedAt($value)
 * @method static Builder|IconType whereEnabled($value)
 * @method static Builder|IconType whereId($value)
 * @method static Builder|IconType whereName($value)
 * @method static Builder|IconType whereRemark($value)
 * @method static Builder|IconType whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class IconType extends Model {
    
    use ModelTrait;
    
    protected $fillable = ['name', 'remark', 'enabled'];
    
    /**
     * 获取指定图标类型包含的所有图标对象
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function icons() { return $this->hasMany('App\Models\Icon'); }
    
    /**
     * 保存图标类型
     *
     * @param array $data
     * @return bool
     */
    public function store(array $data) {
        
        $iconType = $this->create($data);
        return $iconType ? true : false;
        
    }
    
    /**
     * 更新图标类型
     *
     * @param array $data
     * @param $id
     * @return bool
     */
    public function modify(array $data, $id) {
        
        $iconType = $this->find($id);
        if (!$iconType) {
            return false;
        }
        return $iconType->update($data) ? true : false;
        
    }
    
    /**
     * 删除图标类型
     *
     * @param $id
     * @return bool|null
     */
    public function remove($id) {
        
        $iconType = $this->find($id);
        if (!$iconType) { return false; }
        return $iconType->removable($iconType)
            ? $iconType->delete() : false;
        
    }
    
    public function datatable() {
        
        $columns = [
            ['db' => 'IconType.id', 'dt' => 0],
            ['db' => 'IconType.name', 'dt' => 1],
            ['db' => 'IconType.remark', 'dt' => 2],
            ['db' => 'IconType.created_at', 'dt' => 3],
            ['db' => 'IconType.updated_at', 'dt' => 4],
            [
                'db' => 'IconType.enabled', 'dt' => 5,
                'formatter' => function ($d, $row) {
                    return Datatable::dtOps($this, $d, $row);
                }
            ]
        ];
        
        return Datatable::simple($this, $columns);
    }
    
}
