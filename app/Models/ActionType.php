<?php
namespace App\Models;

use App\Helpers\ModelTrait;
use Eloquent;
use Illuminate\Database\Eloquent\{Builder, Model};
use Illuminate\Support\Carbon;
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
        
        return $this->purge($id, [
            'clear.action_type_ids' => ['Action']
        ]);
        
    }
    
}
