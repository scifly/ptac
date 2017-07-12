<?php

namespace App\Models;

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
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\EducatorAttendance whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\EducatorAttendance whereEasId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\EducatorAttendance whereEducatorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\EducatorAttendance whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\EducatorAttendance whereInorout($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\EducatorAttendance whereLatitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\EducatorAttendance whereLongitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\EducatorAttendance wherePunchTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\EducatorAttendance whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class EducatorAttendance extends Model
{
    //
}
