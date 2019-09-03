<?php
namespace App\Models;

use App\Facades\Datatable;
use App\Helpers\{Constant, ModelTrait};
use Carbon\Carbon;
use Eloquent;
use Exception;
use Illuminate\Database\Eloquent\{Builder, Collection, Model, Relations\BelongsTo, Relations\HasMany};
use Illuminate\Support\Facades\{Auth, DB, Request};
use Throwable;

/**
 * App\Models\ConferenceQueue 会议队列
 *
 * @property int $id
 * @property string $name 会议名称
 * @property string $remark 会议备注
 * @property string $start 会议开始时间
 * @property string $end 会议结束时间
 * @property int $user_id 发起人用户ID
 * @property string $educator_ids （应到）与会者教职员工ID
 * @property string $attended_educator_ids （应到）与会者教职员工ID
 * @property int $conference_room_id 会议室ID
 * @property string $attendance_qrcode_url 扫码签到用二维码URL
 * @property int $event_id 相关日程ID
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int $educator_id 发起人教职员工ID
 * @property int $status 会议状态
 * @property-read Collection|ConferenceParticipant[] $conferenceParticipants
 * @property-read ConferenceRoom $conferenceRoom
 * @property-read User $user
 * @method static Builder|ConferenceQueue whereAttendanceQrcodeUrl($value)
 * @method static Builder|ConferenceQueue whereAttendedEducatorIds($value)
 * @method static Builder|ConferenceQueue whereConferenceRoomId($value)
 * @method static Builder|ConferenceQueue whereCreatedAt($value)
 * @method static Builder|ConferenceQueue whereUserId($value)
 * @method static Builder|ConferenceQueue whereEducatorIds($value)
 * @method static Builder|ConferenceQueue whereEnd($value)
 * @method static Builder|ConferenceQueue whereEventId($value)
 * @method static Builder|ConferenceQueue whereId($value)
 * @method static Builder|ConferenceQueue whereName($value)
 * @method static Builder|ConferenceQueue whereRemark($value)
 * @method static Builder|ConferenceQueue whereStart($value)
 * @method static Builder|ConferenceQueue whereUpdatedAt($value)
 * @method static Builder|ConferenceQueue whereEducatorId($value)
 * @method static Builder|ConferenceQueue whereStatus($value)
 * @method static Builder|ConferenceQueue newModelQuery()
 * @method static Builder|ConferenceQueue newQuery()
 * @method static Builder|ConferenceQueue query()
 * @mixin Eloquent
 */
class ConferenceQueue extends Model {
    
    use ModelTrait;
    
    protected $table = 'conference_queues';
    
    protected $fillable = [
        'name', 'remark', 'start', 'end',
        'user_id', 'educator_ids', 'attended_educator_ids',
        'conference_room_id', 'attendance_qrcode_url',
        'event_id', 'status',
    ];
    
    /** Properties -------------------------------------------------------------------------------------------------- */
    /**
     * 返回举行指定会议的会议室对象
     *
     * @return BelongsTo
     */
    function conferenceRoom() { return $this->belongsTo('App\Models\ConferenceRoom'); }
    
    /**
     * 获取参与指定会议的所有教职员工对象
     *
     * @return HasMany
     */
    function conferenceParticipants() { return $this->hasMany('App\Models\ConferenceParticipant'); }
    
    /**
     * 获取会议发起者的用户对象
     *
     * @return BelongsTo
     */
    function user() { return $this->belongsTo('App\Models\User'); }
    
    /** crud -------------------------------------------------------------------------------------------------------- */
    /**
     * 会议列表
     *
     * @return array
     */
    function index() {
        
        $columns = [
            ['db' => 'ConferenceQueue.id', 'dt' => 0],
            ['db' => 'ConferenceQueue.name', 'dt' => 1],
            ['db' => 'User.realname', 'dt' => 2],
            ['db' => 'ConferenceRoom.name as conferenceroomname', 'dt' => 3],
            ['db' => 'ConferenceQueue.start', 'dt' => 4],
            ['db' => 'ConferenceQueue.end', 'dt' => 5],
            ['db' => 'ConferenceQueue.remark', 'dt' => 6],
            [
                'db'        => 'ConferenceQueue.status', 'dt' => 7,
                'formatter' => function ($d, $row) {
                    $statuses = [
                        ['green', '进行中'],
                        ['red', '已结束'],
                        ['gray', '已取消'],
                    ];
                    $status = sprintf(
                        '<i class="fa fa-circle text-%s" title="%s"></i>',
                        $statuses[$d][0], $statuses[$d][1]
                    );
                    
                    return Datatable::status($status, $row);
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
                'table'      => 'users',
                'alias'      => 'User',
                'type'       => 'INNER',
                'conditions' => [
                    'User.id = ConferenceQueue.user_id',
                ],
            ],
        ];
        $condition = 'ConferenceRoom.school_id = ' . $this->schoolId();
        if (!in_array(Auth::user()->role(), Constant::SUPER_ROLES)) {
            $condition .= ' AND ConferenceQueue.user_id = ' . Auth::id();
        }
        
        return Datatable::simple($this, $columns, $joins, $condition);
        
    }
    
    /**
     * 保存会议
     *
     * @param array $data
     * @return bool
     */
    function store(array $data) {
        
        return $this->create($data) ? true : false;
        
    }
    
    /**
     * 更新会议
     *
     * @param array $data
     * @param $id
     * @return bool
     * @throws Exception
     */
    function modify(array $data, $id) {
        
        return $this->find($id)->update($data);
        
    }
    
    /**
     * 删除会议
     *
     * @param null $id
     * @return bool
     * @throws Throwable
     */
    function remove($id = null) {
        
        try {
            DB::transaction(function () use ($id) {
                $this->purge(
                    [class_basename($this), 'ConferenceParticipant'],
                    'conference_queue_id', 'purge', $id
                );
            });
        } catch (Exception $e) {
            throw $e;
        }
        
        return true;
        
    }
    
    /** Helper functions -------------------------------------------------------------------------------------------- */
    
    function compose() {
    
        $action = explode('/', Request::path())[1];
        if ($action == 'index') {
            $data = [
                'titles' => ['#', '名称', '发起人', '会议室', '开始时间', '结束时间', '备注', '状态 . 操作'],
            ];
        } else {
            $schoolId = $this->schoolId();
            $conferenceRooms = ConferenceRoom::whereSchoolId($schoolId)->pluck('name', 'id');
            $user = Auth::user();
            if (in_array($user->role(), Constant::SUPER_ROLES)) {
                $educators = Educator::whereSchoolId($schoolId)->with('user')->pluck('user.realname', 'id');
            } else {
                $userIds = array_unique(
                    DepartmentUser::whereIn(
                        'department_id', $user->departmentIds($user->id)
                    )->get(['user_id'])->toArray()
                );
                $educators = collect([]);
                foreach ($userIds as $userId) {
                    $u = User::find($userId);
                    if ($u->educator) {
                        $educators[$u->educator->id] = $u->realname;
                    }
                }
            }
            $cq = ConferenceQueue::find(Request::route('id'));
            $data = [
                'conferenceRooms'   => $conferenceRooms,
                'educators'         => $educators,
                'selectedEducators' => collect(
                    explode(',', $cq ? $cq->educator_ids : null)
                )
            ];
        }
        
        return $data;
        
    }
    
}
