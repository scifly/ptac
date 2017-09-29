<?php
namespace App\Models;

use App\Facades\DatatableFacade as Datatable;
use App\Http\Requests\CustodianRequest;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Mockery\Exception;

/**
 * App\Models\Custodian
 *
 * @property int $id
 * @property int $user_id 监护人用户ID
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property string $expiry 服务到期时间
 * @property-read \App\Models\User $user
 * @method static Builder|Custodian whereCreatedAt($value)
 * @method static Builder|Custodian whereExpiry($value)
 * @method static Builder|Custodian whereId($value)
 * @method static Builder|Custodian whereUpdatedAt($value)
 * @method static Builder|Custodian whereUserId($value)
 * @mixin \Eloquent
 * @property-read Collection|Student[] $students
 * @property-read Collection|CustodianStudent[] $custodianStudent
 * @property int $menu_id
 * @property int $department_id
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Custodian whereDepartmentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Custodian whereMenuId($value)
 */
class Custodian extends Model {

    protected $fillable = ['user_id', 'expiry'];

    /**
     * 返回对应的用户对象
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user() { return $this->belongsTo('App\Models\User'); }

    /**
     * 返回对应的学生对象
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function students() {
        return $this->belongsToMany(
            'App\Models\Student',
            'custodians_students',
            'custodian_id',
            'student_id'
        );
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function custodianStudents() {
        return $this->hasMany('App\Models\CustodianStudent');
    }

    /**
     * 保存新创建的监护人记录
     *
     * @param CustodianRequest $request
     * @return bool|mixed
     */
    public function store(CustodianRequest $request) {

        try {
            $exception = DB::transaction(function () use ($request) {

                $user = $request->input('user');
                # 包含学生的Id
                $studentIds = $request->input('student_ids');
                # 与学生之间的关系
                $relationships = $request->input('relationship');
                $studentId_relationship = [];
                foreach ($studentIds as $key => $studentId) {
                    $studentId_relationship[$studentId] = $relationships[$key];
                }
                $userData = [
                    'username'     => uniqid('custodian_'),
                    'group_id'     => $user['group_id'],
                    'password'     => bcrypt('custodian8888'),
                    'email'        => $user['email'],
                    'realname'     => $user['realname'],
                    'gender'       => $user['gender'],
                    'avatar_url'   => '00001.jpg',
                    'userid'       => uniqid('custodian_'),
                    'isleader'     => 0,
                    'english_name' => $user['english_name'],
                    'telephone'    => $user['telephone'],
                    'wechatid'     => '',
                    'enabled'      => $user['enabled'],
                ];
                $user = new User();
                $u = $user->create($userData);
                $custodianData = [
                    'user_id' => $u->id,
                    'expiry'  => '1970-01-01 00:00:00',
                ];
                # 向mobile表中添加工具
                $mobiles = $request->input('mobile');
                if ($mobiles) {
                    $mobileModel = new Mobile();
                    foreach ($mobiles as $k => $mobile) {
                        $mobileData = [
                            'user_id'   => $u->id,
                            'mobile'    => $mobile['mobile'],
                            'isdefault' => $mobile['isdefault'],
                            'enabled'   => $mobile['enabled'],
                        ];
                        $mobileModel->create($mobileData);
                    }
                    unset($mobileModel);
                }
                $c = $this->create($custodianData);
                # 向部门用户表添加数据
                $departmentUser = new DepartmentUser();
                $departmentIds = $request->input('selectedDepartments');
                $departmentUser->storeByUserId($u->id, $departmentIds);
                unset($departmentUser);
                # 向监护人学生表中添加数据
                $custodianStudent = new CustodianStudent();
                if ($studentId_relationship != null) {
                    $custodianStudent->storeByCustodianId($c->id, $studentId_relationship);
                }
                unset($custodianStudent);
                # 创建企业号成员
                $user->createWechatUser($u->id);
                unset($user);
            });

            return is_null($exception) ? true : $exception;
        } catch (Exception $e) {
            return false;
        }

    }

