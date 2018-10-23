<?php
namespace App\Models;

use App\Facades\Datatable;
use App\Helpers\Constant;
use App\Helpers\HttpStatusCode;
use App\Helpers\ModelTrait;
use App\Helpers\Snippet;
use Carbon\Carbon;
use Eloquent;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;
use Throwable;

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
    
    protected $fillable = ['user_id', 'singular', 'enabled'];
    
    /**
     * 返回对应的用户对象
     *
     * @return BelongsTo
     */
    function user() { return $this->belongsTo('App\Models\User'); }
    
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
     * 监护人记录列表
     *
     * @return array
     */
    function index() {
        
        $columns = [
            ['db' => 'Custodian.id', 'dt' => 0],
            ['db' => 'User.realname', 'dt' => 1],
            [
                'db'        => 'User.avatar_url', 'dt' => 2,
                'formatter' => function ($d) {
                    return Snippet::avatar($d);
                },
            ],
            [
                'db'        => 'User.gender', 'dt' => 3,
                'formatter' => function ($d) {
                    return $d ? Snippet::MALE : Snippet::FEMALE;
                },
            ],
            [
                'db'        => 'Custodian.id as students', 'dt' => 4,
                'formatter' => function ($d) {
                    $students = $this->find($d)->students;
                    $studentUserIds = $students->isNotEmpty() ? $students->pluck('user_id')->toArray() : [0];
                    return implode(',', User::whereIn('id', $studentUserIds)->pluck('realname')->toArray());
                },
            ],
            ['db' => 'Mobile.mobile', 'dt' => 5],
            ['db' => 'Custodian.created_at', 'dt' => 6, 'dr' => true],
            ['db' => 'Custodian.updated_at', 'dt' => 7, 'dr' => true],
            [
                'db' => 'User.synced', 'dt' => 8,
                'formatter' => function ($d) {
                    return $this->synced($d);
                }
            ],
            [
                'db' => 'User.subscribed', 'dt' => 9,
                'formatter' => function ($d) {
                    return $this->subscribed($d);
                }
            ],
            [
                'db'        => 'User.enabled', 'dt' => 10,
                'formatter' => function ($d, $row) {
                    return Datatable::status($d, $row, false);
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
            [
                'table' => 'mobiles',
                'alias' => 'Mobile',
                'type' => 'INNER',
                'conditions' => [
                    'User.id = Mobile.user_id',
                    'Mobile.isdefault = 1'
                ]
            ]
        ];
        
        return Datatable::simple(
            $this->getModel(), $columns, $joins, $this->contactCondition('监护人')
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
                $user = User::create($data['user']);
                # 创建监护人(Custodian) 记录
                $data['user_id'] = $user->id;
                $custodian = $this->create($data);
                # 保存监护人用户&部门绑定关系、监护关系、手机号码
                $this->updateProperties($custodian, $data);
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
        
        if (!$id) { return $this->batchUpdateContact($this); }
        try {
            DB::transaction(function () use ($data, $id) {
                $custodian = $this->find($id);
                # 更新用户数据
                User::find($custodian->user_id)->update($data['user']);
                # 更新监护人记录
                $custodian->update($data);
                # 更新监护人用户&部门绑定关系、监护关系、手机号码
                $this->updateProperties($custodian, $data);
                # 更新企业号会员数据
                $custodian->user->UpdateWechatUser($custodian->user_id);
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
     * @param bool $broadcast
     * @return bool
     * @throws Throwable
     */
    function purge($id, $broadcast = true) {
        
        try {
            DB::transaction(function () use ($id, $broadcast) {
                $custodian = $this->find($id);
                CustodianStudent::whereCustodianId($id)->delete();
                (new User)->remove($custodian->user_id, $broadcast);
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
     * @return array|bool
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
            if (!$custodian->user) {
                continue;
            }
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
    
    /**
     * 返回create/edit view使用的数据
     *
     * @return array
     */
    function compose() {
    
        $grades = Grade::whereIn('id', $this->gradeIds())
            ->where('enabled', 1)
            ->pluck('name', 'id')
            ->toArray();
        reset($grades);
        $classes = Squad::where(['grade_id' => key($grades), 'enabled' => 1])
            ->pluck('name', 'id')->toArray();
        reset($classes);
        $records = Student::with('user:id,realname')
            ->where(['class_id' => key($classes), 'enabled' => 1])
            ->get()->toArray();
        foreach ($records as $record) {
            if (!isset($record['user'])) continue;
            $students[$record['id']] = $record['user']['realname'] . '-' . $record['card_number'];
        }
        if (Request::route('id') && Request::method() == 'GET') {
            $mobiles = $this->find(Request::route('id'))->user->mobiles;
            $relations = CustodianStudent::whereCustodianId(Request::route('id'))->get();
        }
        
        return [
            $grades, $classes, $students ?? [], $relations ?? [], $mobiles ?? []
        ];
        
    }
    
    /**
     * 析取家长&学生 、家长&部门绑定关系数据
     *
     * @param Custodian $custodian
     * @param array $data
     * @throws Throwable
     */
    private function updateProperties(Custodian $custodian, array $data) {
    
        $mobiles = $data['mobile'];
        $rses = $departmentIds = [];
        foreach ($data['student_ids'] as $key => $sId) {
            $student = Student::find($sId);
            abort_if(!$student, HttpStatusCode::NOT_FOUND, '找不到学生id: ' . $sId . '对应的记录');
            (new DepartmentUser)->store(
                $custodian->user_id,
                $student->squad->department_id
            );
            $rses[$sId] = $data['relationships'][$key];
        }
        # 更新监护人&部门绑定关系
        (new DepartmentUser)->storeByUserId($custodian->user_id, $departmentIds);
        # 更新监护人&学生关系
        (new CustodianStudent)->storeByCustodianId($custodian->id, $rses);
        # 更新监护人手机号码
        (new Mobile)->store($mobiles, $custodian->user->id);
        
    }
    
}
