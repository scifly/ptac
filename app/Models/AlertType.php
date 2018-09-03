<?php
namespace App\Models;

use App\Facades\Datatable;
use App\Helpers\ModelTrait;
use Carbon\Carbon;
use Eloquent;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Throwable;

/**
 * App\Models\AlertType 警告类型
 *
 * @property int $id
 * @property string $name 提前提醒的时间
 * @property string $english_name 提前提醒时间的英文名称
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int $enabled
 * @method static Builder|AlertType whereCreatedAt($value)
 * @method static Builder|AlertType whereEnabled($value)
 * @method static Builder|AlertType whereEnglishName($value)
 * @method static Builder|AlertType whereId($value)
 * @method static Builder|AlertType whereName($value)
 * @method static Builder|AlertType whereUpdatedAt($value)
 * @mixin Eloquent
 */
class AlertType extends Model {
    
    use ModelTrait;
    
    protected $fillable = ['name', 'english_name', 'enabled'];
    
    /**
     * 警告类型列表
     *
     * @return array
     */
    function index() {
        
        $columns = [
            ['db' => 'AlertType.id', 'dt' => 0],
            ['db' => 'AlertType.name', 'dt' => 1],
            ['db' => 'AlertType.english_name', 'dt' => 2],
            ['db' => 'AlertType.created_at', 'dt' => 3],
            ['db' => 'AlertType.updated_at', 'dt' => 4],
            [
                'db'        => 'AlertType.enabled', 'dt' => 5,
                'formatter' => function ($d, $row) {
                    return Datatable::status($d, $row, false);
                },
            ],
        ];
        
        return Datatable::simple($this->getModel(), $columns);
        
    }
    
    /**
     * 保存警告类型
     *
     * @param array $data
     * @return bool
     */
    function store(array $data) {
        
        return $this->create($data) ? true : false;
        
    }
    
    /**
     * 更新警告类型
     *
     * @param array $data
     * @param $id
     * @return bool
     */
    function modify(array $data, $id) {
        
        return $this->find($id)->update($data);
        
    }
    
    /**
     * 删除警告类型
     *
     * @param $id
     * @return bool|null
     * @throws Throwable
     */
    function remove($id) {
        
        return $this->del($this, $id);
        
    }
    
    /**
     * 删除指定警告类型的所有数据
     *
     * @param $id
     * @return bool
     * @throws Throwable
     */
    function purge($id) {
        
        try {
            DB::transaction(function () use ($id) {
                # todo:
                $this->find($id)->delete();
            });
        } catch (Exception $e) {
            throw $e;
        }
        
        return true;
        
    }
    
}
