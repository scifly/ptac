<?php

namespace App\Models;

use App\Events\UserCreated;
use App\Events\UserDeleted;
use App\Events\UserUpdated;
use App\Facades\DatatableFacade as Datatable;
use Carbon\Carbon;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Notifications\DatabaseNotificationCollection;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;

/**
 * App\User 用户
 *
 * @mixin Eloquent
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
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int $enabled
 * @property string $userid 成员userid
 * @property string|null $english_name 英文名
 * @property string $department_ids 用户所属部门IDs
 * @property int|null $isleader 上级字段，标识是否为上级。第三方暂不支持
 * @property string|null $position 职位信息
 * @property string|null $telephone 座机号码
 * @property int|null $order 部门内的排序值，默认为0。数量必须和department一致，数值越大排序越前面
 * @property string|null $mobile 手机号码
 * @property string|null $avatar_mediaid 成员头像的mediaid，通过多媒体接口上传图片获得的mediaid
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
 * @property-read Custodian $custodian
 * @property-read Educator $educator
 * @property-read Student $student
 * @property-read Group $group
 * @property-read Operator $operator
 * @property-read PollQuestionnaireAnswer $pollquestionnaireAnswer
 * @property-read PollQuestionnaireParticipant $pollquestionnairePartcipant
 * @property-read PollQuestionnaire $pollquestionnaires
 * @property-read Collection|Message[] $messages
 * @property-read Collection|PollQuestionnaireAnswer[] $pollQuestionnaireAnswers
 * @property-read Collection|PollQuestionnaireParticipant[] $pollQuestionnairePartcipants
 * @property-read Collection|PollQuestionnaire[] $pollQuestionnaires
 * @property-read Collection|Department[] $departments
 * @property-read Collection|Mobile[] $mobiles
 * @property-read Collection|Order[] $orders
 * @property-read DatabaseNotificationCollection|DatabaseNotification[] $notifications
 */
class User extends Authenticatable {

    use Notifiable;
    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'group_id', 'username', 'password',
        'email', 'realname', 'gender', 'avatar_url',
        'wechatid', 'userid', 'english_name',
        'isleader', 'position',
        'telephone', 'order', 'mobile',
        'avatar_mediaid', 'enabled',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */

    protected $hidden = ['password', 'remember_token'];

    /**
     * 返回指定用户所属的角色对象
     *
     * @return BelongsTo
     */
    public function group() { return $this->belongsTo('App\Models\Group'); }

    /**
     * 获取指定用户对应的监护人对象
     *
     * @return HasOne
     */
    public function custodian() { return $this->hasOne('App\Models\Custodian'); }

    /**
     * 获取指定用户对应的教职员工对象
     *
     * @return HasOne
     */
    public function educator() { return $this->hasOne('App\Models\Educator'); }

    /**
     * 获取指定用户对应的学生对象
     *
     * @return HasOne
     */
    public function student() { return $this->hasOne('App\Models\Student'); }

    /**
     * 获取指定用户对应的管理/操作员对象
     *
     * @return HasOne
     */
    public function operator() { return $this->hasOne('App\Models\Operator'); }

    /**
     * 获取指定用户的所有订单对象
     *
     * @return HasMany
     */
    public function orders() { return $this->hasMany('App\Models\Order'); }

    /**
     * 获取指定用户的所有手机号码对象
     *
     * @return HasMany
     */
    public function mobiles() { return $this->hasMany('App\Models\Mobile'); }

    /**
     * 获取指定用户所属的所有部门对象
     *
     * @return BelongsToMany
     */
    public function departments() {
        
        return $this->belongsToMany('App\Models\Department', 'departments_users');
    
    }

    /**
     * 获取指定用户发起的所有调查问卷对象
     *
     * @return HasMany
     */
    public function pollQuestionnaires() {
        
        return $this->hasMany('App\Models\PollQuestionnaire');
    
    }

    /**
     * 获取指定用户参与的调查问卷所给出的答案对象
     *
     * @return HasMany
     */
    public function pollQuestionnaireAnswers() {
        
        return $this->hasMany('App\Models\PollQuestionnaireAnswer');
    
    }

