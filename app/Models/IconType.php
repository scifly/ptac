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
 * @method static Builder|IconType newModelQuery()
 * @method static Builder|IconType newQuery()
 * @method static Builder|IconType query()
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
     * 图标类型列表
     *
     * @return array
     */
    function index() {
        
        $columns = [
            ['db' => 'IconType.id', 'dt' => 0],
            ['db' => 'IconType.name', 'dt' => 1],
            ['db' => 'IconType.remark', 'dt' => 2],
            ['db' => 'IconType.created_at', 'dt' => 3],
            ['db' => 'IconType.updated_at', 'dt' => 4],
            [
                'db'        => 'IconType.enabled', 'dt' => 5,
                'formatter' => function ($d, $row) {
                    return Datatable::status($d, $row);
                },
            ],
        ];
        
        return Datatable::simple(
            $this->getModel(), $columns
        );
        
    }
    
    /**
     * 保存图标类型
     *
     * @param array $data
     * @return bool
     */
    function store(array $data) {
        
        return $this->create($data) ? true : false;
        
    }
    
    /**
     * 更新图标类型
     *
     * @param array $data
     * @param $id
     * @return bool
     */
    function modify(array $data, $id) {
        
        return $this->find($id)->update($data);
        
    }
    
    /**
     * 删除图标类型
     *
     * @param $id
     * @return bool|null
     * @throws Throwable
     */
    function remove($id = null) {
        
        return $this->del($this, $id);
        
    }
    
    /**
     * 删除指定图标类型的所有相关数据
     *
     * @param $id
     * @return bool
     * @throws Throwable
     */
    function purge($id) {
        
        try {
            DB::transaction(function () use ($id) {
                $this->delRelated('icon_type_id', 'Icon', $id);
                $this->find($id)->delete();
            });
        } catch (Exception $e) {
            throw $e;
        }
        
        return true;
        
    }
    
}
