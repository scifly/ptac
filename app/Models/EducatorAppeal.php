<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\EducatorAppeal
 *
 * @property int $id
 * @property int $educator_id 教职员工ID
 * @property string $ea_ids 考勤记录IDs
 * @property string $appeal_content 申诉内容(考勤/会议/其他)
 * @property int $procedure_log_id 相关流程日志ID
 * @property string $approver_educator_ids 审批人教职员工IDs
 * @property string $related_educator_ids 相关人教职员工IDs
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property int $status 审批状态 0 - 通过 1 - 拒绝 2 - 待审
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\EducatorAppeal whereAppealContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\EducatorAppeal whereApproverEducatorIds($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\EducatorAppeal whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\EducatorAppeal whereEaIds($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\EducatorAppeal whereEducatorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\EducatorAppeal whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\EducatorAppeal whereProcedureLogId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\EducatorAppeal whereRelatedEducatorIds($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\EducatorAppeal whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\EducatorAppeal whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class EducatorAppeal extends Model
{
    //
}
