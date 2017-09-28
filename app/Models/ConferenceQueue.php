<?php
namespace App\Models;

use App\Facades\DatatableFacade as Datatable;
use App\Http\Requests\ConferenceQueueRequest;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\ConferenceQueue 会议队列
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
 * @property-read Collection|ConferenceParticipant[] $conferenceParticipants
 * @property-read ConferenceRoom $conferenceRoom
 */
class ConferenceQueue extends Model {

    protected $table = 'conference_queues';
    protected $fillable = [
        'name', 'remark', 'start', 'end',
        'educator_id', 'educator_ids', 'attended_educator_ids',
        'conference_room_id', 'attendance_qrcode_url', 'event_id',
    ];

    /**
     * 返回举行指定会议的会议室对象
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function conferenceRoom() { return $this->belongsTo('App\Models\ConferenceRoom'); }

    /**
     * 获取参与指定会议的所有教职员工对象
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function conferenceParticipants() { return $this->hasMany('App\Models\ConferenceParticipant'); }

    /**
     * 保存会议
     *
     * @param ConferenceQueueRequest $request
     * @return bool
     */
    public function store(ConferenceQueueRequest $request) {

        return true;

    }

    /**
     * 更新会议
     *
     * @param ConferenceQueueRequest $request
     * @param $id
     * @return bool
     */
    public function modify(ConferenceQueueRequest $request, $id) {

        return true;

    }

    /**
     * 删除会议
     *
     * @param $id
     * @return bool
     */
    public function remove($id) {

        return true;

    }

    public function datatable() {

        $columns = [
            ['db' => 'ConferenceQueue.id', 'dt' => 0],
            ['db' => 'ConferenceQueue.name', 'dt' => 1],
            ['db' => 'ConferenceQueue.remark', 'dt' => 2],
            ['db' => 'ConferenceQueue.start', 'dt' => 3],
            ['db' => 'ConferenceQueue.end', 'dt' => 4],
            ['db' => 'User.realname', 'dt' => 5],
            ['db' => 'ConferenceRoom.name as conferenceroomname', 'dt' => 6],
            [
                'db'        => 'ConferenceQueue.end', 'dt' => 7,
                'formatter' => function ($d, $row) {
                    // 进行中, 已结束, 已取消; 查看, 编辑, 删除
                },
            ],
        ];
        $joins = [
            [
                'table'      => 'conference_rooms',
                'alias'      => 'ConferenceRoom',
                'type'       => 'INNER',
                'conditions' => [
                    'ConferenceRoom.id = ConferenceQueue.conference_room_id',
                ],
            ],
            [
                'table'      => 'educators',
                'alias'      => 'Educator',
                'type'       => 'INNER',
                'conditions' => [
                    'Educator.id = ConferenceQueue.educator_id',
                ],
            ],
            [
                'table'      => 'users',
                'alias'      => 'User',
                'type'       => 'INNER',
                'conditions' => [
                    'User.id = Educator.user_id',
                ],
            ],
        ];

        return Datatable::simple($this, $columns, $joins);

    }

}
