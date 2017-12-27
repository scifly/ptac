<?php

namespace App\Models;

use App\Facades\DatatableFacade as Datatable;
use App\Helpers\ModelTrait;
use App\Http\Requests\OperatorRequest;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * App\Models\Operator 管理/操作员
 *
 * @property int $id
 * @property int $company_id 所属运营者公司ID
 * @property int $user_id 用户ID
 * @property string $school_ids 可管理的学校ID
 * @property int $type 管理员类型：0 - 我们 1 - 代理人
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder|Operator whereCompanyId($value)
 * @method static Builder|Operator whereCreatedAt($value)
 * @method static Builder|Operator whereId($value)
 * @method static Builder|Operator whereSchoolIds($value)
 * @method static Builder|Operator whereType($value)
 * @method static Builder|Operator whereUpdatedAt($value)
 * @method static Builder|Operator whereUserId($value)
 * @mixin \Eloquent
 * @property-read User $user
 */
class Operator extends Model {

    use ModelTrait;

    protected $fillable = ['user_id', 'school_ids', 'type', 'enabled'];

    /**
     * 获取指定管理/操作员对应的用户对象
     *
     * @return BelongsTo
     */
    public function user() { return $this->belongsTo('App\Models\User'); }
    
    /**
     * 保存系统管理员
     *
     * @param OperatorRequest $request
     * @return bool|mixed
     * @throws Exception
     * @throws \Throwable
     */
    static function store(OperatorRequest $request) {

        try {
            DB::transaction(function () use ($request) {
                # step 1: 创建用户记录
                $user = $request->input('user');
                $data = [
                    'username' => $user['username'],
                    'group_id' => $user['group_id'],
                    'password' => bcrypt($user['password']),
                    'email' => $user['email'],
                    'realname' => $user['realname'],
                    'gender' => $user['gender'],
                    'avatar_url' => '00001.jpg',
                    'userid' => $user['username'],
                    'isleader' => 0,
                    'english_name' => $user['english_name'],
                    'telephone' => $user['telephone'],
                    'wechatid' => '',
                    'enabled' => $user['enabled'],
                ];
                $u = User::create($data);

                # step 2: 创建部门用户对应关系记录
                $departments = $request->input('selectedDepartments');
                $departments = self::filterDepartments($departments, $u);
                if (!$departments) {
                    throw new Exception('部门数据错误');
                }
                foreach ($departments as $d) {
                    $data = [
                        'user_id' => $u->id,
                        'department_id' => $d,
                        'enabled' => $u->enabled,
                    ];
                    DepartmentUser::create($data);
                }

                # step 3: 创建Operator记录
                self::create(['user_id' => $u->id, 'type' => 0]);

                # step 4: 创建Mobile记录
                $mobiles = $request->input('mobile');
                if ($mobiles) {
                    foreach ($mobiles as $m) {
                        $data = [
                            'user_id' => $u->id,
                            'mobile' => $m['mobile'],
                            'isdefault' => $m['isdefault'],
                            'enabled' => $m['enabled'],
                        ];
                        Mobile::create($data);
                    }
                }

                # step 5: 创建企业号成员
                $u->createWechatUser($u->id);
            });

        } catch (Exception $e) {
            throw $e;
        }
        return true;

    }

    /**
     * 返回系统管理员所属部门ID数组
     *
     * @param array $departments
     * @param User $user 已创建的用户对象
     * @return array|bool
     */
    private static function filterDepartments(array $departments, User $user) {

        $companyDepartmentIds = Company::pluck('department_id')->toArray();
        $corpDepartmentIds = Corp::pluck('department_id')->toArray();
        $schoolDepartmentIds = School::pluck('department_id')->toArray();
        switch ($user->group->name) {
            case '运营':
                return array_unique(array_merge(
                    $departments, $companyDepartmentIds, $corpDepartmentIds, [1] // 根部门ID
                ));
                break;
            case '企业':
                $o = Auth::user();
                if ($o->group->name == '企业') {
                    $departments[] = $o->topDeptId();
                    $departments = array_unique($departments);
                } else {
                    # 所属企业的数量
                    $corps = 0;
                    foreach ($departments as $d) {
                        if (in_array($d, $corpDepartmentIds)) {
                            $corps += 1;
                        }
                    }
                    # 企业级管理员只能管理一个企业号
                    if ($corps != 1) {
                        return false;
                    }
                }
                foreach ($departments as $d) {
                    # 企业级管理员不得属于运营级部门
                    if (in_array($d, $companyDepartmentIds)) {
                        return false;
                    }
                }
                break;
            case '学校':
                $o = Auth::user();
                if ($o->group->name == '学校') {
                    $departments[] = $o->topDeptId();
                    $departments = array_unique($departments);
                } else {
                    # 所属学校的数量
                    $schools = 0;
                    foreach ($departments as $d) {
                        if (in_array($d, $schoolDepartmentIds)) {
                            $schools += 1;
                        }
                    }
                    # 校级管理员只能管理一所学校
                    if ($schools != 1) {
                        return false;
                    }
                }
                foreach ($departments as $d) {
                    # 校级管理员不得属于企业或运营级部门
                    if (in_array($d, $companyDepartmentIds) || in_array($d, $corpDepartmentIds)) {
                        return false;
                    }
                }
                break;
            default:
                break;
        }
        # 所属部门数量不得超过20个
        if (sizeof($departments) > 20) {
            return false;
        }

        return $departments;

    }
    
