<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\ProcedureLog
 *
 * @property int $id
 * @property int $initiator_user_id 发起人用户ID
 * @property int $procedure_id 流程ID
 * @property int $procedure_step_id 流程步骤ID
 * @property int $operator_user_id 操作者用户ID
 * @property string $initiator_msg （发起人）步骤相关留言
 * @property string $initiator_media_ids （发起人）步骤相关附件媒体IDs
 * @property string $operator_msg （操作者）步骤相关留言
 * @property string $operator_media_ids （操作者）步骤相关附件媒体IDs
 * @property int $step_status 步骤状态：0-通过、1-拒绝、2-待定
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ProcedureLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ProcedureLog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ProcedureLog whereInitiatorMediaIds($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ProcedureLog whereInitiatorMsg($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ProcedureLog whereInitiatorUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ProcedureLog whereOperatorMediaIds($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ProcedureLog whereOperatorMsg($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ProcedureLog whereOperatorUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ProcedureLog whereProcedureId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ProcedureLog whereProcedureStepId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ProcedureLog whereStepStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ProcedureLog whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class ProcedureLog extends Model
{
    //
}
