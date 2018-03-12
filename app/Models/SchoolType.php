<?php

namespace App\Models;

use App\Facades\DatatableFacade as Datatable;
use App\Helpers\ModelTrait;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * App\Models\SchoolType 学校类型
 *
 * @property int $id
 * @property string $name 学校类型名称
 * @property string $remark 学校类型备注
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int $enabled
 * @method static Builder|SchoolType whereCreatedAt($value)
 * @method static Builder|SchoolType whereEnabled($value)
 * @method static Builder|SchoolType whereId($value)
 * @method static Builder|SchoolType whereName($value)
 * @method static Builder|SchoolType whereRemark($value)
 * @method static Builder|SchoolType whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property-read Collection|School[] $schools
 */
class SchoolType extends Model {
    
    use ModelTrait;
    
    protected $fillable = ['name', 'remark', 'enabled'];

    /**
     * 获取指定学校类型下属的所有学校对象
     *
     * @return HasMany
     */
    function schools() { return $this->hasMany('App\Models\School'); }
    
    /**
     * 保存学校类型
     *
     * @param array $data
     * @return bool
     */
    function store(array $data) {
        
        $st = self::create($data);
        
        return $st ? true: false;
        
    }
    
    /**
     * 更新学校类型
     *
     * @param array $data
     * @param $id
     * @return bool
     */
    function modify(array $data, $id) {
        
        $st = self::find($id);
        if (!$st) { return false; }
        
        return $st->update($data) ? true: false;
        
    }
    
    /**
     * 删除学校类型
     *
     * @param $id
     * @return bool
     * @throws Exception
     */
    function remove($id) {
        
        $st = self::find($id);
        if (!$st) { return false; }
        $removed = self::removable($st) ? $st->delete() : false;
        
        return $removed ?? false;     
    }
    
    /**
     * 学校类型列表
     *
     * @return array
     */
    function datatable() {

        $columns = [
            ['db' => 'SchoolType.id', 'dt' => 0],
            ['db' => 'SchoolType.name', 'dt' => 1],
            ['db' => 'SchoolType.remark', 'dt' => 2],
            ['db' => 'SchoolType.created_at', 'dt' => 3],
            ['db' => 'SchoolType.updated_at', 'dt' => 4],
            [
                'db' => 'SchoolType.enabled', 'dt' => 5,
                'formatter' => function ($d, $row) {
                    return Datatable::dtOps($d, $row, false);
                },
            ],
        ];

        return Datatable::simple($this->getModel(), $columns);

    }

}
