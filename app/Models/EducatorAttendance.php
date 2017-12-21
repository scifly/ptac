<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\EducatorAttendance 教职员工考勤记录
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
 * @property-read EducatorAppeal $educatorAppeal
 * @property-read EducatorAttendanceSetting $educatorAttendanceSetting
 * @property-read Educator $educator
 */
class EducatorAttendance extends Model {

    protected $table = 'educator_attendances';

    protected $fillable = [
        'educator_id', 'punch_time', 'longitude',
        'latitude', 'inorout', 'eas_id',
    ];

    /**
     * 获取对应的教职员工对象
     *
     * @return BelongsTo
     */
    public function educator() { return $this->belongsTo('App\Models\Educator'); }

    /**
     * 获取对应的教职员工考勤设置对象
     *
     * @return BelongsTo
     */
    public function educatorAttendanceSetting() {

        return $this->belongsTo('App\Models\EducatorAttendanceSetting', 'eas_id');

    }

}
