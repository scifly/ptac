<?php
namespace App\Models;

use App\Facades\Datatable;
use App\Helpers\{Constant, Snippet};
use Carbon\Carbon;
use Eloquent;
use Exception;
use Illuminate\Database\Eloquent\{Builder, Model, Relations\BelongsTo, Relations\HasOne};
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Throwable;

/**
 * App\Models\Event 事件(日程)
 *
 * @property int $id
 * @property string $title 事件名称
 * @property string $remark 事件备注
 * @property string $location 时间相关地点
 * @property string $contact 事件联系人
 * @property string $url 事件URL
 * @property string $start 事件开始时间
 * @property string $end 事件结束时间
 * @property int $ispublic 事件是否公开
 * @property int $iscourse 是否为课程表事件，如果是，ispublic置1
 * @property int $educator_id 教职员工ID，如果是课程表事件的话
 * @property int $subject_id 科目ID，如果是课程表事件的话
 * @property int $alertable 是否提醒
 * @property int $alert_mins 提醒时间(分钟)
 * @property int $user_id 事件创建者用户ID
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int $enabled
 * @property-read Educator $educator
 * @property-read Subject $subject
 * @property-read User $user
 * @property-read Message $message
 * @method static Builder|Event whereAlertMins($value)
 * @method static Builder|Event whereAlertable($value)
 * @method static Builder|Event whereContact($value)
 * @method static Builder|Event whereCreatedAt($value)
 * @method static Builder|Event whereEducatorId($value)
 * @method static Builder|Event whereEnabled($value)
 * @method static Builder|Event whereEnd($value)
 * @method static Builder|Event whereId($value)
 * @method static Builder|Event whereIscourse($value)
 * @method static Builder|Event whereIspublic($value)
 * @method static Builder|Event whereLocation($value)
 * @method static Builder|Event whereRemark($value)
 * @method static Builder|Event whereStart($value)
 * @method static Builder|Event whereSubjectId($value)
 * @method static Builder|Event whereTitle($value)
 * @method static Builder|Event whereUpdatedAt($value)
 * @method static Builder|Event whereUrl($value)
 * @method static Builder|Event whereUserId($value)
 * @method static Builder|Event newModelQuery()
 * @method static Builder|Event newQuery()
 * @method static Builder|Event query()
 * @mixin Eloquent
 */
class Event extends Model {
    
    protected $table = 'events';
    
    protected $fillable = [
        'title', 'remark', 'location',
        'contact', 'url', 'start',
        'end', 'ispublic', 'iscourse',
        'educator_id', 'subject_id', 'alertable',
        'alert_mins', 'user_id', 'enabled',
    ];
    
    /**
     * 返回事件创建者的用户对象
     *
     * @return BelongsTo
     */
    function user() { return $this->belongsTo('App\Models\User'); }
    
    /**
     * 返回课程表事件对应的教职员工对象
     *
     * @return BelongsTo
     */
    function educator() { return $this->belongsTo('App\Models\Educator'); }
    
    /**
     * 返回课程表事件对应的科目对象
     *
     * @return BelongsTo
     */
    function subject() { return $this->belongsTo('App\Models\Subject'); }
    
    /**
     * 返回事件对应的消息对象
     *
     * @return HasOne
     */
    function message() { return $this->hasOne('App\Models\Message'); }
    
