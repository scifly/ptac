<?php
namespace App\Models;

use App\Helpers\Constant;
use App\Helpers\ModelTrait;
use Eloquent;
use Exception;
use Laravel\Passport\Client;
use Laravel\Passport\Token;
use Throwable;
use Carbon\Carbon;
use App\Helpers\Snippet;
use App\Events\UserCreated;
use App\Events\UserDeleted;
use App\Events\UserUpdated;
use App\Helpers\HttpStatusCode;
use Illuminate\Support\Facades\DB;
use Laravel\Passport\HasApiTokens;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use App\Facades\DatatableFacade as Datatable;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Notifications\DatabaseNotificationCollection;

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
 * @property-read Operator $operator
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
        'order', 'mobile', 'enabled', 'synced'
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
     * 获取指定用户发出的消息对象
     *
     * @return HasMany
     */
    function messages() { return $this->hasMany('App\Models\Message'); }
    
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
     * 返回用户所属最顶级部门的ID
     *
     * @return mixed
     */
    function topDeptId() {
        
        $departmentIds = Auth::user()->departments
            ->pluck('id')->toArray();
        $levels = [];
        foreach ($departmentIds as $id) {
            $level = 0;
            $department = new Department();
            $levels[$id] = $department->level($id, $level);
            unset($department);
        }
        asort($levels);
        reset($levels);
        
        return key($levels) ?? 1;
        
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
     */
    function createWechatUser($id) {
        
        $user = self::find($id);
        $mobile = Mobile::whereUserId($id)->where('isdefault', 1)->first()->mobile;
        $data = [
            'userid'     => $user->userid,
            'name'       => $user->realname,
            'mobile'     => $mobile,
            'department' => $user->departments->pluck('id')->toArray(),
            'gender'     => $user->gender,
            'enable'     => $user->enabled,
        ];
        event(new UserCreated($data));
        
    }
    
    /**
     * 更新企业号会员
     *
     * @param $id
     * @return bool
     */
    function updateWechatUser($id) {
        
        $user = self::find($id);
        $mobile = Mobile::whereUserId($id)->where('isdefault', 1)->first()->mobile;
        $data = [
            'userid'       => $user->userid,
            'name'         => $user->realname,
            'english_name' => $user->english_name,
            'mobile'       => $mobile,
            'department'   => $user->departments->pluck('id')->toArray(),
            'gender'       => $user->gender,
            'enable'       => $user->enabled,
        ];
        event(new UserUpdated($data));
        return true;
        
    }
    
    /**
     * 删除企业号会员
     *
     * @param $id
     */
    function deleteWechatUser($id) {
        
        event(new UserDeleted([
            'userid' => self::find($id)->userid
        ]));
        
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
                $mobile->store($data, $user);
                unset($mobile);
                
                # 保存用户所属部门数据
                $du = new DepartmentUser();
                $du->store($data, $user);
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
                'enabled' => $action == 'enable' ? Constant::ENABLED : Constant::DISABLED
            ]);
            foreach ($ids as $id) {
                $this->updateWechatUser($id);
            }
            return $result;
        } else {
            $user = $this->find($id);
            abort_if(!$user, HttpStatusCode::NOT_FOUND, '找不到该用户');
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
                });
                # 更新手机号码
                Mobile::whereUserId($user->id)->delete();
                $mobile = new Mobile();
                $mobile->store($data, $user);
                unset($mobile);
                # 更新部门数据
                DepartmentUser::whereUserId($user->id)->delete();
                $du = new DepartmentUser();
                $du->store($data, $user);
                unset($du);
                # 更新企业号成员记录
                $this->updateWechatUser($user->id);
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
                if (!$this->purge($id)) { return false; }
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
     * @return \Illuminate\Http\JsonResponse
     */
    function csList() {
        
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
            $corps = Corp::whereEnabled(1)->pluck('name', 'id')->toArray();
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
            ['db' => 'Groups.name as role', 'dt' => 2],
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
                    $user = Auth::user();
                    $id = $row['id'];
                    $status = $d ? Snippet::DT_ON : Snippet::DT_OFF;
                    $status .= ($row['synced']
                        ? sprintf(Snippet::ICON, 'fa-wechat text-green', '已同步')
                        : sprintf(Snippet::ICON, 'fa-wechat text-gray', '未同步'));
                    $editLink = sprintf(Snippet::DT_LINK_EDIT, 'edit_' . $id);
                    $delLink = sprintf(Snippet::DT_LINK_DEL, $id);
    
                    return
                        $status .
                        ($user->can('act', $this->uris()['edit']) ? $editLink : '') .
                        ($user->can('act', $this->uris()['destroy']) ? $delLink : '') ;
                },
            ],
            ['db' => 'User.synced', 'dt' => 10]
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
        $menuType = Menu::find($menu->rootMenuId(true))->menuType->name;
        unset($menu);
        
        switch ($menuType) {
            case '根':
                $condition = sprintf($sql, implode(',', [$rootGId, $corpGId, $schoolGId]));
                break;
            case '企业':
                $condition = sprintf($sql, implode(',', [$corpGId, $schoolGId]));
                break;
            case '学校':
                $condition = sprintf($sql, implode(',', [$schoolGId]));
                break;
            default:
                break;
        }
    
        return Datatable::simple(
            $this->getModel(), $columns, $joins, $condition
        );
        
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
     * 删除指定用户
     *
     * @param $id
     * @return bool
     * @throws Throwable
     */
    private function purge($id): bool {
        
        $user = self::find($id);
        if (!isset($user)) { return false; }
        try {
            DB::transaction(function () use ($id, $user) {
                # 删除企业号成员
                User::deleteWechatUser($id);
                # custodian删除指定user绑定的部门记录
                DepartmentUser::whereUserId($id)->delete();
                # 删除与指定user绑定的手机记录
                Mobile::whereUserId($id)->delete();
                # 删除user
                $user->delete();
            });
        } catch (Exception $e) {
            throw $e;
        }
        
        return true;
        
    }
    
}