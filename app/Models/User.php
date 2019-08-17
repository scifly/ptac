<?php
namespace App\Models;

use App\Facades\Datatable;
use App\Helpers\{Constant, HttpStatusCode, ModelTrait, Sms, Snippet};
use App\Jobs\SyncMember;
use Carbon\Carbon;
use Eloquent;
use Exception;
use Illuminate\Contracts\View\Factory;
use Illuminate\Database\Eloquent\{Builder,
    Collection,
    Relations\BelongsTo,
    Relations\BelongsToMany,
    Relations\HasMany,
    Relations\HasOne};
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Http\JsonResponse;
use Illuminate\Notifications\{DatabaseNotification, DatabaseNotificationCollection, Notifiable};
use Illuminate\Support\Facades\{Auth, DB, Hash, Request};
use Illuminate\View\View;
use Laravel\Passport\{Client, HasApiTokens, Token};
use Throwable;

/**
 * App\Models\User 用户
 *
 * @property int $id
 * @property int $group_id 所属角色/权限ID
 * @property int|null $card_id
 * @property int $face_id 人脸id
 * @property string $username 用户名
 * @property string|null $remember_token "记住我"令牌，登录时用
 * @property string $password 密码
 * @property string|null $email 电子邮箱
 * @property int $gender 性别
 * @property string $realname 真实姓名
 * @property string $avatar_url 头像URL
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int $enabled
 * @property int $synced 是否已同步到企业号
 * @property string $userid 成员userid
 * @property string|null $english_name 英文名
 * @property int|null $isleader 上级字段，标识是否为上级。第三方暂不支持
 * @property string|null $position 职位信息
 * @property string|null $telephone 座机号码
 * @property int|null $order 部门内的排序值，默认为0。数量必须和department一致，数值越大排序越前面
 * @property int $subscribed 是否关注企业微信
 * @property-read Custodian $custodian
 * @property-read Collection|Department[] $departments
 * @property-read Educator $educator
 * @property-read Group $group
 * @property-read Card $card
 * @property-read Face $face
 * @property-read Collection|Message[] $messages
 * @property-read Collection|Mobile[] $mobiles
 * @property-read DatabaseNotificationCollection|DatabaseNotification[] $notifications
 * @property-read Collection|Order[] $orders
 * @property-read Collection|PollQuestionnaireAnswer[] $pollQuestionnaireAnswers
 * @property-read Collection|PollQuestionnaireParticipant[] $pollQuestionnairePartcipants
 * @property-read Collection|PollQuestionnaire[] $pollQuestionnaires
 * @property-read Student $student
 * @property-read Collection|Client[] $clients
 * @property-read Collection|Token[] $tokens
 * @property-read Collection|Event[] $events
 * @property-read Collection|PollQuestionnaireAnswer[] $pqAnswers
 * @property-read Collection|PollQuestionnaireParticipant[] $pqParticipants
 * @property-read Collection|Tag[] $tags
 * @property-read Collection|Tag[] $_tags
 * @method static Builder|User whereAvatarMediaid($value)
 * @method static Builder|User whereAvatarUrl($value)
 * @method static Builder|User whereCardId($value)
 * @method static Builder|User whereFaceId($value)
 * @method static Builder|User whereCreatedAt($value)
 * @method static Builder|User whereEmail($value)
 * @method static Builder|User whereEnabled($value)
 * @method static Builder|User whereEnglishName($value)
 * @method static Builder|User whereGender($value)
 * @method static Builder|User whereGroupId($value)
 * @method static Builder|User whereId($value)
 * @method static Builder|User whereIsleader($value)
 * @method static Builder|User whereOrder($value)
 * @method static Builder|User wherePassword($value)
 * @method static Builder|User wherePosition($value)
 * @method static Builder|User whereRealname($value)
 * @method static Builder|User whereRememberToken($value)
 * @method static Builder|User whereTelephone($value)
 * @method static Builder|User whereUpdatedAt($value)
 * @method static Builder|User whereUserid($value)
 * @method static Builder|User whereSubscribed($value)
 * @method static Builder|User whereSynced($value)
 * @method static Builder|User whereUsername($value)
 * @method static Builder|User newModelQuery()
 * @method static Builder|User newQuery()
 * @method static Builder|User query()
 * @mixin Eloquent
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Openid[] $openids
 */
