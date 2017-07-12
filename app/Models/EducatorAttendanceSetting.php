<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\EducatorAttendanceSetting
 *
 * @property int $id
 * @property string $name 考勤设置名称
 * @property int $school_id 考勤设置所属学校ID
 * @property string $start 考勤设置起始时间
 * @property string $end 考勤设置结束时间
 * @property int $inorout 进或出
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @method static Builder|EducatorAttendanceSetting whereCreatedAt($value)
 * @method static Builder|EducatorAttendanceSetting whereEnd($value)
 * @method static Builder|EducatorAttendanceSetting whereId($value)
 * @method static Builder|EducatorAttendanceSetting whereInorout($value)
 * @method static Builder|EducatorAttendanceSetting whereName($value)
 * @method static Builder|EducatorAttendanceSetting whereSchoolId($value)
 * @method static Builder|EducatorAttendanceSetting whereStart($value)
 * @method static Builder|EducatorAttendanceSetting whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class EducatorAttendanceSetting extends Model
{
    //
}
