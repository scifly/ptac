<?php

namespace App\Models;

use App\Facades\DatatableFacade as Datatable;
use App\Helpers\Constant;
use App\Helpers\ModelTrait;
use App\Http\Requests\CustodianRequest;
use Carbon\Carbon;
use Eloquent;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\DB;
use Throwable;

/**
 * App\Models\Custodian 监护人
 *
 * @property int $id
 * @property int $user_id 监护人用户ID
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection|Student[] $students
 * @property-read \App\Models\User $user
 * @method static Builder|Custodian whereCreatedAt($value)
 * @method static Builder|Custodian whereId($value)
 * @method static Builder|Custodian whereUpdatedAt($value)
 * @method static Builder|Custodian whereUserId($value)
 * @mixin Eloquent
 */
class Custodian extends Model {
    
    use ModelTrait;

    const EXCEL_EXPORT_TITLE = [
        '监护人姓名', '性别', '电子邮箱',
        '手机号码', '创建于', '更新于',
    ];
    protected $fillable = ['user_id'];

    /**
     * 返回对应的用户对象
     *
     * @return BelongsTo
     */
    function user() {
        
        return $this->belongsTo('App\Models\User');
        
    }

    /**
     * 返回绑定的学生对象
     *
     * @return BelongsToMany
     */
    function students() {

        return $this->belongsToMany(
            'App\Models\Student',
            'custodians_students',
            'custodian_id',
            'student_id'
        );

    }

    /**
     * 保存新创建的监护人记录
     *
     * @param CustodianRequest $request
     * @return bool|mixed
     * @throws Exception
     * @throws \Throwable
     */
    function store(CustodianRequest $request) {

        try {
            DB::transaction(function () use ($request) {
                $user = $request->input('user');
                # 包含学生的Id
                $studentIds = $request->input('student_ids');
                # 与学生之间的关系
                $relationships = $request->input('relationships');
                $studentId_relationship = [];

                # 创建用户(User)
                $userid = uniqid('custodian_'); // 企业号会员userid
                $u = User::create([
                    'username' => $userid,
                    'group_id' => $user['group_id'],
                    'password' => bcrypt('custodian8888'),
                    'email' => $user['email'],
                    'realname' => $user['realname'],
                    'gender' => $user['gender'],
                    'avatar_url' => '00001.jpg',
                    'userid' => $userid,
                    'isleader' => 0,
                    'english_name' => $user['english_name'],
                    'telephone' => $user['telephone'],
                    'wechatid' => '',
                    'enabled' => $user['enabled'],
                ]);
                
                # 向部门用户关系表(DepartmentUser)中添加数据
                foreach ($studentIds as $key => $studentId) {
                    $student = Student::find($studentId);
                    if ($student) {
                        $du = DepartmentUser::whereUserId($student->user->id)->first();
                        if ($du) {
                            # 创建企业微信部门成员
                            DepartmentUser::create([
                                'department_id' => $du->department_id,
                                'user_id' => $u->id,
                                'enabled' => Constant::ENABLED,
                            ]);
                        }
                    }
                    $studentId_relationship[$studentId] = $relationships[$key];
                }

                # 保存用户手机号码(Mobile)
                $mobiles = $request->input('mobile');
                if ($mobiles) {
                    foreach ($mobiles as $k => $mobile) {
                        Mobile::create([
                            'user_id' => $u->id,
                            'mobile' => $mobile['mobile'],
                            'isdefault' => $mobile['isdefault'],
                            'enabled' => $mobile['enabled'],
                        ]);
                    }
                }
                
                # 创建监护人(Custodian) 记录
                $c = self::create(['user_id' => $u->id]);
                
                # 向监护人学生关系表(CustodianStudent)中添加数据
                if (isset($studentId_relationship)) {
                    $cs = new CustodianStudent();
                    $cs->storeByCustodianId($c->id, $studentId_relationship);
                    unset($cs);
                }
                
                # 创建企业号成员
                $u->createWechatUser($u->id);
            });
        } catch (Exception $e) {
            throw $e;
        }

        return true;

    }