    /**
     * 更新系统管理员
     *
     * @param OperatorRequest $request
     * @param $id
     * @return bool|mixed
     * @throws Exception
     * @throws \Throwable
     */
    static function modify(OperatorRequest $request, $id) {

        $operator = self::find($id);
        if (!$operator) { return false; }
        try {
            DB::transaction(function () use ($request, $id, $operator) {
                # step 1: 更新对应的User记录
                $user = $request->input('user');
                $operator->user->update([
                    'username' => $user['username'],
                    'group_id' => $user['group_id'],
                    'email' => $user['email'],
                    'realname' => $user['realname'],
                    'gender' => $user['gender'],
                    'avatar_url' => '00001.jpg',
                    'userid' => $user['username'],
                    'isleader' => 0,
                    'english_name' => $user['english_name'],
                    'telephone' => $user['telephone'],
                    'wechatid' => '',
                    'enabled' => $user['enabled'],
                ]);

                # step 2: 更新部门用户对应关系
                $departments = $request->input('selectedDepartments');
                $departments = self::filterDepartments($departments, $operator->user);
                if (!$departments) {
                    throw new Exception('部门数据错误');
                }
                DepartmentUser::whereUserId($operator->user->id)->delete();
                foreach ($departments as $d) {
                    $data = [
                        'user_id' => $operator->user->id,
                        'department_id' => $d,
                        'enabled' => $operator->user->enabled,
                    ];
                    DepartmentUser::create($data);
                }

                # step 3: 更新Operator记录
                $operator->update([
                    'user_id' => $operator->user->id,
                    'type' => 0,
                ]);

                # step 4: 更新Mobile记录
                $mobiles = $request->input('mobile');
                if ($mobiles) {
                    Mobile::whereUserId($operator->user->id)->delete();
                    foreach ($mobiles as $m) {
                        $data = [
                            'user_id' => $request->input('user_id'),
                            'mobile' => $m['mobile'],
                            'isdefault' => $m['isdefault'],
                            'enabled' => $m['enabled'],
                        ];
                        Mobile::create($data);
                    }
                }
                # 更新企业号成员记录
                $operator->user->UpdateWechatUser($request->input('user_id'));
            });

        } catch (Exception $e) {
            throw $e;
        }
        
        return true;

    }
    
    /**
     * 删除系统管理员
     *
     * @param $id
     * @return bool
     * @throws Exception
     */
    static function remove($id) {

        $operator = self::find($id);
        if (!$operator) { return false; }
        
        return self::removable($id) ? $operator->delete() : false;

    }
    
    /**
     * 操作员列表
     *
     * @return array
     */
    static function datatable() {

        $columns = [
            ['db' => 'Operator.id', 'dt' => 0],
            ['db' => 'User.realname', 'dt' => 1],
            ['db' => 'User.username', 'dt' => 2],
            ['db' => 'Groups.name as groupname', 'dt' => 3],
            ['db' => 'User.userid', 'dt' => 4],
            ['db' => 'Mobile.mobile', 'dt' => 5],
            ['db' => 'Operator.created_at', 'dt' => 6],
            ['db' => 'Operator.updated_at', 'dt' => 7],
            [
                'db' => 'User.enabled', 'dt' => 8,
                'formatter' => function ($d, $row) {
                    return Datatable::dtOps($d, $row);
                },
            ],
        ];
        $joins = [
            [
                'table' => 'users',
                'alias' => 'User',
                'type' => 'INNER',
                'conditions' => ['User.id = Operator.user_id'],
            ],
            [
                'table' => 'groups',
                'alias' => 'Groups',
                'type' => 'INNER',
                'conditions' => ['Groups.id = User.group_id'],
            ],
            [
                'table' => 'mobiles',
                'alias' => 'Mobile',
                'type' => 'LEFT',
                'conditions' => [
                    'User.id = Mobile.user_id',
                    'Mobile.isdefault = 1',
                ],
            ],
        ];
        
        return Datatable::simple(self::getModel(), $columns, $joins);

    }

}
