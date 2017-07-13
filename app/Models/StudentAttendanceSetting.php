<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\StudentAttendanceSetting
 *
 * @property int $id
 * @property string $name 学生考勤设置名称
 * @property int $grade_id 所属年级ID
 * @property int $semester_id 学期ID
 * @property int $ispublic 是否为学校公用考勤设置
 * @property string $start 考勤时段起始时间
 * @property string $end 考勤时段截止时间
 * @property string $day 星期几？
 * @property int $inorout 进或出
 * @property string $msg_template 考勤消息模板
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @method static Builder|StudentAttendanceSetting whereCreatedAt($value)
 * @method static Builder|StudentAttendanceSetting whereDay($value)
 * @method static Builder|StudentAttendanceSetting whereEnd($value)
 * @method static Builder|StudentAttendanceSetting whereGradeId($value)
 * @method static Builder|StudentAttendanceSetting whereId($value)
 * @method static Builder|StudentAttendanceSetting whereInorout($value)
 * @method static Builder|StudentAttendanceSetting whereIspublic($value)
 * @method static Builder|StudentAttendanceSetting whereMsgTemplate($value)
 * @method static Builder|StudentAttendanceSetting whereName($value)
 * @method static Builder|StudentAttendanceSetting whereSemesterId($value)
 * @method static Builder|StudentAttendanceSetting whereStart($value)
 * @method static Builder|StudentAttendanceSetting whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class StudentAttendanceSetting extends Model {
    //
}
