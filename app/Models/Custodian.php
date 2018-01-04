<?php

namespace App\Models;

use App\Facades\DatatableFacade as Datatable;
use App\Http\Requests\CustodianRequest;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

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
 * @method static Builder|Custodian whereDepartmentId($value)
 * @method static Builder|Custodian whereMenuId($value)
 * @property-read Collection|CustodianStudent[] $custodianStudents
 */
class Custodian extends Model {

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
    public function user() { return $this->belongsTo('App\Models\User'); }

    /**
     * 返回对应的学生对象
     *
     * @return BelongsToMany
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
     * @return HasMany
     */
    public function custodianStudents() { return $this->hasMany('App\Models\CustodianStudent'); }
    
    /**
     * 保存新创建的监护人记录
     *
     * @param CustodianRequest $request
     * @return bool|mixed
     * @throws Exception
     * @throws \Throwable
     */
    static function store(CustodianRequest $request) {

        try {
            DB::transaction(function () use ($request) {
                $user = $request->input('user');
                # 包含学生的Id
                $studentIds = $request->input('student_ids');
                # 与学生之间的关系
                $relationships = $request->input('relationships');
                $studentId_relationship = [];
                foreach ($studentIds as $key => $studentId) {
                    $studentId_relationship[$studentId] = $relationships[$key];
                }
                # 创建用户
                $u = User::create([
                    'username' => uniqid('custodian_'),
                    'group_id' => $user['group_id'],
                    'password' => bcrypt('custodian8888'),
                    'email' => $user['email'],
                    'realname' => $user['realname'],
                    'gender' => $user['gender'],
                    'avatar_url' => '00001.jpg',
                    'userid' => uniqid('custodian_'),
                    'isleader' => 0,
                    'english_name' => $user['english_name'],
                    'telephone' => $user['telephone'],
                    'wechatid' => '',
                    'enabled' => $user['enabled'],
                ]);
                # 保存手机号码
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
                $c = self::create(['user_id' => $u->id]);
                # 向部门用户表添加数据
                // $departmentUser = new DepartmentUser();
                // $departmentIds = $request->input('selectedDepartments');
                // $departmentUser->storeByUserId($u->id, $departmentIds);
                // unset($departmentUser);
                # 向监护人学生表中添加数据
                if (isset($studentId_relationship)) {
                    CustodianStudent::storeByCustodianId($c->id, $studentId_relationship);
                }
                # 创建企业号成员
                User::createWechatUser($u->id);
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
     * @throws Exception
     * @throws \Throwable
     */
    static function modify(CustodianRequest $request, $custodianId) {

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
                if (!empty($studentIds)) {
                    foreach ($studentIds as $key => $studentId) {
                        $studentId_Relationship[$studentId] = $relationships[$key];
                    }
                }
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
                $custodian->update(['user_id' => $userId]);
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
                # 向部门用户表添加数据
                // $departmentIds = $request->input('selectedDepartments');
                // sort($departmentIds);
                // $departmentUser = new DepartmentUser();
                // $departmentUser::where('user_id', $userId)->delete();
                // $departmentUser->storeByUserId($userId, $departmentIds);
                // unset($departmentUser);
                # 向监护人学生表中添加数据
                CustodianStudent::whereCustodianId($custodianId)->delete();
                CustodianStudent::storeByCustodianId($custodianId, $studentId_Relationship);
                User::UpdateWechatUser($userId);
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
     * @throws \Throwable
     */
    static function remove($custodianId) {

        $custodian = self::find($custodianId);
        if (!isset($custodian)) { return false; }
        try {
            DB::transaction(function () use ($custodianId, $custodian) {
                # 删除指定的监护人记录
                $custodian->delete();
                # 删除与指定监护人绑定的学生记录
                CustodianStudent::whereCustodianId($custodianId)->delete();
                # 删除与指定监护人绑定的部门记录
                DepartmentUser::whereUserId($custodian['user_id'])->delete();
                # 删除与指定监护人绑定的手机记录
                Mobile::whereUserId($custodian['user_id'])->delete();
            });
        } catch (Exception $e) {
            throw $e;
        }
        
        return true;
        
    }

    /**
     * 获取字段列表
     *
     * @param $field
     * @param $id
     * @return array
     */
    static function getFieldList($field, $id) {

        $grades = [];
        $classes = [];
        $students = [];
        switch ($field) {
            case 'school':
                $grades = Grade::whereSchoolId($id)
                    ->where('enabled', 1)
                    ->pluck('name', 'id');
                $classes = Squad::whereGradeId($grades->keys()->first())
                    ->where('enabled', 1)
                    ->pluck('name', 'id');
                $students = Student::whereClassId($classes->keys()->first())
                    ->where('enabled', 1)
                    ->pluck('student_number', 'id');
                break;
            case 'grade':
                $classes = Squad::whereGradeId($id)
                    ->where('enabled', 1)
                    ->pluck('name', 'id');
                $students = Student::whereClassId($classes->keys()->first())
                    ->where('enabled', 1)
                    ->pluck('student_number', 'id');
                break;
            case 'class':
                $list = Student::whereClassId($id)
                    ->where('enabled', 1)
                    ->get();
                if (!empty($list)) {
                    foreach ($list as $s) {
                        $students[$s->id] = $s->user->realname . "-" . $s->student_number;
                    }
                }
                break;
            default:
                break;
        }
        $htmls = array_map(
            function ($items) {
                $html = '<select class="form-control col-sm-6" id="%s" name="%s">';
                foreach ($items as $key => $value) {
                    $html .= '<option value="' . $key . '">' . $value . '</option>';
                }
                $html .= '</select>';
                return $html;

            }, [$grades, $classes, $students]
        );

        return [
            'grades' => sprintf($htmls[0], 'gradeId', 'gradeId'),
            'classes' => sprintf($htmls[1], 'classId', 'classId'),
            'students' => sprintf($htmls[2], 'studentId', 'studentId')
        ];
        
    }

    static function export() {

        $custodians = self::all();
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
                    $custodian->user->gender == 1 ? '男' : '女',
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
     * 返回监护人记录列表
     *
     * @return array
     */
    static function datatable() {

        $columns = [
            ['db' => 'Custodian.id', 'dt' => 0],
            ['db' => 'User.realname', 'dt' => 1],
            ['db' => 'User.gender', 'dt' => 2,
                'formatter' => function ($d) {
                    return $d == 1 ? '男' : '女';
                },
            ],
            ['db' => 'User.email', 'dt' => 3],
            ['db' => 'Custodian.id as mobile', 'dt' => 4,
                'formatter' => function ($d) {
                    $custodian = Custodian::find($d);
                    $mobiles = Mobile::whereUserId($custodian->user_id)->get();
                    $mobile = [];
                    foreach ($mobiles as $key => $value) {
                        $mobile[] = $value->mobile;
                    }

                    return implode(',', $mobile);
                },
            ],
            ['db' => 'Custodian.created_at', 'dt' => 5],
            ['db' => 'Custodian.updated_at', 'dt' => 6],
            [
                'db' => 'User.enabled', 'dt' => 7,
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
            [
                'table' => 'classes',
                'alias' => 'Squad',
                'type' => 'INNER',
                'conditions' => [
                    'Squad.id = Student.class_id',
                ],
            ],
            [
                'table' => 'grades',
                'alias' => 'Grade',
                'type' => 'INNER',
                'conditions' => [
                    'Grade.id = Squad.grade_id',
                ],
            ],
        ];
        $condition = 'Grade.school_id = ' . School::schoolId();
        
        return Datatable::simple(self::getModel(), $columns, $joins, $condition);

    }

}