    /**
     * 待办事项列表
     *
     * @return array
     */
    function index() {
        
        $columns = [
            ['db' => 'Event.id', 'dt' => 0],
            ['db' => 'Event.title', 'dt' => 1],
            ['db' => 'Event.remark', 'dt' => 2],
            ['db' => 'Event.location', 'dt' => 3],
            ['db' => 'Event.start', 'dt' => 4],
            ['db' => 'Event.end', 'dt' => 5],
            [
                'db'        => 'Event.ispublic', 'dt' => 6,
                'formatter' => function ($d) {
                    if (!empty($d)) {
                        return sprintf(
                            Snippet::BADGE,
                            $d ? 'text-green' : 'text-red',
                            $d ? '是' : '否'
                        );
                    }
                    
                    return '-';
                },
            ],
            ['db' => 'Subject.name', 'dt' => 7],
            [
                'db'        => 'Event.alertable', 'dt' => 8,
                'formatter' => function ($d) {
                    return sprintf(
                        Snippet::BADGE,
                        $d ? 'text-green' : 'text-red',
                        $d ? '是' : '否'
                    );
                },
            ],
            ['db' => 'User.realname', 'dt' => 9],
            ['db' => 'Event.updated_at', 'dt' => 10],
        ];
        $joins = [
            [
                'table'      => 'users',
                'alias'      => 'User',
                'type'       => 'INNER',
                'conditions' => [
                    'User.id = Event.user_id',
                ],
            ],
            [
                'table'      => 'subjects',
                'alias'      => 'Subject',
                'type'       => 'INNER',
                'conditions' => [
                    'Subject.id = Event.subject_id',
                ],
            ],
        ];
        $condition = 'Event.enabled = 1';
        
        return Datatable::simple(
            $this, $columns, $joins, $condition
        );
        
    }
    
    /**
     * 保存事件
     *
     * @param array $data
     * @return bool
     */
    function store(array $data) {
        
        return $this->create($data) ? true : false;
        
    }
    
    /**
     * 更新事件
     *
     * @param array $data
     * @param $id
     * @return bool
     */
    function modify(array $data, $id) {
        
        return $this->find($id)->update($data);
        
    }
    
    /**
     * 删除事件
     *
     * @param $id
     * @return bool|null
     * @throws Exception
     */
    function remove($id) {
        
        return $this->find($id)->delete();
        
    }
    
    /**
     * 删除/更新指定用户创建的事件
     *
     * @param $userId
     * @return bool
     * @throws Throwable
     */
    function removeUser($userId) {
        
        try {
            DB::transaction(function () use ($userId) {
                # 删除用户创建的非课程&非公共事件
                $this->where([
                    'user_id'  => $userId,
                    'ispublic' => 0,
                    'iscourse' => 0,
                ])->delete();
                # 更新用户创建的课程&公共事件
                $this->where(['user_id' => $userId, 'ispublic' => 1])
                    ->orWhere('iscourse', 1)
                    ->update(['user_id' => 0]);
            });
        } catch (Exception $e) {
            throw $e;
        }
        
        return true;
        
    }
    
    /**
     * 显示日历事件
     * @param $userId
     * @return JsonResponse
     */
    function showCalendar($userId) {
        
        //通过userId找出educator_id
        $educator = Educator::whereUserId($userId)->first();
        //全部公共事件
        $pubEvents = self::whereIspublic(1)->get()->toArray();
        if (!empty($educator)) {
            //先选出公开事件中 非课程的事件
            $pubNoCourseEvents = $this
                ->where('ispublic', 1)
                ->where('iscourse', 0)
                ->get()->toArray();
            //选出公开事件中 课程事件
            $pubCourEvents = $this
                ->where('ispublic', 1)
                ->where('iscourse', 1)
                ->where('educator_id', $educator->id)
                ->get()->toArray();
            //再选个人未公开事件
            $perEvents = $this
                ->where('User_id', $userId)
                ->where('ispublic', 0)
                ->where('enabled', '1')
                ->get()->toArray();
        } else {
            return response()->json($pubEvents);
        }
        //如果是管理员
        if (in_array(User::find($userId)->role($userId), Constant::SUPER_ROLES)) {
            return response()->json(array_merge($pubEvents, $perEvents));
        }
        
        //如果是用户
        return response()->json(array_merge($pubNoCourseEvents, $perEvents, $pubCourEvents));
    }
    
