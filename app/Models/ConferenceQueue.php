<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
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
 * @method static Builder|ConferenceQueue whereAttendanceQrcodeUrl($value)
 * @method static Builder|ConferenceQueue whereAttendedEducatorIds($value)
 * @method static Builder|ConferenceQueue whereConferenceRoomId($value)
 * @method static Builder|ConferenceQueue whereCreatedAt($value)
 * @method static Builder|ConferenceQueue whereEducatorId($value)
 * @method static Builder|ConferenceQueue whereEducatorIds($value)
 * @method static Builder|ConferenceQueue whereEnd($value)
 * @method static Builder|ConferenceQueue whereEventId($value)
 * @method static Builder|ConferenceQueue whereId($value)
 * @method static Builder|ConferenceQueue whereName($value)
 * @method static Builder|ConferenceQueue whereRemark($value)
 * @method static Builder|ConferenceQueue whereStart($value)
 * @method static Builder|ConferenceQueue whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\ConferenceParticipant[] $conferenceParticipants
 * @property-read \App\Models\ConferenceRoom $conferenceRoom
 */
class ConferenceQueue extends Model {
    
    protected $table = 'conference_queues';
    protected $fillable = [
        'name',
        'remark',
        'start',
        'end',
        'educator_id',
        'educator_ids',
        'attended_educator_ids',
        'conference_room_id',
        'attendance_qrcode_url',
        'event_id'
    ];
    
    /**
     * 会议与会议参与者
     */
    public function conferenceParticipants() {
        return $this->hasMany('App\Models\ConferenceParticipant');
    }
    
    /**
     * 会议与会议地址
     */
    public function conferenceRoom() {
        return $this->belongsTo('App\Models\ConferenceRoom');
    }
}