    /**
     * 获取指定用户参与的所有调查问卷对象
     *
     * @return HasMany
     */
    public function pollQuestionnairePartcipants() {
        
        return $this->hasMany('App\Models\PollQuestionnaireParticipant');
    
    }

    /**
     * 获取指定用户发出的消息对象
     *
     * @return HasMany
     */
    public function messages() { return $this->hasMany('App\Models\Message'); }

    /**
     * 返回用户列表(id, name)
     *
     * @param array $userIds
     * @return array
     */
    static function users(array $userIds) {

        $users = [];
        foreach ($userIds as $id) {
            $user = self::find($id);
            $users[$user->id] = $user->realname;
        }

        return $users;

    }

    /**
     * 返回用户所属最顶级部门的ID
     *
     * @return mixed
     */
    static function topDeptId() {
        
        $departmentIds = Auth::user()->departments
            ->pluck('id')
            ->toArray();
        $levels = [];
        foreach ($departmentIds as $id) {
            $level = 0;
            $levels[$id] = Department::level($id, $level);
        }
        asort($levels);
        reset($levels);

        return key($levels) ?? 1;

    }

    /**
     * 学校以下的部门获取所属学校部门id
     *
     * @param $deptId
     * @return int|mixed
     */
    static function getDeptSchoolId(&$deptId) {

        $dept = Department::find($deptId);
        $typeId = DepartmentType::where('name', '学校')->first()->id;
        if ($dept->department_type_id != $typeId) {
            $deptId = $dept->parent_id;
            return self::getDeptSchoolId($deptId);
        }else{
            return $deptId;
        }
    }
    /**
     * 创建企业号会员
     *
     * @param $id
     */
    static function createWechatUser($id) {

        $user = self::find($id);
        $mobile = Mobile::whereUserId($id)->where('isdefault', 1)->first()->mobile;
        $data = [
            'userid' => $user->userid,
            'name' => $user->realname,
            'mobile' => $mobile,
            'department' => $user->departments->pluck('id')->toArray(),
            'gender' => $user->gender,
            'enable' => $user->enabled,
        ];
        event(new UserCreated($data));

    }

    static function importData(&$data) {
        
        $u = self::create($data);
        $data['id'] = $u->id;
        
    }

    /**
     * 更新企业号会员
     *
     * @param $id
     */
    static function updateWechatUser($id) {

        $user = self::find($id);
        $mobile = Mobile::whereUserId($id)->where('isdefault', 1)->first()->mobile;
        $data = [
            'userid' => $user->userid,
            'name' => $user->realname,
            'english_name' => $user->english_name,
            'mobile' => $mobile,
            'department' => $user->departments->pluck('id')->toArray(),
            'gender' => $user->gender,
            'enable' => $user->enabled,
        ];
        event(new UserUpdated($data));

    }

    /**
     * 删除企业号会员
     *
     * @param $id
     */
    static function deleteWechatUser($id) {

        event(new UserDeleted(self::find($id)->userid));

    }
    
    /**
     * 用户列表
     *
     * @return array
     */
    static function datatable() {

        $columns = [
            ['db' => 'User.id', 'dt' => 0],
            ['db' => 'User.username', 'dt' => 2],
            ['db' => 'Groups.name as grapname', 'dt' => 1],
            ['db' => 'User.avatar_url', 'dt' => 3],
            ['db' => 'User.realname', 'dt' => 4],
            [
                'db' => 'User.gender', 'dt' => 5,
                'formatter' => function ($d) {
                    return $d ? '男' : '女';
                },
            ],
            ['db' => 'User.email', 'dt' => 6],
            ['db' => 'User.created_at', 'dt' => 7],
            ['db' => 'User.updated_at', 'dt' => 8],
            [
                'db' => 'User.enabled', 'dt' => 9,
                'formatter' => function ($d, $row) {
                    return Datatable::dtOps($d, $row);
                },
            ],
        ];
        $joins = [
            [
                'table' => 'groups',
                'alias' => 'Groups',
                'type' => 'INNER',
                'conditions' => [
                    'Groups.id = User.group_id',
                ],
            ],
        ];

        return Datatable::simple(self::getModel(), $columns, $joins);

    }

}
