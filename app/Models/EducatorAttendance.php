<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\EducatorAttendance
 *
 * @property int $id
 * @property int $educator_id 教职员工ID
 * @property string $punch_time 打卡日期时间
 * @property float $longitude 签到时所处经度
 * @property float $latitude 签到时所处纬度
 * @property int $inorout 进或出
 * @property int $eas_id 所属考勤设置ID
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @method static Builder|EducatorAttendance whereCreatedAt($value)
 * @method static Builder|EducatorAttendance whereEasId($value)
 * @method static Builder|EducatorAttendance whereEducatorId($value)
 * @method static Builder|EducatorAttendance whereId($value)
 * @method static Builder|EducatorAttendance whereInorout($value)
 * @method static Builder|EducatorAttendance whereLatitude($value)
 * @method static Builder|EducatorAttendance whereLongitude($value)
 * @method static Builder|EducatorAttendance wherePunchTime($value)
 * @method static Builder|EducatorAttendance whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class EducatorAttendance extends Model {
    //
}