    /**
     * 根据角色验证时间冲突
     *
     * @param $userId
     * @param $educator_id
     * @param $start
     * @param $end
     * @param $id
     * @return bool
     */
    function isValidateTime($userId, $educator_id, $start, $end, $id = null) {
        
        if (!in_array(User::find($userId)->role($userId), Constant::SUPER_ROLES)) {
            return $this->isRepeatTimeUser($userId, $start, $end, $id);
        } else {
            if ($educator_id != 0) {
                return $this->isRepeatTimeAdmin($educator_id, $start, $end, $id);
            }
        }
        
        return false;
    }
    
    /**
     * 验证用户添加事件是否有重复
     * @param $userId
     * @param $start
     * @param $end
     * @param null $id
     * @return bool
     */
    function isRepeatTimeUser($userId, $start, $end, $id = null) {
        
        //通过userId 找到educator_id
        $educator = Educator::whereUserId($userId)->first();
        //验证是否和课表时间有冲突
        $event = $this
            ->where('id', '<>', $id)
            ->where('educator_id', $educator->id)
            ->where('start', '<=', $start)
            ->where('end', '>', $start)
            ->first();
        if (empty($event)) {
            $event = $this
                ->where('id', '<>', $id)
                ->where('educator_id', $educator->id)
                ->where('start', '>=', $start)
                ->where('start', '<', $end)
                ->first();
        }
        //验证个人时间是否有冲突和其余除开课表的公共事件是否有冲突
        if (empty($event)) {
            $event = $this
                ->where('id', '<>', $id)
                ->where('user_id', $userId)
                ->where('start', '<=', $start)
                ->where('end', '>', $start)
                ->first();
        }
        if (empty($event)) {
            $event = $this
                ->where('id', '<>', $id)
                ->where('user_id', $userId)
                ->where('start', '>=', $start)
                ->where('start', '<', $end)
                ->first();
        }
        if (empty($event)) {
            $event = $this
                ->where('id', '<>', $id)
                ->where('ispublic', 1)
                ->where('iscourse', 0)
                ->where('start', '<=', $start)
                ->where('end', '>', $start)
                ->first();
        }
        if (empty($event)) {
            $event = $this
                ->where('id', '<>', $id)
                ->where('ispublic', 1)
                ->where('iscourse', 0)
                ->where('start', '>=', $start)
                ->where('start', '<', $end)
                ->first();
        }
        
        return !empty($event);
        
    }
    
    /**
     * 验证管理员添加事件是否有重复
     * 未判断管理员个人事件重复
     * @param $educatorId
     * @param $start
     * @param $end
     * @param null $id
     * @return bool
     */
    function isRepeatTimeAdmin($educatorId, $start, $end, $id = null) {
        
        $event = $this
            ->where('id', '<>', $id)
            ->where('educator_id', $educatorId)
            ->where('start', '<=', $start)
            ->where('end', '>', $start)
            ->first();
        if (empty($event)) {
            $event = $this
                ->where('id', '<>', $id)
                ->where('educator_id', $educatorId)
                ->where('start', '>=', $start)
                ->where('start', '<', $end)
                ->first();
        }
        if (empty($event)) {
            $event = $this
                ->where('id', '<>', $id)
                ->where('ispublic', 1)
                ->where('iscourse', 0)
                ->where('start', '<=', $start)
                ->where('end', '>', $start)
                ->first();
        }
        if (empty($event)) {
            $event = $this
                ->where('id', '<>', $id)
                ->where('ispublic', 1)
                ->where('iscourse', 0)
                ->where('start', '>=', $start)
                ->where('start', '<', $start)
                ->first();
        }
        
        return !empty($event);
        
    }
    
    /**
     * 计算拖动后的时间差
     * @param $day
     * @param $hour
     * @param $minute
     * @return int
     */
    function timeDiff($day, $hour, $minute) {
        
        $days = $day * 24 * 60 * 60;
        $hours = $hour * 60 * 60;
        $minutes = $minute * 60;
        
        return $diffTime = $days + $hours + $minutes;
        
    }
    
    /**
     * 删除与指定教职员工关联的所有事件
     *
     * @param $educatorId
     * @throws Exception
     */
    function removeEducator($educatorId) {
        
        $this->whereEducatorId($educatorId)->delete();
        
    }
    
}
