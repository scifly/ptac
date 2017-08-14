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
 */
class Event extends Model {
    //
    protected $table = 'events';
    
    protected $fillable = [
        'name',
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
        'alert_mins',
        'user_id',
        'created_at',
        'updated_at',
        'enabled'
    ];
}
