<?php
namespace App\Models;

use App\Facades\Datatable;
use App\Helpers\Constant;
use App\Helpers\ModelTrait;
use App\Helpers\Snippet;
use Carbon\Carbon;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;
use Throwable;

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
 * @property-read ConferenceQueue $conferenceQueues
 * @property-read Educator $educator
 * @property-read ConferenceQueue $conferenceQueue
 * @method static Builder|ConferenceParticipant whereAttendanceTime($value)
 * @method static Builder|ConferenceParticipant whereConferenceQueueId($value)
 * @method static Builder|ConferenceParticipant whereCreatedAt($value)
 * @method static Builder|ConferenceParticipant whereEducatorId($value)
 * @method static Builder|ConferenceParticipant whereId($value)
 * @method static Builder|ConferenceParticipant whereStatus($value)
 * @method static Builder|ConferenceParticipant whereUpdatedAt($value)
 * @method static Builder|ConferenceParticipant newModelQuery()
 * @method static Builder|ConferenceParticipant newQuery()
 * @method static Builder|ConferenceParticipant query()
 * @mixin Eloquent
 */
class ConferenceParticipant extends Model {
    
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
    function conferenceQueue() { return $this->belongsTo('App\Models\ConferenceQueue'); }
    
    /**
     * 与会者列表
     *
     * @return array
     */
    function index() {
        
        $columns = [
            ['db' => 'ConferenceParticipant.id', 'dt' => 0],
            ['db' => 'User.realname', 'dt' => 1],
            ['db' => 'ConferenceQueue.name', 'dt' => 2],
            ['db' => 'ConferenceParticipant.attendance_time', 'dt' => 3],
            ['db' => 'ConferenceParticipant.created_at', 'dt' => 4],
            ['db' => 'ConferenceParticipant.updated_at', 'dt' => 5],
            [
                'db'        => 'ConferenceParticipant.status', 'dt' => 6,
                'formatter' => function ($d) {
                    return $d ? sprintf(Snippet::BADGE_GREEN, '签到已到') :
                        sprintf(Snippet::BADGE_YELLOW, '签到未到');
                },
            ],
        ];
        $joins = [
            [
                'table'      => 'educators',
                'alias'      => 'Educator',
                'type'       => 'INNER',
                'conditions' => [
                    'Educator.id = ConferenceParticipant.educator_id',
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
                'alias'      => 'ConferenceQueue',
                'type'       => 'INNER',
                'conditions' => [
                    'ConferenceQueue.id = ConferenceParticipant.conference_queue_id',
                ],
            ],
        ];
        $condition = 'Educator.school_id = ' . $this->schoolId();
        $user = Auth::user();
        # 普通角色用户只能查看自己发起会议的与会者列表
        if (!in_array($user->role(), Constant::SUPER_ROLES)) {
            $condition .= ' AND ConferenceQueue.user_id = ' . $user->id;
        }
        
        return Datatable::simple($this->getModel(), $columns, $joins, $condition);
        
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
