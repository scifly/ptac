<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\ConferenceParticipant
 *
 * @property int $id
 * @property int $educator_id 与会者教职员工ID
 * @property string $attendance_time 与会者签到时间
 * @property int $conference_queue_id 会议队列ID
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property int $status 状态（0 - 签到已到 1 - 签到未到）
 * @method static Builder|ConferenceParticipant whereAttendanceTime($value)
 * @method static Builder|ConferenceParticipant whereConferenceQueueId($value)
 * @method static Builder|ConferenceParticipant whereCreatedAt($value)
 * @method static Builder|ConferenceParticipant whereEducatorId($value)
 * @method static Builder|ConferenceParticipant whereId($value)
 * @method static Builder|ConferenceParticipant whereStatus($value)
 * @method static Builder|ConferenceParticipant whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class ConferenceParticipant extends Model {
    
    //
    
}
