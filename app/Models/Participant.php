<?php
namespace App\Models;

use App\Facades\Datatable;
use App\Helpers\{Constant, ModelTrait};
use Carbon\Carbon;
use Eloquent;
use Illuminate\Database\Eloquent\{Builder, Model, Relations\BelongsTo};
use Illuminate\Support\Facades\Auth;
use Throwable;

/**
 * App\Models\Participant 与会者
 *
 * @property int $id
 * @property int $educator_id 与会者教职员工ID
 * @property string $attendance_time 与会者签到时间
 * @property int $conference_queue_id 会议队列ID
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int $status 状态（0 - 签到已到 1 - 签到未到）
 * @property-read Conference $conferenceQueues
 * @property-read Educator $educator
 * @property-read Conference $conferenceQueue
 * @method static Builder|Participant whereAttendanceTime($value)
 * @method static Builder|Participant whereConferenceQueueId($value)
 * @method static Builder|Participant whereCreatedAt($value)
 * @method static Builder|Participant whereEducatorId($value)
 * @method static Builder|Participant whereId($value)
 * @method static Builder|Participant whereStatus($value)
 * @method static Builder|Participant whereUpdatedAt($value)
 * @method static Builder|Participant newModelQuery()
 * @method static Builder|Participant newQuery()
 * @method static Builder|Participant query()
 * @mixin Eloquent
 * @property int $conference_id 会议id
 * @property string $signed_up 与会者签到时间
 * @method static Builder|Participant whereConferenceId($value)
 * @method static Builder|Participant whereSignedUp($value)
 */
class Participant extends Model {
    
    use ModelTrait;
    
    protected $fillable = [
        'educator_id', 'attendance_time',
        'conference_queue_id', 'status',
    ];
    
    /**
     * 返回与会者的教职员工对象
     *
     * @return BelongsTo
     */
    function educator() { return $this->belongsTo('\App\Models\Educator'); }
    
    /**
     * 返回与会者参加的会议对象
     *
     * @return BelongsTo
     */
    function conferenceQueue() { return $this->belongsTo('App\Models\Conference'); }
    
    /**
     * 与会者列表
     *
     * @return array
     */
    function index() {
        
        $columns = [
            ['db' => 'Participant.id', 'dt' => 0],
            ['db' => 'User.realname', 'dt' => 1],
            ['db' => 'Conference.name', 'dt' => 2],
            ['db' => 'Participant.attendance_time', 'dt' => 3],
            ['db' => 'Participant.created_at', 'dt' => 4],
            ['db' => 'Participant.updated_at', 'dt' => 5],
            [
                'db'        => 'Participant.status', 'dt' => 6,
                'formatter' => function ($d) {
                    return $this->badge(
                        'text-' . ($d ? 'green' : 'yellow'),
                        $d ? '签到已到' : '签到未到'
                    );
                },
            ],
        ];
        $joins = [
            [
                'table'      => 'educators',
                'alias'      => 'Educator',
                'type'       => 'INNER',
                'conditions' => [
                    'Educator.id = Participant.educator_id',
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
            [
                'table'      => 'conference_queues',
                'alias'      => 'Conference',
                'type'       => 'INNER',
                'conditions' => [
                    'Conference.id = Participant.conference_queue_id',
                ],
            ],
        ];
        $condition = 'Educator.school_id = ' . $this->schoolId();
        $user = Auth::user();
        # 普通角色用户只能查看自己发起会议的与会者列表
        if (!in_array($user->role(), Constant::SUPER_ROLES)) {
            $condition .= ' AND Conference.user_id = ' . $user->id;
        }
        
        return Datatable::simple($this, $columns, $joins, $condition);
        
    }
    
    /**
     * 保存与会者记录
     *
     * @param array $data
     * @return bool
     */
    function store(array $data) {
        
        return $this->create($data) ? true : false;
        
    }
    
    /**
     * 更新与会者记录
     *
     * @param array $data
     * @param $id
     * @return bool
     */
    function modify(array $data, $id) {
        
        return $this->find($id)->update($data);
        
    }
    
    /**
     * 删除与会者记录
     *
     * @param null $id
     * @return bool
     * @throws Throwable
     */
    function remove($id = null) {
        
        return $this->purge([class_basename($this)], 'id', 'purge', $id);
        
    }
    
}
