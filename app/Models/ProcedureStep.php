<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\ProcedureStep
 *
 * @property int $id
 * @property int $procedure_id 流程ID
 * @property string $name 流程步骤名称
 * @property string $approver_user_ids 审批人用户IDs
 * @property string $related_user_ids 相关人用户IDs
 * @property string $remark 流程步骤备注
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property int $enabled
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ProcedureStep whereApproverUserIds($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ProcedureStep whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ProcedureStep whereEnabled($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ProcedureStep whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ProcedureStep whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ProcedureStep whereProcedureId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ProcedureStep whereRelatedUserIds($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ProcedureStep whereRemark($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ProcedureStep whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class ProcedureStep extends Model
{
    //
}
