<?php
namespace App\Models;

use App\Facades\Datatable;
use App\Helpers\{Constant, ModelTrait};
use Carbon\Carbon;
use Eloquent;
use Exception;
use Html;
use Illuminate\Database\Eloquent\{Builder, Model, Relations\BelongsTo, Relations\HasMany};
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\{Auth, DB, Request};
use Throwable;

/**
 * App\Models\Conference 会议队列
 *
 * @property int $id
 * @property int $user_id 发起人用户id
 * @property int $room_id 房间id
 * @property int $message_id 消息id
 * @property string $url 扫码签到用二维码URL
 * @property string $name 会议名称
 * @property string $start 开始时间
 * @property string $end 结束时间
 * @property string $remark 备注
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int $status 会议状态
 * @property-read Message $message
 * @property-read Collection|Participant[] $participants
 * @property-read int|null $participants_count
 * @property-read Room $room
 * @property-read Educator $user
 * @method static Builder|Conference newModelQuery()
 * @method static Builder|Conference newQuery()
 * @method static Builder|Conference query()
 * @method static Builder|Conference whereCreatedAt($value)
 * @method static Builder|Conference whereEnd($value)
 * @method static Builder|Conference whereId($value)
 * @method static Builder|Conference whereMessageId($value)
 * @method static Builder|Conference whereName($value)
 * @method static Builder|Conference whereRemark($value)
 * @method static Builder|Conference whereRoomId($value)
 * @method static Builder|Conference whereStart($value)
 * @method static Builder|Conference whereStatus($value)
 * @method static Builder|Conference whereUpdatedAt($value)
 * @method static Builder|Conference whereUrl($value)
 * @method static Builder|Conference whereUserId($value)
 * @mixin Eloquent
 */
class Conference extends Model {
    
    use ModelTrait;
    
    protected $fillable = [
        'user_id', 'room_id', 'message_id', 'url',
        'name', 'start', 'end', 'remark', 'status'
    ];
    
    /** Properties -------------------------------------------------------------------------------------------------- */
    /** @return BelongsTo */
    function user() { return $this->belongsTo('App\Models\User'); }
    
    /** @return BelongsTo */
    function room() { return $this->belongsTo('App\Models\Room'); }
    
    /** @return BelongsTo */
    function message() { return $this->belongsTo('App\Models\Message'); }
    
    /** @return HasMany */
    function participants() { return $this->hasMany('App\Models\Participant'); }
    
    /** crud -------------------------------------------------------------------------------------------------------- */
    /** @return array */
    function index() {
        
        $columns = [
            ['db' => 'Conference.id', 'dt' => 0],
            ['db' => 'Conference.name', 'dt' => 1],
            ['db' => 'User.realname', 'dt' => 2],
            ['db' => 'Room.name as rname', 'dt' => 3],
            ['db' => 'Conference.start', 'dt' => 4],
            ['db' => 'Conference.end', 'dt' => 5],
            ['db' => 'Conference.remark', 'dt' => 6],
            [
                'db'        => 'Conference.status', 'dt' => 7,
                'formatter' => function ($d, $row) {
                    $statuses = [
                        ['green', '进行中'],
                        ['red', '已结束'],
                        ['gray', '已取消'],
                    ];
                    [$color, $title] = $statuses[$d];
                    $status = Html::tag('i', '', [
                        'class' => 'fa fa-circle text-' . $color,
                        'title' => $title
                    ])->toHtml();
                    
                    return Datatable::status($status, $row);
                },
            ],
        ];
        $joins = [
            [
                'table'      => 'rooms',
                'alias'      => 'Room',
                'type'       => 'INNER',
                'conditions' => [
                    'Room.id = Conference.room_id',
                ],
            ],
            [
                'table'      => 'educators',
                'alias'      => 'Educator',
                'type'       => 'INNER',
                'conditions' => [
                    'Educator.id = Conference.educator_id',
                ],
            ],
            [
                'table' => 'buildings',
                'alias' => 'Building',
                'type' => 'INNER',
                'conditions' => [
                    'Building.id = Room.building_id'
                ]
            ]
        ];
        $condition = 'Building.school_id = ' . $this->schoolId();
        $user = Auth::user();
        if (!in_array($user->role(), Constant::SUPER_ROLES)) {
            $condition .= ' AND Conference.educator_id = ' . $user->educator->id;
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
     * @throws Throwable
     */
    function modify(array $data, $id) {
        
        try {
            DB::transaction(function () use ($data, $id) {
                throw_if(
                    $conference = $this->find($id),
                    new Exception(__('messages.not_found'))
                );
                $conference->update($data);
            });
        } catch (Exception $e) {
            throw $e;
        }
        
        return true;
        
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
                    [class_basename($this), 'Participant'],
                    'conference_id', 'purge', $id
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
            $conference = Conference::find(Request::route('id'));
            $data = [
                'rooms'   => (new Room)->rooms('会议'),
                'educators'         => $educators,
                'selectedEducators' => collect(
                    explode(',', $conference ? $conference->educator_ids : null)
                )
            ];
        }
        
        return $data;
        
    }
    
}
