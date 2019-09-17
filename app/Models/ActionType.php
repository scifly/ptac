<?php
namespace App\Models;

use App\Helpers\ModelTrait;
use Eloquent;
use Exception;
use Illuminate\Database\Eloquent\{Builder, Model};
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Throwable;

/**
 * App\Models\ActionType 功能HTTP请求类型
 *
 * @property int $id
 * @property string $name action类型名称
 * @property string|null $remark 备注
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int $enabled
 * @method static Builder|ActionType newModelQuery()
 * @method static Builder|ActionType newQuery()
 * @method static Builder|ActionType query()
 * @method static Builder|ActionType whereCreatedAt($value)
 * @method static Builder|ActionType whereEnabled($value)
 * @method static Builder|ActionType whereId($value)
 * @method static Builder|ActionType whereName($value)
 * @method static Builder|ActionType whereRemark($value)
 * @method static Builder|ActionType whereUpdatedAt($value)
 * @mixin Eloquent
 */
class ActionType extends Model {
    
    use ModelTrait;
    
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
                $this->purge(['ActionType'], 'action_type_id', 'purge', $id);
                $this->purge(['Action'], ['action_type_ids'], 'clear', $id);
            });
        } catch (Exception $e) {
            throw $e;
        }
        
        return true;
        
    }
    
}
