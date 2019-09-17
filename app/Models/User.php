<?php
namespace App\Models;

use App\Facades\Datatable;
use App\Helpers\{Constant, HttpStatusCode, ModelTrait, Sms};
use App\Jobs\SyncMember;
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
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Notifications\DatabaseNotificationCollection;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\{Auth, DB, Hash, Request};
use Illuminate\View\View;
use Laravel\Passport\Client;
use Laravel\Passport\HasApiTokens;
use Laravel\Passport\Token;
use Throwable;

/**
 * App\Models\User 用户
 *
 * @property int $id
 * @property int $group_id 所属角色/权限ID
 * @property string $username 用户名
 * @property string $password 密码
 * @property string $realname 真实姓名
 * @property int $gender 性别
 * @property string|null $email 电子邮箱
 * @property string|null $mobile 手机号码
 * @property int|null $face_id 人脸识别id
 * @property int|null $card_id 一卡通id
 * @property string|null $avatar_url 头像URL
 * @property mixed|null $ent_attrs 企业微信相关属性
 * @property mixed|null $api_attrs api相关属性
 * @property string|null $remember_token "记住我"令牌，登录时用
 * @property Carbon|null $created_at 创建于
 * @property Carbon|null $updated_at 更新于
 * @property int $enabled 状态：1 - 启用，0 - 禁用
 * @property-read Collection|Tag[] $_tags
 * @property-read int|null $_tags_count
 * @property-read Card $card
 * @property-read Collection|Client[] $clients
 * @property-read int|null $clients_count
 * @property-read Custodian $custodian
 * @property-read Collection|Department[] $departments
 * @property-read int|null $departments_count
 * @property-read Educator $educator
 * @property-read Collection|Event[] $events
 * @property-read int|null $events_count
 * @property-read Face $face
 * @property-read Group $group
 * @property-read DatabaseNotificationCollection|DatabaseNotification[] $notifications
 * @property-read int|null $notifications_count
 * @property-read Collection|Openid[] $openids
 * @property-read int|null $openids_count
 * @property-read Collection|Order[] $orders
 * @property-read int|null $orders_count
 * @property-read Collection|PollReply[] $pollReplies
 * @property-read int|null $poll_replies_count
 * @property-read Collection|Poll[] $polls
 * @property-read int|null $polls_count
 * @property-read Student $student
 * @property-read Collection|Tag[] $tags
 * @property-read int|null $tags_count
 * @property-read Collection|Token[] $tokens
 * @property-read int|null $tokens_count
 * @method static Builder|User newModelQuery()
 * @method static Builder|User newQuery()
 * @method static Builder|User query()
 * @method static Builder|User whereApiAttrs($value)
 * @method static Builder|User whereAvatarUrl($value)
 * @method static Builder|User whereCardId($value)
 * @method static Builder|User whereCreatedAt($value)
 * @method static Builder|User whereEmail($value)
 * @method static Builder|User whereEnabled($value)
 * @method static Builder|User whereEntAttrs($value)
 * @method static Builder|User whereFaceId($value)
 * @method static Builder|User whereGender($value)
 * @method static Builder|User whereGroupId($value)
 * @method static Builder|User whereId($value)
 * @method static Builder|User whereMobile($value)
 * @method static Builder|User wherePassword($value)
 * @method static Builder|User whereRealname($value)
 * @method static Builder|User whereRememberToken($value)
 * @method static Builder|User whereUpdatedAt($value)
 * @method static Builder|User whereUsername($value)
 * @mixin Eloquent
 * @property-read Collection|Conference[] $conferences
 * @property-read int|null $conferences_count
 * @property-read Collection|Department[] $depts
 * @property-read int|null $depts_count
 * @property-read Collection|MessageReply[] $mReplies
 * @property-read int|null $m_replies_count
 * @property-read Collection|PollReply[] $pReplies
 * @property-read int|null $p_replies_count
 */
class User extends Authenticatable {
    
    use HasApiTokens, Notifiable, ModelTrait;
    