    /**
     * 更新指定的监护人记录
     *
     * @param CustodianRequest $request
     * @param $custodianId
     * @return bool|mixed
     * @throws Throwable
     */
    function modify(CustodianRequest $request, $custodianId) {

        $custodian = self::find($custodianId);
        if (!isset($custodian)) { return false; }
        try {
            DB::transaction(function () use ($request, $custodianId, $custodian) {
                $userId = $request->input('user_id');
                $userData = $request->input('user');
                # 包含学生的Id
                $studentIds = $request->input('student_ids');
                # 与学生之间的关系
                $relationships = $request->input('relationships');
                $studentId_Relationship = [];
                
                # 更新用户(User)数据
                User::find($userId)->update([
                    'group_id' => $userData['group_id'],
                    'email' => $userData['email'],
                    'realname' => $userData['realname'],
                    'gender' => $userData['gender'],
                    'isleader' => 0,
                    'english_name' => $userData['english_name'],
                    'telephone' => $userData['telephone'],
                    'enabled' => $userData['enabled'],
                ]);
                
                # 保存部门用户关系(DepartmentUser)数据
                if (!empty($studentIds)) {
                    DepartmentUser::whereUserId($userId)->delete();
                    foreach ($studentIds as $key => $studentId) {
                        $student = Student::find($studentId);
                        if ($student) {
                            $du = DepartmentUser::whereUserId($student->user->id)->first();
                            if ($du) {
                                # 创建企业微信部门成员
                                DepartmentUser::create([
                                    'department_id' => $du->department_id,
                                    'user_id' => $userId,
                                    'enabled' => Constant::ENABLED,
                                ]);
                            }
                        }
                        $studentId_Relationship[$studentId] = $relationships[$key];
                    }
                }
                
                # 更新监护人(Custodian)记录
                $custodian->update(['user_id' => $userId]);
                
                # 更新用户的手机号码(Mobile)记录
                $mobiles = $request->input('mobile');
                if ($mobiles) {
                    $mobileModel = new Mobile();
                    $delMobile = $mobileModel->where('user_id', $userId)->delete();
                    if ($delMobile) {
                        foreach ($mobiles as $k => $mobile) {
                            $mobileData = [
                                'user_id' => $request->input('user_id'),
                                'mobile' => $mobile['mobile'],
                                'isdefault' => $mobile['isdefault'],
                                'enabled' => $mobile['enabled'],
                            ];
                            $mobileModel->create($mobileData);
                        }
                    }
                    unset($mobile);
                }
                
                # 更新监护人学生关系表(CustodianStudent)中的数据
                CustodianStudent::whereCustodianId($custodianId)->delete();
                $cs = new CustodianStudent();
                $cs->storeByCustodianId($custodianId, $studentId_Relationship);
                unset($cs);
                
                # 更新企业号会员数据
                $custodian->user->UpdateWechatUser($userId);
            });
        } catch (Exception $e) {
            throw $e;
        }

        return true;

    }

    /**
     * 删除指定的监护人记录
     *
     * @param $custodianId
     * @return bool|mixed
     * @throws Exception
     * @throws Throwable
     */
    function remove($custodianId) {
    
        # todo 删除监护人（监护人可能绑定不同学校的学生，所以不能直接删除）
        $custodian = self::find($custodianId);
        if (!isset($custodian)) { return false; }
        try {
            DB::transaction(function () use ($custodianId, $custodian) {
                $userId = $custodian->user_id;
                # 删除与指定监护人绑定的学生记录
                CustodianStudent::whereCustodianId($custodianId)->delete();
                
                # 删除指定的监护人记录
                $custodian->delete();
                # 删除user数据
                $custodian->user->remove($userId);
            });
        } catch (Exception $e) {
            throw $e;
        }

        return true;

    }

    /**
     * 导出（仅对当前用户可见的）监护人记录
     *
     * @return array
     */
    function export() {

        $custodians = self::whereIn('id', $this->contactIds('custodian'))->get();
        $data = array(self::EXCEL_EXPORT_TITLE);
        foreach ($custodians as $custodian) {
            if (!empty($custodian->user)) {
                $m = $custodian->user->mobiles;
                $mobile = [];
                foreach ($m as $key => $value) {
                    $mobile[] = $value->mobile;
                }
                $mobiles = implode(',', $mobile);
                $item = [
                    $custodian->user->realname,
                    $custodian->user->gender == Constant::ENABLED ? '男' : '女',
                    $custodian->user->email,
                    $mobiles,
                    $custodian->created_at,
                    $custodian->updated_at,
                ];
                $data[] = $item;
                unset($item);
            }

        }

        return $data;

    }

    /**
     * 监护人记录列表
     *
     * @return array
     */
    function datatable() {

        $columns = [
            ['db' => 'Custodian.id', 'dt' => 0],
            ['db' => 'User.realname', 'dt' => 1],
            [
                'db' => 'CustodianStudent.student_id', 'dt' => 2,
                'formatter' => function ($d) {
                    return Student::find($d)->user->realname;
                }
            ],
            ['db' => 'User.email', 'dt' => 3],
            ['db' => 'User.gender', 'dt' => 4,
                'formatter' => function ($d) {
                    return $d == 1 ? '男' : '女';
                },
            ],
            ['db' => 'Custodian.id as mobile', 'dt' => 5,
                'formatter' => function ($d) {
                    $custodian = $this->find($d);
                    $mobiles = Mobile::whereUserId($custodian->user_id)->get();
                    $mobile = [];
                    foreach ($mobiles as $key => $value) {
                        $mobile[] = $value->mobile;
                    }

                    return implode(',', $mobile);
                },
            ],
            ['db' => 'Custodian.created_at', 'dt' => 6],
            ['db' => 'Custodian.updated_at', 'dt' => 7],
            [
                'db' => 'User.enabled', 'dt' => 8,
                'formatter' => function ($d, $row) {
                    return Datatable::dtOps($d, $row, false);
                },
            ],
        ];
        $joins = [
            [
                'table' => 'users',
                'alias' => 'User',
                'type' => 'INNER',
                'conditions' => [
                    'User.id = Custodian.user_id',
                ]
            ],
            [
                'table' => 'custodians_students',
                'alias' => 'CustodianStudent',
                'type' => 'INNER',
                'conditions' => [
                    'CustodianStudent.custodian_id = Custodian.id',
                ]
            ],
            [
                'table' => 'students',
                'alias' => 'Student',
                'type' => 'INNER',
                'conditions' => [
                    'Student.id = CustodianStudent.student_id',
                ]
            ],
        ];
    
        $condition = 'Custodian.id IN (' . implode(',', $this->contactIds('custodian')) . ')';

        return Datatable::simple(
            self::getModel(), $columns, $joins, $condition
        );

    }

}
