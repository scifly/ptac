<?php
namespace App\Models;

use App\Facades\Datatable;
use App\Helpers\{Constant, HttpStatusCode, ModelTrait, Snippet};
use App\Jobs\SyncMember;
use Carbon\Carbon;
use Eloquent;
use Exception;
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
use Laravel\Passport\{Client, HasApiTokens, Token};
use Throwable;

/**
 * App\Models\User 用户
 *
 * @property int $id
 * @property int $group_id 所属角色/权限ID
 * @property int|null $card_id
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
        'group_id', 'username', 'password',
        'email', 'realname', 'gender', 'card_id',
        'avatar_url', 'userid', 'english_name',
        'isleader', 'position', 'telephone',
        'order', 'mobile', 'enabled', 'synced',
        'subscribed',
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
    function departments() {
        
        return $this->belongsToMany('App\Models\Department', 'departments_users');
        
    }
    
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
    function tags() { return $this->belongsToMany('App\Models\Tag', 'tags_users'); }
    
    /**
     * 获取指定用户创建的所有日历对象
     *
     * @return HasMany
     */
    function events() { return $this->hasMany('App\Models\Event'); }
    
    /**
     * 获取指定用户发起的所有调查问卷对象
     *
     * @return HasMany
     */
    function pollQuestionnaires() {
        
        return $this->hasMany('App\Models\PollQuestionnaire');
        
    }
    
    /**
     * 获取指定用户参与的调查问卷所给出的答案对象
     *
     * @return HasMany
     */
    function pqAnswers() {
        
        return $this->hasMany('App\Models\PollQuestionnaireAnswer');
        
    }
    
    /**
     * 获取指定用户参与的所有调查问卷对象
     *
     * @return HasMany
     */
    function pqParticipants() {
        
        return $this->hasMany('App\Models\PollQuestionnaireParticipant');
        
    }
    
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
            [
                'db'        => 'User.synced', 'dt' => 9,
                'formatter' => function ($d) {
                    return $this->synced($d);
                },
            ],
            [
                'db'        => 'User.subscribed', 'dt' => 10,
                'formatter' => function ($d) {
                    return $this->subscribed($d);
                },
            ],
            [
                'db'        => 'User.enabled', 'dt' => 11,
                'formatter' => function ($d, $row) {
                    return Datatable::status($d, $row, false);
                },
            ],
            ['db' => 'Groups.remark as remark', 'dt' => 12]
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
        list($rootGId, $corpGId, $schoolGId) = array_map(
            function ($name) { return Group::whereName($name)->first()->id; },
            ['运营', '企业', '学校']
        );
        $rootMenu = Menu::find((new Menu)->rootId(true));
        switch ($rootMenu->menuType->name) {
            case '根':
                $allowedGIds = [$rootGId, $corpGId, $schoolGId];
                $condition = sprintf($sql, implode(',', $allowedGIds));
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
                if (empty($userIds)) $userIds = [0];
                $condition = sprintf($sql, implode(',', [$corpGId, $schoolGId])) .
                    ' AND User.id IN (' . implode(',', array_unique($userIds)) . ')';
                break;
            case '学校':
                $userIds = Department::find(School::whereMenuId($rootMenu->id)->first()->department_id)
                    ->users->pluck('id')->toArray();
                if (empty($userIds)) $userIds = [0];
                $condition = sprintf($sql, implode(',', [$schoolGId])) .
                    ' AND User.id IN (' . implode(',', $userIds) . ')';
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
            ['db' => 'User.username', 'dt' => 2],
            ['db' => 'User.english_name', 'dt' => 3],
            ['db' => 'User.telephone', 'dt' => 4],
            ['db' => 'User.email', 'dt' => 5],
            ['db' => 'User.created_at', 'dt' => 6],
            ['db' => 'User.updated_at', 'dt' => 7],
            [
                'db'        => 'User.enabled', 'dt' => 8,
                'formatter' => function ($d, $row) {
                    return Datatable::status($d, $row, false);
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
                # 创建超级用户(运营/企业/学校)
                if ($data['group_id'] != Group::whereName('api')->first()->id) {
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
                        [$user->id, $group->name, 'create']
                    ]);
                # 创建合作伙伴(api用户)
                } else {
                    $partner = $this->create($data);
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
                # 更新超级用户(运营/企业/学校)
                if ($data['group_id'] != Group::whereName('api')->first()->id) {
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
                # 更新合作伙伴(api)
                } else {
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
                        'Tag', 'Mobile', 'PollQuestionnaire', 'MessageReply',
                        'PollQuestionnaireAnswer', 'PollQuestionnaireParticipant'
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
    
    /** Helper functions -------------------------------------------------------------------------------------------- */
    /**
     * 同步企业微信会员
     *
     * @param array $contacts
     * @param null $id - 接收广播的用户id
     * @return bool
     */
    function sync(array $contacts, $id = null) {
        
        foreach ($contacts as $contact) {
            list($userId, $role, $method) = $contact;
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
            if (Group::find($value)->name == '学校') {
                reset($corps);
                $corpId = key($corps);
            }
        } else {
            $corpId = $value;
        }
        $condition = ['corp_id' => $corpId ?? 0, 'enabled' => 1];
        $schools = !($corpId ?? 0) ? [] : School::where($condition)->pluck('name', 'id');
        $result['schoolList'] = $this->selectList($schools->toArray(), 'school_id');
        
        return response()->json($result);
        
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
        
        return [
            Request::route('id') ? $this->find(Request::route('id'))->mobiles : [],
            $groups ?? [], $corps, $schools,
        ];
        
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
    
}