<?php
namespace App\Models;

use App\Facades\Datatable;
use App\Helpers\ModelTrait;
use App\Helpers\Snippet;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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
 * @mixin \Eloquent
 * @property-read Collection|ConferenceParticipant[] $conferenceParticipants
 * @property-read ConferenceRoom $conferenceRoom
 * @property int $educator_id 发起人教职员工ID
 * @property-read \App\Models\User $user
 * @method static Builder|ConferenceQueue whereEducatorId($value)
 * @property int $status 会议状态
 * @method static Builder|ConferenceQueue whereStatus($value)
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
                    $user = Auth::user();
                    $id = $row['id'];
                    $statusHtml = '<i class="fa fa-circle text-%s" title="%s"></i>';
                    $status = '';
                    switch ($d) {
                        case 0:
                            $status = sprintf($statusHtml, 'green', '进行中');
                            break;
                        case 1:
                            $status = sprintf($statusHtml, 'red', '已结束');
                            break;
                        case 2:
                            $status = sprintf($statusHtml, 'gray', '已取消');
                            break;
                        default:
                            break;
                    }
                    $showLink = sprintf(Snippet::DT_LINK_SHOW, 'show_' . $id);
                    $editLink = sprintf(Snippet::DT_LINK_EDIT, 'edit_' . $id);
                    $delLink = sprintf(Snippet::DT_LINK_DEL, $id);
                    
                    return
                        $status .
                        ($user->can('act', self::uris()['show']) ? $showLink : '') .
                        ($user->can('act', self::uris()['edit']) ? $editLink : '') .
                        ($user->can('act', self::uris()['destroy']) ? $delLink : '');
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
        if (!in_array(Auth::user()->group->name, ['运营', '企业', '学校'])) {
            $condition .= ' AND ConferenceQueue.user_id = ' . Auth::id();
        }
        
        return Datatable::simple($this->getModel(), $columns, $joins, $condition);
        
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
     * @param $id
     * @return bool
     * @throws Exception
     */
    function remove($id = null) {
        
        return $this->del($this, $id);
        
    }
    
    /**
     * 删除指定会议的所有数据
     *
     * @param $id
     * @throws Exception
     */
    function purge($id) {
        
        try {
            DB::transaction(function () use ($id) {
                ConferenceParticipant::whereConferenceQueueId($id)->delete();
                $this->find($id)->delete();
            });
        } catch (Exception $e) {
            throw $e;
        }
        
    }
    
    /**
     * 删除指定教职员工的与会记录
     *
     * @param $educatorId
     * @return bool
     * @throws Exception
     */
    function removeEducator($educatorId) {
        
        try {
            DB::transaction(function () use ($educatorId) {
                $cqs = $this->whereRaw($educatorId . ' IN (educator_ids)')->get();
                foreach ($cqs as $cq) {
                    $educatorIds = array_diff(explode(',', $cq->educator_ids), [$educatorId]);
                    $cq->update(['educator_ids' => implode(',', $educatorIds)]);
                }
            });
        } catch (Exception $e) {
            throw $e;
        }
        
        return true;
        
    }
    
}
