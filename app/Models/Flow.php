<?php
namespace App\Models;

use App\Helpers\ModelTrait;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Flow 审批流程日志
 *
 * @property int $id
 * @property int $flow_type_id 流程类型ID
 * @property int $user_id 发起人用户ID
 * @property mixed|null $logs 审批日志
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int $enabled
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Flow newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Flow newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Flow query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Flow whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Flow whereEnabled($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Flow whereFlowTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Flow whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Flow whereLogs($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Flow whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Flow whereUserId($value)
 * @mixin \Eloquent
 */
class Flow extends Model {
    
    use ModelTrait;
    
}
