<?php

namespace App\Models;

use App\Facades\DatatableFacade as Datatable;
use Carbon\Carbon;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

/**
 * App\Models\ConferenceParticipant 与会者
 *
 * @property int $id
 * @property int $educator_id 与会者教职员工ID
 * @property string $attendance_time 与会者签到时间
 * @property int $conference_queue_id 会议队列ID
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int $status 状态（0 - 签到已到 1 - 签到未到）
 * @method static Builder|ConferenceParticipant whereAttendanceTime($value)
 * @method static Builder|ConferenceParticipant whereConferenceQueueId($value)
 * @method static Builder|ConferenceParticipant whereCreatedAt($value)
 * @method static Builder|ConferenceParticipant whereEducatorId($value)
 * @method static Builder|ConferenceParticipant whereId($value)
 * @method static Builder|ConferenceParticipant whereStatus($value)
 * @method static Builder|ConferenceParticipant whereUpdatedAt($value)
 * @mixin Eloquent
 * @property-read ConferenceQueue $conferenceQueues
 * @property-read Educator $educator
 * @property-read ConferenceQueue $conferenceQueue
 */
class ConferenceParticipant extends Model {

    protected $fillable = [
        'educator_id', 'attendance_time',
        'conference_queue_id', 'status'
    ];

    /**
     * 返回与会者的教职员工对象
     *
     * @return BelongsTo
     */
    public function educator() { return $this->belongsTo('\App\Models\Educator'); }

    /**
     * 返回与会者参加的会议对象
     *
     * @return BelongsTo
     */
    public function conferenceQueue() { return $this->belongsTo('App\Models\ConferenceQueue'); }
    
    /**
     * 与会者列表
     *
     * @return array
     */
    public function datatable() {
        
        $columns = [
            ['db' => 'ConferenceParticipant.id', 'dt' => 0],
            ['db' => 'User.realname', 'dt' => 1],
            ['db' => 'ConferenceQueue.name', 'dt' => 2],
            ['db' => 'ConferenceParticipant.attendance_time', 'dt' => 3],
            ['db' => 'ConferenceParticipant.created_at', 'dt' => 4],
            ['db' => 'ConferenceParticipant.updated_at', 'dt' => 5],
            [
                'db' => 'ConferenceParticipant.status', 'dt' => 6,
                'formatter' => function ($d) {
                    return $d ? '<span class="badge bg-green">签到已到</span>' :
                        '<span class="badge bg-yellow">签到未到</span>';
                },
            ],
        ];
        $joins = [
            [
                'table' => 'educators',
                'alias' => 'Educator',
                'type' => 'INNER',
                'conditions' => [
                    'Educator.id = ConferenceParticipant.educator_id',
                ],
            ],
            [
                'table' => 'users',
                'alias' => 'User',
                'type' => 'INNER',
                'conditions' => [
                    'User.id = Educator.user_id',
                ],
            ],
            [
                'table' => 'conference_queues',
                'alias' => 'ConferenceQueue',
                'type' => 'INNER',
                'conditions' => [
                    'ConferenceQueue.id = ConferenceParticipant.conference_queue_id',
                ],
            ]
        ];
        $condition = 'Educator.school_id = ' . School::schoolId();
        $user = Auth::user();
        # 普通角色用户只能查看自己发起会议的与会者列表
        if (!in_array($user->group->name, ['运营', '企业', '学校'])) {
            $condition .= ' AND ConferenceQueue.user_id = ' . $user->id;
        }

        return Datatable::simple(self::getModel(), $columns, $joins, $condition);

    }

}