    protected $table = 'users';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        # 基本信息
        'group_id', 'username', 'password', 'gender',
        'email', 'mobile', 'face_id', 'card_id',
        'avatar_url', 'ent_attrs', 'api_attrs', 'enabeld',
        # 企业微信相关属性
        'ent_attrs->userid',
        'ent_attrs->english_name',
        'ent_attrs->alias',
        'ent_attrs->order',
        'ent_attrs->position',
        'ent_attrs->is_leader_in_dept',
        'ent_attrs->avatar',
        'ent_attrs->telephone',
        'ent_attrs->extattr',
        'ent_attrs->qr_code',
        'ent_attrs->external_profile',
        'ent_attrs->external_position',
        'ent_attrs->position',
        'ent_attrs->enable',
        'ent_attrs->status',
        'ent_attrs->synced',
        'ent_attrs->subscribed',
        # api相关属性
        'api_attrs->secret',
        'api_attrs->classname',
        'api_attrs->contact',
    ];
    
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = ['password', 'remember_token'];
    
    /** properties -------------------------------------------------------------------------------------------------- */
    /** @return BelongsTo */
    function group() { return $this->belongsTo('App\Models\Group'); }
    
    /** @return HasOne */
    function custodian() { return $this->hasOne('App\Models\Custodian'); }
    
    /** @return HasOne */
    function educator() { return $this->hasOne('App\Models\Educator'); }
    
    /** @return HasOne */
    function student() { return $this->hasOne('App\Models\Student'); }
    
    /** @return HasOne */
    function card() { return $this->hasOne('App\Models\Card'); }
    
    /** @return HasMany */
    function conferences() { return $this->hasMany('App\Models\Conference'); }
    
    /** @return HasOne */
    function face() { return $this->hasOne('App\Models\Face'); }
    
    /** @return HasMany */
    function orders() { return $this->hasMany('App\Models\Order'); }
    
    /** @return BelongsToMany */
    function depts() { return $this->belongsToMany('App\Models\Department', 'department_user'); }
    
    /** @return HasMany */
    function _tags() { return $this->hasMany('App\Models\Tag'); }
    
    /** @return BelongsToMany */
    function tags() { return $this->belongsToMany('App\Models\Tag', 'tag_user'); }
    
    /** @return HasMany */
    function events() { return $this->hasMany('App\Models\Event'); }
    
    /** @return HasMany */
    function openids() { return $this->hasMany('App\Models\Openid'); }
    
    /** @return HasMany */
    function polls() { return $this->hasMany('App\Models\Poll'); }
    
    /** @return HasMany */
    function pReplies() { return $this->hasMany('App\Models\PollReply'); }
    
    /** @return HasMany */
    function mReplies() { return $this->hasMany('App\Models\MessageReply'); }
    
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
                    return $this->iconHtml($d, $row['remark']);
                },
            ],
            ['db' => 'User.realname', 'dt' => 3],
            [
                'db'        => 'User.avatar_url', 'dt' => 4,
                'formatter' => function ($d) {
                    return $this->avatar($d);
                },
            ],
            [
                'db'        => 'User.gender', 'dt' => 5,
                'formatter' => function ($d) {
                    return $this->gender($d);
                },
            ],
            ['db' => 'User.mobile', 'dt' => 6],
            ['db' => 'User.email', 'dt' => 7],
            ['db' => 'User.created_at', 'dt' => 8],
            ['db' => 'User.updated_at', 'dt' => 9],
            [
                'db'        => 'User.enabled', 'dt' => 10,
                'formatter' => function ($d, $row) {
                    return Datatable::status($d, $row, false);
                },
            ],
            ['db' => 'Groups.remark as remark', 'dt' => 11],
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
        [$rootGId, $corpGId, $schoolGId] = array_map(
            function ($name) {
                return Group::whereName($name)->first()->id;
            }, ['运营', '企业', '学校']
        );
        $rootMenu = Menu::find((new Menu)->rootId(true));
        switch ($rootMenu->menuType->name) {
            case '根':
                $gIds = [$rootGId, $corpGId, $schoolGId];
                $users = null;
                break;
            case '企业':
                $gIds = [$corpGId, $schoolGId];
                $corp = Corp::whereMenuId($rootMenu->id)->first();
                $users = Department::find($corp->department_id)->users;
                foreach ($corp->schools as $school) {
                    $users = $users->merge(Department::find($school->department_id)->users);
                }
                break;
            default: #'学校':
                $gIds = [$schoolGId];
                $users = Department::find(School::whereMenuId($rootMenu->id)->first()->department_id)->users;
                break;
        }
        $condition = sprintf('User.group_id IN (%s)', collect($gIds)->join(',')) .
            ($users->isEmpty() ? '' : ' AND User.id IN (' . $users->pluck('id')->join(',') . ')');
        
        return Datatable::simple(
            $this, $columns, $joins, $condition
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
                    return $this->iconHtml($d, 'school');
                },
            ],
            ['db' => 'User.username', 'dt' => 3],
            ['db' => 'Groups.name as groupname', 'dt' => 4],
            ['db' => 'User.mobile', 'dt' => 5],
            ['db' => 'User.email', 'dt' => 6],
            ['db' => 'User.created_at', 'dt' => 7],
            ['db' => 'User.updated_at', 'dt' => 8],
            [
                'db'        => 'User.enabled', 'dt' => 9,
                'formatter' => function ($d, $row) {
                    $rechargeLink = $this->anchor(
                        'recharge_' . $row['id'],
                        '短信充值 & 查询',
                        'fa-money'
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
                    // unset($data['user']['mobile']);
                    $user = $this->create($data['user']);
                    (new Card)->store($user);
                    # 如果角色为校级管理员，则同时创建教职员工记录
                    if (!in_array($this->role($user->id), Constant::NON_EDUCATOR)) {
                        $data['user_id'] = $user->id;
                        Educator::create($data);
                    }
                    (new DepartmentUser)->storeByUserId($user->id, [$this->departmentId($data)]);
                    $group = Group::find($data['user']['group_id']);
                    $this->sync([
                        [$user->id, $group->name, 'create'],
                    ]);
                } else {
                    # 创建合作伙伴(api用户)
                    $data['api_attrs'] = json_encode([
                        'contact'   => $data['contact'],
                        'secret'    => $data['secret'],
                        'classname' => $data['classname'],
                    ], JSON_UNESCAPED_UNICODE);
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
            default: # 学校
                return School::find($data['school_id'])->department_id;
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
            $attrs = json_decode($user->ent_attrs, true);
            $params = [
                'userid'   => $attrs['userid'],
                'username' => $user->username,
                'position' => $attrs['position'] ?? $role,
                'corpIds'  => $this->corpIds($userId),
            ];
            if ($method != 'delete') {
                $departments = in_array($role, ['运营', '企业']) ? [1]
                    : $user->departments->unique()->pluck('id')->toArray();
                $params = array_merge($params, [
                    'name'         => $user->realname,
                    'english_name' => $attrs['english_name'],
                    'mobile'       => $user->mobile,
                    'email'        => $user->email,
                    'department'   => $departments,
                    'gender'       => $user->gender,
                    'remark'       => '',
                    'enable'       => $user->enabled,
                ]);
                # 在创建会员时，默认情况下不向该会员发送邀请
                $method != 'create' ?: $params = array_merge(
                    $params, ['to_invite' => false]
                );
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
    function corpIds($id = null) {
        
        $user = $this->find($id ?? Auth::id());
        
        return $this->role($user->id) == '运营'
            ? Corp::pluck('id')->toArray()
            : [(new Department)->corpId($user->departments->first()->id)];
        
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
                            (new DepartmentUser)->store($user->id, $this->departmentId($data));
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
                        $data['api_attrs'] = json_encode([
                            'contact'   => $data['contact'],
                            'secret'    => $data['secret'],
                            'classname' => $data['classname'],
                        ], JSON_UNESCAPED_UNICODE);
                        $this->find($id)->update($data);
                        MessageType::whereUserId($id)->first()->update([
                            'name'    => $data['realname'],
                            'remark'  => $data['realname'] . '接口消息',
                            'enabled' => Constant::DISABLED,
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
                        array_fill(0, 2, 'Flow'),
                        ['initiator_user_id', 'operator_user_id'], 'reset'
                    );
                    $this->purge([
                        class_basename($this), 'DepartmentUser', 'TagUser', 'Card',
                        'Tag', 'Poll', 'MessageReply', 'Face',
                        'PollReply', 'PollQuestionnaireParticipant',
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
        $corp = new Corp;
        abort_if(
            !in_array($field, ['group_id', 'corp_id']),
            HttpStatusCode::NOT_ACCEPTABLE,
            __('messages.not_acceptable')
        );
        $result = ['statusCode' => HttpStatusCode::OK];
        # 获取企业和学校列表
        if ($field == 'group_id') {
            $builder = Auth::user()->role() == '运营'
                ? Corp::whereEnabled(1)
                : Corp::whereId($corp->corpId());
            $result['corpList'] = $this->htmlSelect(
                $corps = $builder->pluck('name', 'id'),
                'corp_id'
            );
            Group::find($value)->name != '学校' ?: $corpId = array_key_first($corps->toArray());
        } else {
            $corpId = $value;
        }
        $condition = ['corp_id' => $corpId ?? 0, 'enabled' => 1];
        $schools = !($corpId ?? 0) ? collect([]) : School::where($condition)->pluck('name', 'id');
        $result['schoolList'] = $this->htmlSelect($schools, 'school_id');
        
        return response()->json($result);
        
    }
    
    /**
     * 返回指定用户直属的部门集合
     *
     * @param null $id
     * @return array
     */
    function deptIds($id = null) {
        
        $id = $id ?? Auth::id();
        $user = $this->find($id);
        $role = $this->role($id);
        
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
    
        switch (Request::route()->uri) {
            case 'users/message':
                /** @var \Illuminate\Support\Collection $nil */
                [$nil, $htmlApp, $htmlMessageType] = (new Message)->filters();
                
                return [
                    'titles'    => [
                        '#', '标题', '消息批次',
                        ['title' => '媒体类型', 'html' => $htmlApp],
                        ['title' => '类型', 'html' => $htmlMessageType],
                        '发送者',
                        ['title' => '接收于', 'html' => $this->htmlDTRange('接收于')],
                        [
                            'title' => '状态',
                            'html'  => $this->htmlSelect(
                                $nil->union(['未读', '已读']), 'filter_read'
                            ),
                        ],
                    ],
                    'batch'     => true,
                    'removable' => true,
                    'filter'    => true,
                ];
            case 'users/reset':
            case 'users/edit':
                return ['disabled' => true];
            case 'operators/index':
                return [
                    'batch'  => true,
                    'titles' => [
                        '#', '用户名', '角色', '真实姓名', '头像', '性别',
                        '手机号码', '电子邮件', '创建于', '更新于', '状态 . 操作',
                    ],
                ];
            case 'operators/create':
            case 'operators/edit/{id}':
                $operator = $departmentId = $role = null;
                $corps = $schools = collect([]);
                $rootMenu = Menu::find((new Menu)->rootId(true));
                if ($id = Request::route('id')) {
                    $operator = $this->find(Request::route('id'));
                    $role = $operator->role($operator->id);
                    $departmentId = $operator->departments->first()->id;
                }
                switch ($rootMenu->menuType->name) {
                    case '根':
                        $groups = Group::whereIn('name', ['运营', '企业', '学校']);
                        if ($id) {
                            $role == '运营' ?: $corps = new Corp;
                            $corpId = $operator->educator->school->corp_id;
                            $role != '学校' ?: $schools = School::whereCorpId($corpId);
                        }
                        break;
                    case '企业':
                        $groups = Group::whereIn('name', ['企业', '学校']);
                        if ($id) {
                            if ($role == '企业') {
                                $corps = Corp::whereDepartmentId($departmentId);
                            } else {
                                $corpId = head($this->corpIds($operator->id));
                                $corps = Corp::whereId($corpId);
                                $schools = School::whereCorpId($corpId);
                            }
                        } else {
                            $corps = Corp::whereMenuId($rootMenu->id);
                        }
                        break;
                    default: # 学校
                        $groups = Group::where(['name' => '学校']);
                        $schools = $id
                            ? School::whereDepartmentId($departmentId)
                            : School::whereMenuId($rootMenu->id);
                        $corps = Corp::whereId($schools->first()->corp_id);
                        break;
                }
                
                return array_combine(
                    ['groups', 'corps', 'schools'],
                    array_map(
                        function ($builder) {
                            return $builder->{'pluck'}('name', 'id');
                        }, [$groups, $corps, $schools]
                    )
                );
            case 'partners/index':
                return [
                    'batch'  => true,
                    'titles' => [
                        '#', '全称', '所属学校', '接口用户名', '类型',
                        '手机号码', '电子邮箱', '创建于', '更新于', '状态',
                    ],
                ];
            case 'partners/create':    # api用户创建/编辑
            case 'partners/edit/{id?}':
                return ['schools' => School::whereCorpId($this->corpId())->pluck('name', 'id')];
            default:                   # api用户短信充值/查询
                return (new Message)->compose('recharge');
        }
        
    }
    
    /**
     * 公众号用户绑定
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
                !$user = User::whereMobile($mobile)->first(),
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
                    'user_id' => $user->id,
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
                    'mobile'    => $user->mobile,
                    'code'      => $code = mt_rand(1000, 9999),
                    'expiredAt' => time() + 30 * 60,
                ]);
                $sent = (new Sms)->invoke(
                    'BatchSend2',
                    [$user->mobile, urlencode(
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