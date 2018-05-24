<?php
namespace App\Models;

use App\Facades\DatatableFacade as Datatable;
use App\Helpers\Constant;
use App\Helpers\HttpStatusCode;
use App\Helpers\ModelTrait;
use App\Helpers\Snippet;
use App\Jobs\WechatMember;
use Carbon\Carbon;
use Eloquent;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Http\JsonResponse;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Notifications\DatabaseNotificationCollection;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;
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
 * @property string|null $remember_token "记住我"令牌，登录时用
 * @property string $password 密码
 * @property string|null $email 电子邮箱
 * @property int $gender 性别
 * @property string $realname 真实姓名
 * @property string $avatar_url 头像URL
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int $enabled
 * @property string $userid 成员userid
 * @property string|null $english_name 英文名
 * @property int|null $isleader 上级字段，标识是否为上级。第三方暂不支持
 * @property string|null $position 职位信息
 * @property string|null $telephone 座机号码
 * @property int|null $order 部门内的排序值，默认为0。数量必须和department一致，数值越大排序越前面
 * @property string|null $avatar_mediaid 成员头像的mediaid，通过多媒体接口上传图片获得的mediaid
 * @property-read Custodian $custodian
 * @property-read Collection|Department[] $departments
 * @property-read Educator $educator
 * @property-read Group $group
 * @property-read Collection|Message[] $messages
 * @property-read Collection|Mobile[] $mobiles
 * @property-read DatabaseNotificationCollection|DatabaseNotification[] $notifications
 * @property-read Collection|Order[] $orders
 * @property-read Collection|PollQuestionnaireAnswer[] $pollQuestionnaireAnswers
 * @property-read Collection|PollQuestionnaireParticipant[] $pollQuestionnairePartcipants
 * @property-read Collection|PollQuestionnaire[] $pollQuestionnaires
 * @property-read Student $student
 * @method static Builder|User whereAvatarMediaid($value)
 * @method static Builder|User whereAvatarUrl($value)
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
 * @method static Builder|User whereUsername($value)
 * @mixin Eloquent
 * @property-read Collection|Client[] $clients
 * @property-read Collection|Token[] $tokens
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
        'group_id', 'username', 'password',
        'email', 'realname', 'gender',
        'avatar_url', 'userid', 'english_name',
        'isleader', 'position', 'telephone',
        'order', 'mobile', 'enabled', 'synced',
    ];
    
    const SELECT_HTML = '<select class="form-control select2" style="width: 100%;" id="ID" name="ID">';
    
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
    function pollQuestionnaireAnswers() {
        
        return $this->hasMany('App\Models\PollQuestionnaireAnswer');
        
    }
    
    /**
     * 获取指定用户参与的所有调查问卷对象
     *
     * @return HasMany
     */
    function pollQuestionnairePartcipants() {
        
        return $this->hasMany('App\Models\PollQuestionnaireParticipant');
        
    }
    
    /**
     * 返回用户列表(id, name)
     *
     * @param array $ids
     * @return array
     */
    function userList(array $ids) {
        
        $list = [];
        foreach ($ids as $id) {
            $user = self::find($id);
            $list[$user->id] = $user->realname;
        }
        
        return $list;
        
    }
    
    /**
     * 根据部门id获取部门所属学校的部门id
     *
     * @param $deptId
     * @return int|mixed
     */
    function schoolDeptId(&$deptId) {
        
        $dept = Department::find($deptId);
        $typeId = DepartmentType::whereName('学校')->first()->id;
        if ($dept->department_type_id != $typeId) {
            $deptId = $dept->parent_id;
            
            return self::schoolDeptId($deptId);
        } else {
            return $deptId;
        }
        
    }
    
    /**
     * 创建企业号会员
     *
     * @param $id
     * @return bool
     */
    function createWechatUser($id) {
        
        return $this->sync($id, 'create');
        
    }
    
    /**
     * 更新企业号会员
     *
     * @param $id
     * @return bool
     */
    function updateWechatUser($id) {
        
        return $this->sync($id, 'update');
        
    }
    
    /**
     * 批量更新企业号会员
     *
     * @param $ids
     */
    function batchUpdateWechatUsers($ids) {
        
        foreach ($ids as $id) {
            $this->updateWechatUser($id);
        }
        
    }
    
    /**
     * 删除企业号会员
     *
     * @param $id
     * @return bool
     */
    function deleteWechatUser($id) {
        
        return $this->sync($id, 'delete');
        
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
                # 创建用户
                $user = $this->create([
                    'username'     => $data['username'],
                    'userid'       => uniqid('manager_'),
                    'password'     => bcrypt($data['password']),
                    'group_id'     => $data['group_id'],
                    'email'        => $data['email'],
                    'realname'     => $data['realname'],
                    'gender'       => $data['gender'],
                    'english_name' => $data['english_name'],
                    'telephone'    => $data['telephone'],
                    'enabled'      => $data['enabled'],
                    'synced'       => $data['synced'],
                    'avatar_url'   => '',
                    'isleader'     => 0,
                ]);
                # 保存手机号码
                $mobile = new Mobile();
                $mobile->store($data['mobile'], $user);
                unset($mobile);
                # 保存用户所属部门数据
                $du = new DepartmentUser();
                $du->store([
                    'department_id' => $this->departmentId($data),
                    'user_id'       => $user->id,
                    'enabled'       => $data['enabled'],
                ]);
                unset($du);
                # 创建企业号成员
                $this->createWechatUser($user->id);
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
        
        if (Request::has('ids')) {
            $ids = Request::input('ids');
            $action = Request::input('action');
            $result = $this->whereIn('id', $ids)->update([
                'enabled' => $action == 'enable' ? Constant::ENABLED : Constant::DISABLED,
            ]);
            foreach ($ids as $id) {
                $this->updateWechatUser($id);
            }
            
            return $result;
        } else {
            $user = $this->find($id);
            try {
                # 更新用户数据
                DB::transaction(function () use ($data, $id, $user) {
                    $user->update([
                        'username'     => $data['username'],
                        'group_id'     => $data['group_id'],
                        'email'        => $data['email'],
                        'realname'     => $data['realname'],
                        'gender'       => $data['gender'],
                        'english_name' => $data['english_name'],
                        'telephone'    => $data['telephone'],
                        'enabled'      => $data['enabled'],
                    ]);
                    # 更新手机号码
                    Mobile::whereUserId($user->id)->delete();
                    $mobile = new Mobile();
                    $mobile->store($data['mobile'], $user);
                    unset($mobile);
                    # 更新部门数据
                    DepartmentUser::whereUserId($user->id)->delete();
                    $du = new DepartmentUser();
                    (new DepartmentUser())->store([
                        'department_id' => $this->departmentId($data),
                        'user_id'       => $user->id,
                        'enabled'       => Constant::ENABLED,
                    ]);
                    unset($du);
                    # 更新企业号成员记录
                    $this->updateWechatUser($user->id);
                });
            } catch (Exception $e) {
                throw $e;
            }
            
            return true;
            
        }
        
    }
    
    /**
     * 删除用户
     *
     * @param $id
     * @return bool
     * @throws Throwable
     */
    function remove($id = null) {
        
        if (Request::has('ids')) {
            $ids = Request::input('ids');
            foreach ($ids as $id) {
                if (!$this->purge($id)) {
                    return false;
                }
            }
            
            return true;
        } else {
            return $this->purge($id);
        }
        
    }
    
    /**
     * 返回指定用户所属的所有部门id
     *
     * @param integer $id 用户id
     * @return array
     */
    function departmentIds($id) {
        
        $departments = self::find($id)->departments;
        $departmentIds = [];
        foreach ($departments as $d) {
            $departmentIds[] = $d->id;
            $departmentIds = array_merge(
                $departmentIds, $d->subDepartmentIds($d->id)
            );
        }
        
        return array_unique($departmentIds);
        
    }
    
    /**
     * 返回指定角色对应的企业/学校列表HTML
     * 或返回指定企业对应的学校列表HTML
     *
     * @return JsonResponse
     */
    function csList() {
        
        function corps() {
            
            $user = Auth::user();
            switch ($user->group->name) {
                case '运营':
                    return Corp::whereEnabled(1)->pluck('name', 'id')->toArray();
                case '企业':
                    $departmentId = $this->head($user);
                    $corp = Corp::whereDepartmentId($departmentId)->first();
                    
                    return [$corp->id => $corp->name];
                default:
                    return [];
            }
            
        }
        
        $field = Request::input('field');
        $value = Request::input('value');
        abort_if(
            !in_array($field, ['group_id', 'corp_id']),
            HttpStatusCode::NOT_ACCEPTABLE,
            __('messages.not_acceptable')
        );
        $result = [
            'statusCode' => HttpStatusCode::OK,
        ];
        # 获取企业和学校列表
        $corpId = 0;
        if ($field == 'group_id') {
            $role = Group::find($value)->name;
            $corps = corps();
            $result['corpList'] = $this->selectList($corps, 'corp_id');
            if ($role == '学校') {
                reset($corps);
                $corpId = key($corps);
            }
        } else {
            $corpId = $value;
        }
        $schools = $corpId ? School::whereCorpId($corpId)
            ->where('enabled', 1)->get()
            ->pluck('name', 'id')->toArray() : [];
        $result['schoolList'] = $this->selectList($schools, 'school_id');
        
        return response()->json($result);
        
    }
    
    /**
     * 用户列表
     *
     * @return array
     */
    function datatable() {
        
        $columns = [
            ['db' => 'User.id', 'dt' => 0],
            ['db' => 'User.username', 'dt' => 1],
            [
                'db'        => 'Groups.name as role', 'dt' => 2,
                'formatter' => function ($d) {
                    switch ($d) {
                        case '运营':
                            $color = 'text-blue';
                            $class = 'fa-building';
                            break;
                        case '企业':
                            $color = 'text-green';
                            $class = 'fa-weixin';
                            break;
                        case '学校':
                            $color = 'text-purple';
                            $class = 'fa-university';
                            break;
                        default:
                            $color = '';
                            $class = '';
                            break;
                    }
                    
                    return sprintf(Snippet::ICON, $class . ' ' . $color, '')
                        . '<span class="' . $color . '">' . $d . '</span>';
                },
            ],
            ['db' => 'User.avatar_url', 'dt' => 3],
            ['db' => 'User.realname', 'dt' => 4],
            [
                'db'        => 'User.gender', 'dt' => 5,
                'formatter' => function ($d) {
                    return $d ? Snippet::MALE : Snippet::FEMALE;
                },
            ],
            ['db' => 'User.email', 'dt' => 6],
            ['db' => 'User.created_at', 'dt' => 7],
            ['db' => 'User.updated_at', 'dt' => 8],
            [
                'db'        => 'User.enabled', 'dt' => 9,
                'formatter' => function ($d, $row) {
                    return $this->syncStatus($d, $row);
                },
            ],
            ['db' => 'User.synced', 'dt' => 10],
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
        $rootGId = Group::whereName('运营')->first()->id;
        $corpGId = Group::whereName('企业')->first()->id;
        $schoolGId = Group::whereName('学校')->first()->id;
        $condition = '';
        $menu = new Menu();
        $rootMenu = Menu::find($menu->rootMenuId(true));
        $menuType = $rootMenu->menuType->name;
        unset($menu);
        switch ($menuType) {
            case '根':
                $condition = sprintf($sql, implode(',', [$rootGId, $corpGId, $schoolGId]));
                break;
            case '企业':
                $userIds = Department::find(Corp::whereMenuId($rootMenu->id)->first()->department_id)
                    ->users->pluck('id')->toArray();
                $condition = sprintf($sql, implode(',', [$corpGId, $schoolGId])) .
                    ' AND User.id IN (' . implode(',', $userIds) . ')';
                break;
            case '学校':
                $userIds = Department::find(School::whereMenuId($rootMenu->id)->first()->department_id)
                    ->users->pluck('id')->toArray();
                $condition = sprintf($sql, implode(',', [$schoolGId])) .
                    ' AND User.id IN (' . implode(',', $userIds) . ')';
                break;
            default:
                break;
        }
        
        return Datatable::simple(
            $this->getModel(), $columns, $joins, $condition
        );
        
    }
    
    /** Helper functions -------------------------------------------------------------------------------------------- */
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
     * 删除指定用户
     *
     * @param $id
     * @return bool
     * @throws Throwable
     */
    private function purge($id): bool {
        
        try {
            DB::transaction(function () use ($id) {
                # 删除企业号成员
                User::deleteWechatUser($id);
                # custodian删除指定user绑定的部门记录
                DepartmentUser::whereUserId($id)->delete();
                # 删除与指定user绑定的手机记录
                Mobile::whereUserId($id)->delete();
                # 删除user
                $this->find($id)->delete();
            });
        } catch (Exception $e) {
            throw $e;
        }
        
        return true;
        
    }
    
    /**
     * 同步企业微信会员
     *
     * @param $id
     * @param $action
     * @return bool
     */
    private function sync($id, $action) {
        
        $user = $this->find($id);
        $data = $action == 'delete' ? $user->userid : [
            'userid' => $user->userid,
            'name' => $user->realname,
            'english_name' => $user->english_name,
            'position' => $user->group->name,
            'mobile' => head(
                $user->mobiles
                    ->where('isdefault', 1)
                    ->pluck('mobile')->toArray()
            ),
            'email' => $user->email,
            'department' => in_array($user->group->name, ['运营', '企业'])
                ? [1] : $user->departments->pluck('id')->toArray(),
            'gender' => $user->gender,
            'enable' => $user->enabled
        ];
        WechatMember::dispatch($data, Auth::id(), $action);
        
        return true;
        
    }
    
    /**
     * 获取超级用户所处的部门id
     *
     * @param $data
     * @return int|mixed|null
     */
    private function departmentId($data) {
        
        switch (Group::find($data['group_id'])->name) {
            case '运营':
                return Department::whereDepartmentTypeId(
                    DepartmentType::whereName('根')->first()->id
                )->first()->id;
            case '企业':
                return Corp::find($data['corp_id'])->department_id;
            case '学校':
                return School::find($data['school_id'])->department_id;
            default:
                return null;
        }
        
    }
    
}