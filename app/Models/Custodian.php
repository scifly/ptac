<?php
namespace App\Models;

use Eloquent;
use Exception;
use Throwable;
use Carbon\Carbon;
use App\Helpers\Snippet;
use App\Helpers\Constant;
use App\Helpers\ModelTrait;
use App\Helpers\HttpStatusCode;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use App\Facades\DatatableFacade as Datatable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * App\Models\Custodian 监护人
 *
 * @property int $id
 * @property int $user_id 监护人用户ID
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property boolean $enabled
 * @property-read Collection|Student[] $students
 * @property-read User $user
 * @method static Builder|Custodian whereCreatedAt($value)
 * @method static Builder|Custodian whereId($value)
 * @method static Builder|Custodian whereUpdatedAt($value)
 * @method static Builder|Custodian whereUserId($value)
 * @method static Builder|Custodian whereEnabled($value)
 * @mixin Eloquent
 */
class Custodian extends Model {
    
    use ModelTrait;
    
    const EXCEL_EXPORT_TITLE = [
        '监护人姓名', '性别', '电子邮箱',
        '手机号码', '创建于', '更新于',
    ];
    
    protected $fillable = ['user_id', 'enabled'];
    
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
     * @param array $data
     * @return bool|mixed
     * @throws Exception
     * @throws Throwable
     */
    function store(array $data) {
        
        try {
            DB::transaction(function () use ($data) {
                # 创建用户(User)
                $userid = uniqid('custodian_'); // 企业号会员userid
                $user = User::create([
                    'username'     => $userid,
                    'userid'       => $userid,
                    'password'     => bcrypt('custodian8888'),
                    'group_id'     => Group::whereName('监护人')->first()->id,
                    'email'        => $data['user']['email'],
                    'realname'     => $data['user']['realname'],
                    'gender'       => $data['user']['gender'],
                    'english_name' => $data['user']['english_name'],
                    'telephone'    => $data['user']['telephone'],
                    'enabled'      => $data['user']['enabled'],
                    'avatar_url'   => '00001.jpg',
                    'isleader'     => 0,
                    'synced'       => 0,
                    'subscribed'   => 0,
                ]);
                # 创建监护人(Custodian) 记录
                $custodian = self::create([
                    'user_id' => $user->id,
                    'enabled' => $user->enabled,
                ]);
                # 保存监护人用户&部门绑定关系
                $studentIds = $data['student_ids']; # 被监护人的学生ids
                $relationships = $data['relationships']; # 监护关系
                $rses = [];
                $du = new DepartmentUser();
                foreach ($studentIds as $key => $sId) {
                    $student = Student::find($sId);
                    abort_if(!$student, HttpStatusCode::NOT_FOUND, '找不到学生id: ' . $sId . '对应的记录');
                    $du->store([
                        'department_id' => $student->squad->department_id,
                        'user_id'       => $user->id,
                        'enabled'       => Constant::ENABLED,
                    ]);
                    $rses[$sId] = $relationships[$key];
                }
                unset($du);
                # 保存监护关系
                (new CustodianStudent)->storeByCustodianId($custodian->id, $rses);
                # 保存用户手机号码
                (new Mobile)->store($data['mobile'], $user);
                # 创建企业号成员
                $user->createWechatUser($user->id);
            });
        } catch (Exception $e) {
            throw $e;
        }
        
        return true;
        
    }
    
