<?php

namespace App\Models;

use App\Events\UserCreated;
use App\Events\UserDeleted;
use App\Events\UserUpdated;
use App\Facades\DatatableFacade as Datatable;
use App\Helpers\HttpStatusCode;
use App\Helpers\Snippet;
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
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Notifications\DatabaseNotificationCollection;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;
use Laravel\Passport\HasApiTokens;
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
 * @property string|null $wechatid 微信号
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
 * @method static Builder|User whereWechatid($value)
 * @mixin Eloquent
 */
class User extends Authenticatable {

    use HasApiTokens, Notifiable;
    
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
    
    const SELECT_HTML = '<select class="form-control select2" style="width: 100%;" id="corp_id" name="%s">';

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
     * 获取指定用户对应的管理/操作员对象
     *
     * @return HasOne
     */
    function operator() { return $this->hasOne('App\Models\Operator'); }

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
            'userid' => $user->userid,
            'name' => $user->realname,
            'mobile' => $mobile,
            'department' => $user->departments->pluck('id')->toArray(),
            'gender' => $user->gender,
            'enable' => $user->enabled,
        ];
        event(new UserCreated($data));

    }
    /**
     * 更新企业号会员
     *
     * @param $id
     */
    function updateWechatUser($id) {

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
    function deleteWechatUser($id) {

        event(new UserDeleted(self::find($id)->userid));

    }
    
    /**
     * 保存用户
     *
     * @param array $data
     * @return bool
     */
    function store(array $data) {
        
        return $this->create($data) ? true : false;
        
    }
    
    /**
     * 更新用户
     *
     * @param array $data
     * @param $id
     * @param bool $fireEvent
     * @return bool
     */
    function modify(array $data, $id, $fireEvent = false) {

        $user = self::find($id);
        $user->username=$data["username"];
        $user->english_name=$data["english_name"];
        $user->wechatid=$data["wechatid"];
        $user->gender=$data["gender"];
        $user->telephone = $data["telephone"];
        $user->email=$data["email"];
        $updated = $user->update();
        if ($updated && $fireEvent) {
            #event(new SchoolUpdated(self::find($id)));
            return true;
        }

        return $updated ? true : false;

    }
    
    /**
     * 删除用户数据
     *
     * @param $id
     * @return bool
     * @throws Throwable
     */
    function remove($id){
        
        $user = self::find($id);
        if (!isset($user)) { return false; }
        try {
            DB::transaction(function () use ($id, $user) {
                # custodian删除指定user绑定的部门记录
                DepartmentUser::whereUserId($id)->delete();
                # 删除与指定user绑定的手机记录
                Mobile::whereUserId($id)->delete();
                # 删除企业号成员
                User::deleteWechatUser($id);
                # 删除user
                $user->delete();
            });
        } catch (Exception $e) {
            throw $e;
        }
        
        return true;
        
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
     * 返回角色对应的企业/学校列表HTML
     *
     * @return \Illuminate\Http\JsonResponse
     */
    function groupList() {
        
        $groupId = Request::input('group_id');
        abort_if(!$groupId, HttpStatusCode::NOT_ACCEPTABLE, __('messages.not_acceptable'));
        $role = Group::find($groupId)->name;
        $result = [
            'corps' => '',
            'schools' => '',
        ];
        $corps = Corp::pluck('name', 'id')->toArray();
        $html = sprintf(self::SELECT_HTML, 'group_id');
        
        foreach ($corps as $corp) {
            $html .= '<option value="' . $corp->id . '">' . $corp->name . '</option>';
        }
        $result['corps'] = $html . '</select>';
        
        if ($role == '学校') {
            reset($corps);
            $schools = School::whereCorpId(key($corps))->get()->pluck('name', 'id')->toArray();
            $html = sprintf(self::SELECT_HTML, 'school_id');
            foreach ($schools as $school) {
                $html .= '<option value="' . $school->id . '">' . $school->name . '</option>';
            }
            $result['schools'] = $html . '</select>';
        }
        
        return response()->json([
            'statusCode' => HttpStatusCode::OK,
            'corpList' => $result['corps'],
            'schoolList' => $result['schools']
        ]);
        
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
                'db' => 'User.gender', 'dt' => 5,
                'formatter' => function ($d) {
                    return $d ? Snippet::MALE : Snippet::FEMALE;
                },
            ],
            ['db' => 'User.email', 'dt' => 6],
            ['db' => 'User.created_at', 'dt' => 7],
            ['db' => 'User.updated_at', 'dt' => 8],
            [
                'db' => 'User.enabled', 'dt' => 9,
                'formatter' => function ($d, $row) {
                    return Datatable::dtOps($d, $row, false, true, true);
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
        $user = Auth::user();
        $sql = 'User.group_id IN (%s)';
        $rootGroupId = Group::whereName('运营')->first()->id;
        $corpGroupId = Group::whereName('企业')->first()->id;
        $schoolGroupId = Group::whereName('学校')->first()->id;
        switch ($user->group->name) {
            case '运营':
                $condition = sprintf($sql, implode(',', [$rootGroupId, $corpGroupId, $schoolGroupId]));
                break;
            case '企业':
                $condition = sprintf($sql, implode(',', [$corpGroupId, $schoolGroupId]));
                break;
            case '学校':
                $condition = sprintf($sql, implode(',', [$schoolGroupId]));
                break;
            default:
                break;
        }

        return Datatable::simple(
            $this->getModel(), $columns, $joins, $condition
        );

    }

}
