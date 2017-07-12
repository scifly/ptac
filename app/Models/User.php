<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

/**
 * App\User
 * @mixin \Eloquent
 * @property int $id
 * @property int $group_id 所属角色/权限ID
 * @property string $username 用户名
 * @property string $remember_token “记住我”令牌，登录时用
 * @property string $password 密码
 * @property string $email 电子邮件地址
 * @property int $gender 性别
 * @property string $realname 真实姓名
 * @property string $avatar_url 头像URL
 * @property string $wechatid 微信号
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property int $enabled
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @property string $userid 成员userid
 * @property string|null $english_name 英文名
 * @property string $department_ids 用户所属部门IDs
 * @property int|null $isleader 上级字段，标识是否为上级。第三方暂不支持
 * @property string|null $position 职位信息
 * @property string|null $telephone 座机号码
 * @property int|null $order 部门内的排序值，默认为0。数量必须和department一致，数值越大排序越前面
 * @property string|null $mobile 手机号码
 * @property string|null $avatar_mediaid 成员头像的mediaid，通过多媒体接口上传图片获得的mediaid
 * @property-read \App\Models\Custodian $custodian
 * @property-read \App\Models\Educator $educator
 * @property-read \App\Models\Student $student
 * @method static Builder|User whereAvatarUrl($value)
 * @method static Builder|User whereCreatedAt($value)
 * @method static Builder|User whereEmail($value)
 * @method static Builder|User whereEnabled($value)
 * @method static Builder|User whereGender($value)
 * @method static Builder|User whereGroupId($value)
 * @method static Builder|User whereId($value)
 * @method static Builder|User wherePassword($value)
 * @method static Builder|User whereRealname($value)
 * @method static Builder|User whereRememberToken($value)
 * @method static Builder|User whereUpdatedAt($value)
 * @method static Builder|User whereUsername($value)
 * @method static Builder|User whereWechatid($value)
 * @method static Builder|User whereAvatarMediaid($value)
 * @method static Builder|User whereDepartmentIds($value)
 * @method static Builder|User whereEnglishName($value)
 * @method static Builder|User whereIsleader($value)
 * @method static Builder|User whereMobile($value)
 * @method static Builder|User whereOrder($value)
 * @method static Builder|User wherePosition($value)
 * @method static Builder|User whereTelephone($value)
 * @method static Builder|User whereUserid($value)
 */
class User extends Authenticatable {
    
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'username',
        'realname',
        'email',
        'gender',
        'avatar_url',
        'wechatid',
        'userid',
        'english_name',
        'department_ids',
        'isleader',
        'position',
        'telephone',
        'order',
        'mobile',
        'avatar_mediaid',
        'enabled',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];
    
    public function custodian() { return $this->hasOne('App\Models\Custodian'); }
    
    public function educator() { return $this->hasOne('App\Models\Educator'); }
    
    public function student() { return $this->hasOne('App\Models\Student'); }
    
    public function group() { return $this->belongsTo('App\Model\Group'); }
    
    
}