    /**
     * 更新指定的监护人记录
     *
     * @param array $data
     * @param $id
     * @return bool|mixed
     * @throws Exception
     * @throws Throwable
     */
    function modify(array $data, $id = null) {
        
        if (!$id) {
            return $this->batch($this);
        }
        $custodian = $this->find($id);
        if (!$custodian) {
            return false;
        }
        try {
            DB::transaction(function () use ($data, $id, $custodian) {
                
                # 更新用户数据
                $userId = $data['user_id'];
                User::find($userId)->update([
                    'group_id'     => $data['user']['group_id'],
                    'email'        => $data['user']['email'],
                    'realname'     => $data['user']['realname'],
                    'gender'       => $data['user']['gender'],
                    'english_name' => $data['user']['english_name'],
                    'telephone'    => $data['user']['telephone'],
                    'enabled'      => $data['user']['enabled'],
                    'isleader'     => 0,
                ]);
                # 更新监护人记录
                $custodian->update([
                    'user_id' => $userId,
                    'enabled' => $custodian->user->enabled,
                ]);
                # 更新用户所在部门
                $studentIds = $data['student_ids']; # 被监护人的学生ids
                $relationships = $data['relationships']; # 监护关系
                $rses = [];
                $du = new DepartmentUser();
                DepartmentUser::whereUserId($userId)->delete();
                foreach ($studentIds as $key => $sId) {
                    $student = Student::find($sId);
                    abort_if(!$student, HttpStatusCode::NOT_FOUND, '找不到学生id: ' . $sId . '对应的记录');
                    $du->store([
                        'department_id' => $student->squad->department_id,
                        'user_id'       => $custodian->user_id,
                        'enabled'       => Constant::ENABLED,
                    ]);
                    $rses[$sId] = $relationships[$key];
                }
                unset($du);
                # 更新监护人学生关系表(CustodianStudent)中的数据
                CustodianStudent::whereCustodianId($id)->delete();
                (new CustodianStudent)->storeByCustodianId($id, $rses);
                # 更新用户的手机号码(Mobile)记录
                Mobile::whereUserId($userId)->delete();
                (new Mobile)->store($data['mobile'], $custodian->user);
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
     * @param $id
     * @return bool|mixed
     * @throws Exception
     * @throws Throwable
     */
    function remove($id = null) {
        
        return (new User)->removeContact($this, $id);
        
    }
    
    /**
     * 删除监护人
     *
     * @param null $id
     * @return bool
     * @throws Exception
     */
    function purge($id = null) {
        
        try {
            DB::transaction(function () use ($id) {
                $custodian = $this->find($id);
                CustodianStudent::whereCustodianId($id)->delete();
                (new User)->remove($custodian->user_id);
                $custodian->delete();
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
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    function export() {
    
        $range = Request::query('range');
        $departmentId = null;
        switch ($range) {
            case 0:
                $departmentId = Squad::find(Request::query('id'))->department_id;
                break;
            case 1:
                $departmentId = Grade::find(Request::query('id'))->department_id;
                break;
            default:
                break;
        }
        $custodianIds = $departmentId
            ? $this->custodianIds($departmentId)
            : $this->contactIds('custodian');
        $custodians = $this->whereIn('id', $custodianIds)->get();
        $records = [self::EXCEL_EXPORT_TITLE];
        foreach ($custodians as $custodian) {
            if (!$custodian->user) { continue; }
            $records[] = [
                $custodian->user->realname,
                $custodian->user->gender == Constant::ENABLED ? '男' : '女',
                $custodian->user->email,
                implode(', ', $custodian->user->mobiles->pluck('mobile')->toArray()),
                $custodian->created_at,
                $custodian->updated_at,
            ];
        }
        
        return $this->excel($records);
        
    }
    
    /**
     * 返回指定年级和班级对应的学生列表
     *
     * @return JsonResponse
     */
    function csList() {
        
        abort_if(
            !Request::input('field') ||
            !Request::input('id') ||
            !in_array(Request::input('field'), ['grade', 'class']),
            HttpStatusCode::NOT_ACCEPTABLE,
            __('messages.not_acceptable')
        );
        $id = Request::input('id');
        $result = [];
        if (Request::input('field') == 'grade') {
            list($classes, $classId) = (new Grade)->classList($id);
            $result['html'] = [
                'classes'  => $classes,
                'students' => (new Squad)->studentList($classId),
            ];
        } else {
            $result['html']['students'] = (new Squad)->studentList($id);
        }
        
        return response()->json($result);
        
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
                'db'        => 'CustodianStudent.student_id', 'dt' => 2,
                'formatter' => function ($d) {
                    return Student::find($d)->user->realname;
                },
            ],
            ['db' => 'User.email', 'dt' => 3],
            ['db'        => 'User.gender', 'dt' => 4,
             'formatter' => function ($d) {
                 return $d == 1 ? Snippet::MALE : Snippet::FEMALE;
             },
            ],
            ['db'        => 'Custodian.id as mobile', 'dt' => 5,
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
                'db'        => 'User.enabled', 'dt' => 8,
                'formatter' => function ($d, $row) {
                    return $this->syncStatus($d, $row);
                },
            ],
            ['db' => 'User.synced', 'dt' => 9],
            ['db' => 'User.subscribed', 'dt' => 10],
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
            [
                'table'      => 'custodians_students',
                'alias'      => 'CustodianStudent',
                'type'       => 'INNER',
                'conditions' => [
                    'CustodianStudent.custodian_id = Custodian.id',
                ],
            ],
            [
                'table'      => 'students',
                'alias'      => 'Student',
                'type'       => 'INNER',
                'conditions' => [
                    'Student.id = CustodianStudent.student_id',
                ],
            ],
        ];
        $condition = 'Custodian.id IN (' . implode(',', $this->contactIds('custodian')) . ')';
        
        return Datatable::simple(
            $this->getModel(), $columns, $joins, $condition
        );
        
    }
    
    /**
     * 获取指定监护人在指定企业绑定的学生列表
     *
     * @param null $userId
     * @param null $corpId
     * @return array
     */
    function myStudents($userId = null, $corpId = null) {
        
        $custodian = isset($userId) ? User::find($userId)->custodian : Auth::user()->custodian;
        $corpId = $corpId ?? session('corpId');
        $students = [];
        foreach ($custodian->students as $student) {
            if ($student->squad->grade->school->corp_id == $corpId) {
                $students[$student->id] = $student->user->realname;
            }
        }
        
        return $students;
        
    }
    
}
