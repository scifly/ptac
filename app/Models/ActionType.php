<?php
namespace App\Models;

use App\Helpers\ModelTrait;
use Carbon\Carbon;
use Eloquent;
use Exception;
use Illuminate\Database\Eloquent\{Builder, Model};
use Illuminate\Support\Facades\DB;
use Throwable;

/**
 * App\Models\ActionType 功能HTTP请求类型
 *
 * @property int $id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string $name action类型名称
 * @property string|null $remark 备注
 * @property int $enabled
 * @method static Builder|School whereCreatedAt($value)
 * @method static Builder|School whereEnabled($value)
 * @method static Builder|School whereId($value)
 * @method static Builder|School whereName($value)
 * @method static Builder|School whereRemark($value)
 * @method static Builder|School whereUpdatedAt($value)
 * @method static Builder|ActionType newModelQuery()
 * @method static Builder|ActionType newQuery()
 * @method static Builder|ActionType query()
 * @mixin Eloquent
 */
class ActionType extends Model {
    
    use ModelTrait;
    
    protected $table = 'action_types';
    
    protected $fillable = ['name', 'remark', 'enabled'];
    
    /**
     * 删除Http请求类型
     *
     * @param $id
     * @return bool|null
     * @throws Throwable
     */
    function remove($id = null) {
        
        try {
            DB::transaction(function () use ($id) {
                $this->purge([class_basename($this)], 'action_type_id', 'purge', $id);
                $this->purge(['Action'], ['action_type_ids'], 'clear', $id);
            });
        } catch (Exception $e) {
            throw $e;
        }
        
        return true;
        
    }
    
}