    /**
     * 更新指定的监护人记录
     *
     * @param CustodianRequest $request
     * @param $custodianId
     * @return bool|mixed
     */
    public function modify(CustodianRequest $request, $custodianId) {

        $custodian = $this->find($custodianId);
        if (!isset($custodian)) {
            return false;
        }
        try {
            $exception = DB::transaction(function () use ($request, $custodianId, $custodian) {

                $userId = $request->input('user_id');
                $userData = $request->input('user');
                # 包含学生的Id
                $studentIds = $request->input('student_ids');
                # 与学生之间的关系
                $relationships = $request->input('relationship');
                foreach ($studentIds as $key => $studentId) {
                    $studentId_Relationship[$studentId] = $relationships[$key];
                }
                $user = new User();
                $user->where('id', $userId)
                    ->update([
                        'group_id'     => $userData['group_id'],
                        'email'        => $userData['email'],
                        'realname'     => $userData['realname'],
                        'gender'       => $userData['gender'],
                        'isleader'     => 0,
                        'english_name' => $userData['english_name'],
                        'telephone'    => $userData['telephone'],
                        'enabled'      => $userData['enabled'],
                    ]);
                $custodian->update([
                    'user_id' => $userId,
                    'expiry'  => '1970-01-01 00:00:00',
                ]);
                $mobiles = $request->input('mobile');
                if ($mobiles) {
                    $mobileModel = new Mobile();
                    $delMobile = $mobileModel->where('user_id', $userId)->delete();
                    if ($delMobile) {
//                        dd($mobiles);
                        foreach ($mobiles as $k => $mobile) {
                            $mobileData = [
                                'user_id'   => $request->input('user_id'),
                                'mobile'    => $mobile['mobile'],
                                'isdefault' => $mobile['isdefault'],
                                'enabled'   => $mobile['enabled'],
                            ];
                            $mobileModel->create($mobileData);
                        }
                    }
                    unset($mobile);
                }
                # 向部门用户表添加数据
                $departmentIds = $request->input('selectedDepartments');
                $departmentUser = new DepartmentUser();
                $departmentUser::where('user_id', $userId)->delete();
                $departmentUser->storeByUserId($userId, $departmentIds);
                unset($departmentUser);
                # 向监护人学生表中添加数据
                $custodianStudent = new CustodianStudent();
//                $custodianStudent::whereCustodianId($custodianId)->delete();
                $custodianStudent::where('custodian_id', $custodianId)->delete();
                $custodianStudent->storeByCustodianId($custodianId, $studentId_Relationship);
                unset($custodianStudent);
                $user->UpdateWechatUser($userId);
                unset($user);
            });

            return is_null($exception) ? true : $exception;
        } catch (Exception $e) {
            return false;
        }

    }

    /**
     * 删除指定的监护人记录
     *
     * @param $custodianId
     * @return bool|mixed
     */
    public function remove($custodianId) {

        $custodian = $this->find($custodianId);
        if (!isset($custodian)) {
            return false;
        }
        try {
            $exception = DB::transaction(function () use ($custodianId, $custodian) {
                # 删除指定的监护人记录
                $custodian->delete();
                # 删除与指定监护人绑定的学生记录
                CustodianStudent::whereCustodianId($custodianId)->delete();
                # 删除与指定监护人绑定的部门记录
                DepartmentUser::where('user_id', $custodian['user_id'])->delete();
                # 删除与指定监护人绑定的手机记录
                Mobile::where('user_id', $custodian['user_id'])->delete();

            });

            return is_null($exception) ? true : $exception;
        } catch (Exception $e) {
            return false;
        }

    }

    /**
     * 返回监护人记录列表
     *
     * @return array
     */
    public function datatable() {

        $columns = [
            ['db' => 'Custodian.id', 'dt' => 0],
            ['db' => 'User.realname', 'dt' => 1],
            ['db'        => 'User.gender', 'dt' => 2,
             'formatter' => function ($d, $row) {
                 return $d == 1 ? '男' : '女';
             },
            ],
            ['db' => 'User.email', 'dt' => 3],
            ['db'        => 'Custodian.id as mobile', 'dt' => 4,
             'formatter' => function ($d) {
                 $custodian = Custodian::whereId($d)->first();
                 $mobiles = Mobile::where('user_id', $custodian->user_id)->get();
                 foreach ($mobiles as $key => $value) {
                     $mobile[] = $value->mobile;
                 }

                 return implode(',', $mobile);
             },
            ],
            ['db' => 'Custodian.expiry', 'dt' => 5,],
            ['db' => 'Custodian.created_at', 'dt' => 6],
            ['db' => 'Custodian.updated_at', 'dt' => 7],
            [
                'db'        => 'User.enabled', 'dt' => 8,
                'formatter' => function ($d, $row) {
                    return Datatable::dtOps($this, $d, $row);
                },
            ],
        ];
        $joins = [
            [
                'table'      => 'users',
                'alias'      => 'User',
                'type'       => 'INNER',
                'conditions' => [
                    'User.id = Custodian.user_id',
                ],
            ],
        ];

        return Datatable::simple($this, $columns, $joins);

    }

}
