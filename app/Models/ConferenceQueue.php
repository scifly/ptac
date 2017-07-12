<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\ConferenceQueue
 *
 * @property int $id
 * @property string $name 会议名称
 * @property string $remark 会议备注
 * @property string $start 会议开始时间
 * @property string $end 会议结束时间
 * @property int $educator_id 发起人教职员工ID
 * @property string $educator_ids （应到）与会者教职员工ID
 * @property string $attended_educator_ids （应到）与会者教职员工ID
 * @property int $conference_room_id 会议室ID
 * @property string $attendance_qrcode_url 扫码签到用二维码URL
 * @property int $event_id 相关日程ID
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ConferenceQueue whereAttendanceQrcodeUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ConferenceQueue whereAttendedEducatorIds($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ConferenceQueue whereConferenceRoomId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ConferenceQueue whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ConferenceQueue whereEducatorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ConferenceQueue whereEducatorIds($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ConferenceQueue whereEnd($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ConferenceQueue whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ConferenceQueue whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ConferenceQueue whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ConferenceQueue whereRemark($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ConferenceQueue whereStart($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ConferenceQueue whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class ConferenceQueue extends Model
{
    //
}
