<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Event
 *
 * @property int $id
 * @property string $name 事件名称
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
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property int $enabled
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
 * @method static Builder|Event whereName($value)
 * @method static Builder|Event whereRemark($value)
 * @method static Builder|Event whereStart($value)
 * @method static Builder|Event whereSubjectId($value)
 * @method static Builder|Event whereUpdatedAt($value)
 * @method static Builder|Event whereUrl($value)
 * @method static Builder|Event whereUserId($value)
 * @mixin \Eloquent
 * @property-read \App\Models\Educator $educator
 * @property-read \App\Models\Subject $subject
 * @property-read \App\Models\User $user
 * @property string $title 事件名称
 * @method static Builder|Event whereTitle($value)
 */
class Event extends Model {
    
    protected $table = 'events';
    protected $fillable = [
        'title',
        'remark',
        'location',
        'contact',
        'url',
        'start',
        'end',
        'ispublic',
        'iscourse',
        'educator_id',
        'subject_id',
        'alertable',
        'alert_mins',
        'user_id',
        'created_at',
        'updated_at',
        'enabled',
    ];
    
    /**
     * 返回事件创建者的用户对象
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user() { return $this->belongsTo('App\Models\User'); }
    
    /**
     * 返回课程表事件对应的教职员工对象
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function educator() { return $this->belongsTo('App\Models\Educator'); }
    
    /**
     * 返回课程表事件对应的科目对象
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function subject() { return $this->belongsTo('App\Models\Subject'); }
    
    /**
     * 显示日历事件
     * @param $userId
     * @return \Illuminate\Http\JsonResponse
     */
    public function showCalendar($userId) {
        //通过userId找出educator_id
        $educator = Educator::where('user_id', $userId)->first();
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
        //全部公共事件
        $pubEvents = $this->where('ispublic', 1)->get()->toArray();
        //如果是管理员
        if ($this->getRole($userId)) {
            return response()->json(array_merge($pubEvents, $perEvents));
        }
        
        //如果是用户
        return response()->json(array_merge($pubNoCourseEvents, $perEvents, $pubCourEvents));
    }
    
    /**
     * 判断当前用户权限
     * @param $userId
     * @return bool
     */
    public function getRole($userId) {
        $role = User::find($userId)->group;
        
        return $role->name == '管理员' ? true : false;
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
    public function isValidateTime($userId, $educator_id, $start, $end, $id = null) {
        if (!$this->getRole($userId)) {
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
    public function isRepeatTimeUser($userId, $start, $end, $id = null) {
        //通过userId 找到educator_id
        $educator = Educator::where('user_id', $userId)->first();
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
    public function isRepeatTimeAdmin($educatorId, $start, $end, $id = null) {
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
    public function timeDiff($day, $hour, $minute) {
        $days = $day * 24 * 60 * 60;
        $hours = $hour * 60 * 60;
        $minutes = $minute * 60;
        
        return $diffTime = $days + $hours + $minutes;
    }
    
}
