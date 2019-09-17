<?php
namespace App\Models;

use App\Helpers\ModelTrait;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * App\Models\Flow 审批流程日志
 *
 * @property int $id
 * @property int $flow_type_id 流程类型ID
 * @property int $user_id 发起人用户ID
 * @property mixed|null $logs 审批日志
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int $enabled
 * @method static Builder|Flow newModelQuery()
 * @method static Builder|Flow newQuery()
 * @method static Builder|Flow query()
 * @method static Builder|Flow whereCreatedAt($value)
 * @method static Builder|Flow whereEnabled($value)
 * @method static Builder|Flow whereFlowTypeId($value)
 * @method static Builder|Flow whereId($value)
 * @method static Builder|Flow whereLogs($value)
 * @method static Builder|Flow whereUpdatedAt($value)
 * @method static Builder|Flow whereUserId($value)
 * @mixin Eloquent
 * @property-read FlowType $flowType
 * @property-read User $user
 */
class Flow extends Model {
    
    use ModelTrait;
    
    protected $fillable = ['flow_type_id', 'user_id', 'logs', 'enabled'];
    
    /** @return BelongsTo */
    function flowType() { return $this->belongsTo('App\Models\FlowType'); }
    
    /** @return BelongsTo */
    function user() { return $this->belongsTo('App\Models\User'); }
    
}