class User extends Authenticatable {
    
    use HasApiTokens, Notifiable, ModelTrait;
    
    const SELECT_HTML = '<select class="form-control select2" style="width: 100%;" id="ID" name="ID">';
    
    protected $table = 'users';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'username', 'group_id', 'password',
        'realname', 'gender', 'userid',
        'position', 'enabled', 'email',
        'card_id', 'avatar_url', 'english_name',
        'isleader', 'telephone', 'order',
        'synced', 'subscribed', 'face_id',
    ];
    
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = ['password', 'remember_token'];
    
    /** properties -------------------------------------------------------------------------------------------------- */
    /**
     * 返回指定用户所属的角色对象
     *
     * @return BelongsTo
     */
    function group() { return $this->belongsTo('App\Models\Group'); }
    
    /**
     * 获取指定用户对应的监护人对象
     *
     * @return HasOne
     */
    function custodian() { return $this->hasOne('App\Models\Custodian'); }
    
    /**
     * 获取指定用户对应的教职员工对象
     *
     * @return HasOne
     */
    function educator() { return $this->hasOne('App\Models\Educator'); }
    
    /**
     * 获取指定用户对应的学生对象
     *
     * @return HasOne
     */
    function student() { return $this->hasOne('App\Models\Student'); }
    
    /**
     * 获取指定用户对应的一卡通对象
     *
     * @return HasOne
     */
    function card() { return $this->hasOne('App\Models\Card'); }
    
    /**
     * 获取指定用户对应的人脸对象
     *
     * @return HasOne
     */
    function face() { return $this->hasOne('App\Models\Face'); }
    
    /**
     * 获取指定用户的所有订单对象
     *
     * @return HasMany
     */
    function orders() { return $this->hasMany('App\Models\Order'); }
    
    /**
     * 获取指定用户的所有手机号码对象
     *
     * @return HasMany
     */
    function mobiles() { return $this->hasMany('App\Models\Mobile'); }
    
    /**
     * 获取指定用户所属的所有部门对象
     *
     * @return BelongsToMany
     */
    function departments() { return $this->belongsToMany('App\Models\Department', 'department_user'); }
    
    /**
     * 获取指定用户创建的所有标签对象
     *
     * @return HasMany
     */
    function _tags() { return $this->hasMany('App\Models\Tag'); }
    
    /**
     * 获取指定用户所属的所有标签对象
     *
     * @return BelongsToMany
     */
    function tags() { return $this->belongsToMany('App\Models\Tag', 'tag_user'); }
    
    /**
     * 获取指定用户创建的所有日历对象
     *
     * @return HasMany
     */
    function events() { return $this->hasMany('App\Models\Event'); }
    
    /**
     * 返回指定用户对应的所有openid
     *
     * @return HasMany
     */
    function openids() { return $this->hasMany('App\Models\Openid'); }
    
    /**
     * 获取指定用户发起的所有调查问卷对象
     *
     * @return HasMany
     */
    function pollQuestionnaires() { return $this->hasMany('App\Models\PollQuestionnaire'); }
    
    /**
     * 获取指定用户参与的调查问卷所给出的答案对象
     *
     * @return HasMany
     */
    function pqAnswers() { return $this->hasMany('App\Models\PollQuestionnaireAnswer'); }
    
    /**
     * 获取指定用户参与的所有调查问卷对象
     *
     * @return HasMany
     */
    function pqParticipants() { return $this->hasMany('App\Models\PollQuestionnaireParticipant'); }
    
    /** crud -------------------------------------------------------------------------------------------------------- */
    /**
     * (超级)用户列表
     *
     * @return array
     */
    function index() {
        
        $columns = [
            ['db' => 'User.id', 'dt' => 0],
            ['db' => 'User.username', 'dt' => 1],
            [
                'db'        => 'Groups.name as role', 'dt' => 2,
                'formatter' => function ($d, $row) {
                    return Snippet::icon($d, $row['remark']);
                },
            ],
            ['db' => 'User.realname', 'dt' => 3],
            [
                'db'        => 'User.avatar_url', 'dt' => 4,
                'formatter' => function ($d) {
                    return Snippet::avatar($d);
                },
            ],
            [
                'db'        => 'User.gender', 'dt' => 5,
                'formatter' => function ($d) {
                    return Snippet::gender($d);
                },
            ],
            ['db' => 'User.email', 'dt' => 6],
            ['db' => 'User.created_at', 'dt' => 7],
            ['db' => 'User.updated_at', 'dt' => 8],
            // [
            //     'db'        => 'User.synced', 'dt' => 9,
            //     'formatter' => function ($d) {
            //         return $this->synced($d);
            //     },
            // ],
            // [
            //     'db'        => 'User.subscribed', 'dt' => 10,
            //     'formatter' => function ($d) {
            //         return $this->subscribed($d);
            //     },
            // ],
            [
                'db'        => 'User.enabled', 'dt' => 9,
                'formatter' => function ($d, $row) {
                    return Datatable::status($d, $row, false);
                },
            ],
            ['db' => 'Groups.remark as remark', 'dt' => 10],
        ];
        $joins = [
            [
                'table'      => 'groups',
                'alias'      => 'Groups',
                'type'       => 'INNER',
                'conditions' => [
                    'Groups.id = User.group_id',
                ],
            ],
        ];
        $sql = 'User.group_id IN (%s)';
        [$rootGId, $corpGId, $schoolGId] = array_map(
            function ($name) { return Group::whereName($name)->first()->id; },
            ['运营', '企业', '学校']
        );
        $rootMenu = Menu::find((new Menu)->rootId(true));
        switch ($rootMenu->menuType->name) {
            case '根':
                $condition = sprintf($sql, join(',', [$rootGId, $corpGId, $schoolGId]));
                break;
            case '企业':
                $corp = Corp::whereMenuId($rootMenu->id)->first();
                $userIds = Department::find($corp->department_id)
                    ->users->pluck('id')->toArray();
                foreach ($corp->schools as $school) {
                    $userIds = array_merge(
                        $userIds,
                        Department::find($school->department_id)->users->pluck('id')->toArray()
                    );
                }
                !empty($userIds) ?: $userIds = [0];
                $condition = sprintf($sql, join(',', [$corpGId, $schoolGId])) .
                    ' AND User.id IN (' . join(',', array_unique($userIds)) . ')';
                break;
            case '学校':
                $userIds = Department::find(School::whereMenuId($rootMenu->id)->first()->department_id)
                    ->users->pluck('id')->toArray();
                !empty($userIds) ?: $userIds = [0];
                $condition = sprintf($sql, join(',', [$schoolGId])) .
                    ' AND User.id IN (' . join(',', $userIds) . ')';
                break;
            default:
                break;
        }
        
        return Datatable::simple(
            $this, $columns, $joins, $condition ?? ''
        );
        
    }
    
    /**
     * 合作伙伴列表
     *
     * @return array
     */
    function partners() {
        
        $columns = [
            ['db' => 'User.id', 'dt' => 0],
            ['db' => 'User.realname', 'dt' => 1],
            [
                'db'        => 'School.name', 'dt' => 2,
                'formatter' => function ($d) {
                    return sprintf(Snippet::ICON, 'fa-university text-purple', '') .
                        '<span class="text-purple">' . $d . '</span>';
                },
            ],
            ['db' => 'User.username', 'dt' => 3],
            ['db' => 'User.english_name', 'dt' => 4],
            ['db' => 'User.telephone', 'dt' => 5],
            ['db' => 'User.email', 'dt' => 6],
            ['db' => 'User.created_at', 'dt' => 7],
            ['db' => 'User.updated_at', 'dt' => 8],
            [
                'db'        => 'User.enabled', 'dt' => 9,
                'formatter' => function ($d, $row) {
                    $rechargeLink = sprintf(
                        Snippet::DT_ANCHOR,
                        'recharge_' . $row['id'],
                        '短信充值 & 查询', 'fa-money'
                    );
                    
                    return Datatable::status($d, $row, false) .
                        (Auth::user()->can('act', self::uris()['recharge']) ? $rechargeLink : '');
                },
            ],
        ];
        $joins = [
            [
                'table'      => 'groups',
                'alias'      => 'Groups',
                'type'       => 'INNER',
                'conditions' => [
                    'Groups.id = User.group_id',
                ],
            ],
            [
                'table'      => 'educators',
                'alias'      => 'Educator',
                'type'       => 'INNER',
                'conditions' => [
                    'User.id = Educator.user_id',
                ],
            ],
            [
                'table'      => 'schools',
                'alias'      => 'School',
                'type'       => 'INNER',
                'conditions' => [
                    'School.id = Educator.school_id',
                ],
            ],
        ];
        $condition = 'Groups.name = \'api\'';
        
        return Datatable::simple(
            $this, $columns, $joins, $condition
        );
        
    }
    
    /**
     * 保存超级用户
     *
     * @param array $data
     * @return bool
     * @throws Exception
     * @throws Throwable
     */
    function store(array $data) {
        
        try {
            DB::transaction(function () use ($data) {
                if ($data['group_id'] != Group::whereName('api')->first()->id) {
                    # 创建超级用户(运营/企业/学校)
                    $data['user']['password'] = bcrypt($data['user']['password']);
                    $mobiles = $data['mobile'];
                    unset($data['user']['mobile']);
                    $user = $this->create($data['user']);
                    (new Card)->store($user);
                    # 如果角色为校级管理员，则同时创建教职员工记录
                    if (!in_array($this->role($user->id), Constant::NON_EDUCATOR)) {
                        $data['user_id'] = $user->id;
                        Educator::create($data);
                    }
                    (new Mobile)->store($mobiles, $user->id);
                    (new DepartmentUser)->storeByUserId($user->id, [$this->departmentId($data)]);
                    $group = Group::find($data['user']['group_id']);
                    $this->sync([
                        [$user->id, $group->name, 'create'],
                    ]);
                } else {
                    # 创建合作伙伴(api用户)
                    $partner = $this->create($data);
                    $data['user_id'] = $partner->id;
                    Educator::create($data);
                    MessageType::create([
                        'name'    => $partner->realname,
                        'user_id' => $partner->id,
                        'remark'  => $partner->realname . '接口消息',
                        'enabled' => 0 # 不会显示在消息中心“消息类型”下拉列表中
                    ]);
                }
            });
        } catch (Exception $e) {
            throw $e;
        }
        
        return true;
        
    }
    
    /**
     * 返回当前登录用户的角色名称
     *
     * @param null $id
     * @return string
     */
    function role($id = null) {
        
        $user = $this->find($id ?? Auth::id());
        $role = $user->group->name;
        $part = session('part');
        
        return !isset($user->educator, $part) ? $role
            : ($part == 'educator' ? $role : '监护人');
        
    }
    
    /**
     * 获取超级用户所处的部门id
     *
     * @param $data
     * @return int|mixed|null
     */
    private function departmentId($data) {
        
        switch (Group::find($data['user']['group_id'])->name) {
            case '运营':
                return Department::whereParentId(null)->first()->id;
            case '企业':
                return Corp::find($data['corp_id'])->department_id;
            case '学校':
                return School::find($data['school_id'])->department_id;
            default:
                return null;
        }
        
    }
    
    /**
     * 同步企业微信会员
     *
     * @param array $contacts
     * @param null $id - 接收广播的用户id
     * @return bool
     */
    function sync(array $contacts, $id = null) {
        
        foreach ($contacts as $contact) {
            [$userId, $role, $method] = $contact;
            if ($role == '学生') continue;
            $user = $this->find($userId);
            $params = [
                'userid'   => $user->userid,
                'username' => $user->username,
                'position' => $user->position ?? $role,
                'corpIds'  => $this->corpIds($userId),
            ];
            if ($method != 'delete') {
                $departments = in_array($role, ['运营', '企业']) ? [1]
                    : $user->departments->unique()->pluck('id')->toArray();
                $mobile = $user->mobiles->where('isdefault', 1)->first()->mobile;
                $params = array_merge($params, [
                    'name'         => $user->realname,
                    'english_name' => $user->english_name,
                    'mobile'       => $mobile,
                    'email'        => $user->email,
                    'department'   => $departments,
                    'gender'       => $user->gender,
                    'remark'       => '',
                    'enable'       => $user->enabled,
                ]);
                # 在创建会员时，默认情况下不向该会员发送邀请
                // $method != 'create' ?: $params = array_merge($params, ['to_invite' => false]);
            }
            $members[] = [$params, $method];
        }
        SyncMember::dispatch($members ?? [], $id ?? Auth::id());
        
        return true;
        
    }
    
    /** Helper functions -------------------------------------------------------------------------------------------- */
    /**
     * 返回指定用户所属的所有企业id
     *
     * @param $id
     * @return array
     */
    function corpIds($id) {
        
        $user = $this->find($id);
        $topDeptId = $this->topDeptId($user);
        
        return $this->role($id) == '运营'
            ? Corp::pluck('id')->toArray()
            : ($topDeptId != 1 ? [(new Department)->corpId($topDeptId)] : []);
        
    }
    
    /**
     * 更新超级用户
     *
     * @param array $data
     * @param $id
     * @return bool
     * @throws Throwable
     */
    function modify(array $data, $id = null) {
        
        try {
            DB::transaction(function () use ($data, $id) {
                if ($data['group_id'] != Group::whereName('api')->first()->id) {
                    # 更新超级用户(运营/企业/学校)
                    $ids = $id ? [$id] : array_values(Request::input('ids'));
                    if (!$id) {
                        !Request::has('action')
                            ?: $data = ['enabled' => Request::input('action') == 'enable' ? 1 : 0];
                        $this->whereIn('id', $ids)->update($data);
                    } else {
                        $user = $this->find($id);
                        $mobile = $data['mobile'];
                        if (isset($data['enabled'])) unset($data['mobile']);
                        $user->update($data);
                        (new Card)->store($user);
                        $role = isset($data['group_id']) ? Group::find($data['group_id'])->name : null;
                        if ($role && $role == '学校') {
                            abort_if(
                                $user->educator && $user->educator->school_id != $data['school_id'],
                                HttpStatusCode::NOT_ACCEPTABLE,
                                __('messages.educator.switch_school_not_allowed')
                            );
                            Educator::updateOrCreate(
                                ['user_id' => $id],
                                ['enabled' => $data['enabled']]
                            );
                        }
                        if (isset($data['enabled'])) {
                            (new Mobile)->store($mobile, $user->id);
                            (new DepartmentUser)->store($user->id, $this->departmentId($data));
                        } else {
                            Mobile::where(['user_id' => $user->id, 'isdefault' => 1])
                                ->update(['mobile' => $mobile]);
                        }
                    }
                    # 同步企业微信会员
                    $this->sync(
                        array_map(
                            function ($userId) {
                                return [$userId, $this->role($userId), 'update'];
                            }, $ids
                        )
                    );
                } else {
                    # 更新合作伙伴(api)
                    if ($id) {
                        $this->find($id)->update($data);
                        MessageType::whereUserId($id)->first()->update([
                            'name'    => $data['realname'],
                            'remark'  => $data['realname'] . '接口消息',
                            'enabled' => 0,
                        ]);
                    } else {
                        $this->batch($this);
                    }
                }
            });
        } catch (Exception $e) {
            throw $e;
        }
        
        return true;
        
    }
    
    /**
     * 重置密码
     *
     * @return bool
     */
    function reset() {
        
        $user = $this->find(Auth::id());
        abort_if(
            !Hash::check(Request::input('old_password'), $user->password),
            HttpStatusCode::BAD_REQUEST,
            __('messages.bad_request')
        );
        
        return $user->update([
            'password' => bcrypt(Request::input('password')),
        ]);
        
    }
    
    /**
     * 删除用户
     *
     * @param $id
     * @param bool $partner - 是否为“合作伙伴”类型用户
     * @return bool
     * @throws Throwable
     */
    function remove($id = null, $partner = false) {
        
        try {
            DB::transaction(function () use ($id, $partner) {
                $ids = $id ? [$id] : array_values(Request::input('ids'));
                if (!$partner) {
                    $this->sync(
                        array_map(
                            function ($id) {
                                return [$id, $this->role($id), 'delete'];
                            }, $ids
                        )
                    );
                    Request::replace(['ids' => $ids]);
                    $this->purge(
                        array_fill(0, 2, 'ProcedureStep'),
                        ['approver_user_ids', 'related_user_ids'], 'clear'
                    );
                    $this->purge(
                        array_fill(0, 2, 'ProcedureLog'),
                        ['initiator_user_id', 'operator_user_id'], 'reset'
                    );
                    $this->purge([
                        class_basename($this), 'DepartmentUser', 'TagUser', 'Card',
                        'Tag', 'Mobile', 'PollQuestionnaire', 'MessageReply', 'Face',
                        'PollQuestionnaireAnswer', 'PollQuestionnaireParticipant',
                    ], 'user_id');
                } else {
                    $mtIds = MessageType::whereIn('user_id', $ids)
                        ->pluck('id')->toArray();
                    Request::replace(['ids' => $mtIds]);
                    (new MessageType)->remove();
                    Request::replace(['ids' => $ids]);
                    $this->purge(['User'], 'id');
                }
            });
        } catch (Exception $e) {
            throw $e;
        }
        
        return true;
        
    }
    
    /**
     * 短信充值 & 查询
     *
     * @param $id
     * @param array $data
     * @return JsonResponse
     * @throws Throwable
     */
    function recharge($id, array $data) {
        
        return (new SmsCharge)->recharge(
            $this, $id, $data
        );
        
    }
    
    /**
     * 返回指定角色对应的企业/学校列表HTML
     * 或返回指定企业对应的学校列表HTML
     *
     * @return JsonResponse
     */
    function csList() {
        
        $field = Request::input('field');
        $value = Request::input('value');
        abort_if(
            !in_array($field, ['group_id', 'corp_id']),
            HttpStatusCode::NOT_ACCEPTABLE,
            __('messages.not_acceptable')
        );
        $result = ['statusCode' => HttpStatusCode::OK];
        # 获取企业和学校列表
        if ($field == 'group_id') {
            $result['corpList'] = $this->selectList(
                $corps = $this->corps(), 'corp_id'
            );
            Group::find($value)->name != '学校' ?: $corpId = array_key_first($corps);
        } else {
            $corpId = $value;
        }
        $condition = ['corp_id' => $corpId ?? 0, 'enabled' => 1];
        $schools = !($corpId ?? 0) ? collect([]) : School::where($condition)->pluck('name', 'id');
        $result['schoolList'] = $this->selectList($schools->toArray(), 'school_id');
        
        return response()->json($result);
        
    }
    
    /**
     * 返回指定用户直属的部门集合
     *
     * @param null $id
     * @param string $role
     * @return array
     */
    function deptIds($id = null, $role = '') {
        
        $id = $id ?? Auth::id();
        $user = $this->find($id);
        $role = !empty($role) ? $role : $this->role($id);
        
        return in_array($role, Constant::NON_EDUCATOR) && $role != '监护人'
            ? $user->departments->pluck('id')->toArray()
            : DepartmentUser::where([
                'user_id' => $user->id,
                'enabled' => $role == '监护人' ? 0 : 1,
            ])->pluck('department_id')->toArray();
        
    }
    
    /**
     * 返回create/edit view所需数据
     *
     * @return array
     */
    function compose() {
        
        /**
         * @param array $names
         * @return array
         */
        function groups(array $names) {
            return Group::whereIn('name', $names)->pluck('name', 'id')->toArray();
        }
        
        switch (Request::route()->uri) {
            case '/':
            case 'home':
            case 'pages/{id}':
            case 'users/edit':
                $user = Auth::user();
                
                return [
                    'mobile'   => $user->mobiles->isNotEmpty()
                        ? $user->mobiles->where('isdefault', 1)->first()->mobile
                        : '(n/a)',
                    'disabled' => true,
                ];
            case 'users/event':
                return [
                    'titles' => [
                        '#', '名称', '备注', '地点', '开始时间', '结束时间',
                        '公共事件', '课程', '提醒', '创建者', '更新于',
                    ],
                ];
            case 'users/message':
                [$optionAll, $htmlCommType, $htmlApp, $htmlMessageType] = $this->messageFilters();
                
                return [
                    'titles'    => [
                        '#',
                        ['title' => '通信方式', 'html' => $htmlCommType],
                        ['title' => '应用', 'html' => $htmlApp],
                        '消息批次', '发送者',
                        ['title' => '类型', 'html' => $htmlMessageType],
                        ['title' => '接收于', 'html' => $this->inputDateTimeRange('接收于')],
                        [
                            'title' => '状态',
                            'html'  => $this->singleSelectList(
                                array_merge($optionAll, [0 => '未读', 1 => '已读']), 'filter_read'
                            ),
                        ],
                    ],
                    'batch'     => true,
                    'removable' => true,
                    'filter'    => true,
                ];
            case 'users/reset':
                return ['disabled' => true];
            case 'operators/index':
                return [
                    'batch'  => true,
                    'titles' => [
                        '#', '用户名', '角色', '真实姓名', '头像', '性别',
                        '电子邮件', '创建于', '更新于', '状态 . 操作',
                    ],
                ];
            case 'operators/create':
            case 'operators/edit/{id}':
                $operator = $departmentId = $corps = $schools = null;
                $rootMenu = Menu::find((new Menu)->rootId(true));
                if (Request::route('id')) {
                    $operator = $this->find(Request::route('id'));
                    $departmentId = $this->topDeptId($operator);
                }
                switch ($rootMenu->menuType->name) {
                    case '根':
                        $groups = groups(['运营', '企业', '学校']);
                        if (Request::route('id')) {
                            $role = $operator->role($operator->id);
                            $role == '运营' ?: $corps = Corp::all()->pluck('name', 'id')->toArray();
                            $role != '学校' ?: $schools = School::whereCorpId($operator->educator->school->corp_id)
                                ->pluck('name', 'id')->toArray();
                        }
                        break;
                    case '企业':
                        $groups = groups(['企业', '学校']);
                        $corp = null;
                        if (Request::route('id')) {
                            switch ($operator->role($operator->id)) {
                                case '企业':
                                    $corp = Corp::whereDepartmentId($departmentId)->first();
                                    break;
                                case '学校':
                                    $corpId = head($this->corpIds($operator->id)); // $operator->educator->school->corp_id;
                                    $corp = Corp::find($corpId);
                                    $schools = School::whereCorpId($corpId)->pluck('name', 'id')->toArray();
                                    break;
                                default:
                                    break;
                            }
                        } else {
                            $corp = Corp::whereMenuId($rootMenu->id)->first();
                        }
                        $corps = [$corp->id => $corp->name];
                        break;
                    case '学校':
                        $groups = groups(['学校']);
                        $school = Request::route('id')
                            ? School::whereDepartmentId($departmentId)->first()
                            : School::whereMenuId($rootMenu->id)->first();
                        $corp = Corp::find($school->corp_id);
                        $corps = [$corp->id => $corp->name];
                        $schools = [$school->id => $school->name];
                        break;
                    default:
                        break;
                }
                
                return array_combine(
                    ['mobiles', 'groups', 'corps', 'schools'],
                    [
                        Request::route('id') ? $this->find(Request::route('id'))->mobiles : [],
                        $groups ?? [], $corps, $schools,
                    ]
                );
            case 'partners/index':
                return [
                    'batch'  => true,
                    'titles' => [
                        '#', '全称', '所属学校', '接口用户名', '接口密码',
                        '联系人', '电子邮箱', '创建于', '更新于', '状态',
                    ],
                ];
            case 'partners/create':    # api用户创建/编辑
            case 'partners/edit/{id?}':
                return ['schools' => School::whereCorpId((new Corp)->corpId())->pluck('name', 'id')->toArray()];
            default:                   # api用户短信充值/查询
                return (new Message)->compose('recharge');
        }
        
    }
    
    /**
     * @return array
     */
    private function corps() {
        
        switch (Auth::user()->role()) {
            case '运营':
                return Corp::whereEnabled(1)->pluck('name', 'id')->toArray();
            case '企业':
                $corp = Corp::whereDepartmentId($this->topDeptId())->first();
                
                return [$corp->id => $corp->name];
            default:
                return [];
        }
        
    }
    
    /**
     * 获取Select HTML
     *
     * @param array $items
     * @param $field
     * @return string
     */
    private function selectList(array $items, $field) {
        
        $html = str_replace('ID', $field, self::SELECT_HTML);
        foreach ($items as $key => $value) {
            $html .= '<option value="' . $key . '">' . $value . '</option>';
        }
        
        return $html . '</select>';
        
    }
    
    /**
     * 公众号用户注册
     *
     * @param $appId - 公众号应用id
     * @return Factory|JsonResponse|View
     * @throws Throwable
     */
    function signup($appId) {
        
        try {
            throw_if(
                !$app = App::find($appId),
                new Exception(__('messages.not_found'))
            );
            if (Request::method() == 'GET') {
                return view('wechat.wechat.signup', [
                    'openid' => Request::query('openid'),
                ]);
            }
            $mobile = Request::has('openid')
                ? session('mobile')
                : Request::input('mobile');
            throw_if(
                !$default = Mobile::where([
                    'mobile' => $mobile, 'isdefault' => 1,
                ])->first(),
                new Exception(__('messages.user.not_found'))
            );
            if (Request::has('openid')) {
                # 注册
                $vericode = Request::input('vericode');
                $code = session('code');
                $expiredAt = session('expiredAt');
                throw_if(
                    !isset($code, $expiredAt) ||
                    time() - $expiredAt > 30 * 60 ||
                    $vericode != $code ||
                    $mobile != Request::input('mobile'),
                    new Exception(__('messages.user.v_invalid'))
                );
                Openid::create([
                    'user_id' => $default->user_id,
                    'app_id'  => $appId,
                    'openid'  => Request::input('openid'),
                ]);
                
                return response()->json([
                    'message' => __('messages.user.registered'),
                    'url'     => $app->corp->acronym . '/wechat/' . $appId,
                ]);
            } else {
                # 发送短信验证码
                session([
                    'mobile'    => $default->mobile,
                    'code'      => $code = mt_rand(1000, 9999),
                    'expiredAt' => time() + 30 * 60,
                ]);
                $sent = (new Sms)->invoke(
                    'BatchSend2',
                    [$default->mobile, urlencode(
                        mb_convert_encoding(
                            __('messages.user.vericode') . $code,
                            'gb2312', 'utf-8'
                        )
                    ), '', '']
                );
                
                return response()->json([
                    'message' => __('messages.user.v_' . ($sent ? 'sent' : 'failed')),
                ]);
            }
        } catch (Exception $e) {
            throw $e;
        }
        
    }
    
}