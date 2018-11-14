<?php
namespace App\Models;

use App\Facades\Datatable;
use App\Helpers\{Constant, HttpStatusCode, ModelTrait, Snippet};
use Carbon\Carbon;
use Eloquent;
use Exception;
use Illuminate\Database\Eloquent\{Builder, Collection, Model, Relations\BelongsTo, Relations\BelongsToMany};
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\{Auth, DB, Request};
use Throwable;

/**
 * App\Models\Custodian 监护人
 *
 * @property int $id
 * @property int $user_id 监护人用户ID
 * @property int $singular 是否为单角色
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
 * @method static Builder|Custodian whereSingular($value)
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
                    $students = $this->find($d)->students->filter(
                        function (Student $student) {
                            return $student->squad
                                ? $student->squad->grade->school_id == $this->schoolId() : false;
                        }
                    );
                    $userIds = $students->isNotEmpty() ? $students->pluck('user_id')->toArray() : [0];
                    $realnames = User::whereIn('id', $userIds)->pluck('realname')->toArray();
    
                    return implode(',', $realnames);
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
                'db'        => 'Custodian.enabled', 'dt' => 10,
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
            $this->getModel(), $columns, $joins,
            'Custodian.user_id IN (' . $this->visibleUserIds() . ')'
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
                $this->storeProperties($custodian, $data);
                # 如果同时也是教职员工
                if (!$data['singular']) {
                    $schoolId = $this->schoolId();
                    $groupId = Group::where([
                        'school_id' => $schoolId,
                        'name' => '教职员工'
                    ])->first()->id;
                    $user->update(['group_id' => $groupId]);
                    # 创建教职员工(Educator)记录
                    Educator::create([
                        'user_id' => $user->id,
                        'school_id' => $schoolId,
                        'enabled' => Constant::DISABLED
                    ]);
                }
                # 创建企业号成员
                $user->sync($user->id, 'create');
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
        
        try {
            DB::transaction(function () use ($data, $id) {
                if ($id) {
                    $custodian = $this->find($id);
                    # 更新用户数据
                    User::find($custodian->user_id)->update($data['user']);
                    # 更新监护人记录
                    $custodian->update($data);
                    # 更新监护人用户&部门绑定关系、监护关系、手机号码
                    $this->storeProperties($custodian, $data);
                    # 如果同时也是教职员工
                    $educator = $custodian->user->educator;
                    if (!$data['singular']) {
                        $educator ?: Educator::create([
                            'user_id' => $custodian->user_id,
                            'school_id' => $this->schoolId(),
                            'enabled' => Constant::DISABLED
                        ]);
                    } else {
                        !$educator ?: (new Educator)->purge($educator->id);
                    }
                    # 更新企业号会员数据
                    $custodian->user->sync($custodian->user_id, 'update');
                } else {
                    $this->batchUpdateContact($this);
                }
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
        
        return (new User)->clean($this, $id);
        
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
                $cses = CustodianStudent::whereCustodianId($id)->get();
                $schoolId = $this->schoolId();
                $schoolCses = $cses->filter(function (CustodianStudent $cs) use ($schoolId) {
                    return $cs->student->squad->grade->school_id == $schoolId;
                });
                if ($cses->count() <= 1 || $schoolCses->count() == $cses->count()) {
                    CustodianStudent::whereCustodianId($id)->delete();
                    if ($custodian->singular) {
                        (new User)->remove($custodian->user_id);
                    }
                    $custodian->delete();
                } else {
                    CustodianStudent::whereCustodianId($id)
                        ->whereIn('student_id', $schoolCses->pluck('student_id')->toArray())
                        ->delete();
                }
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
        if ($departmentId) {
            $groupId = Group::whereName('监护人')->first()->id;
            $userIds = $this->userIds($departmentId, $groupId);
            $custodianIds = User::whereIn('id', $userIds)->with('custodian')
                ->get()->pluck('custodian.id')->toArray();
        } else {
            $custodianIds = $this->contactIds('custodian');
        }
        $custodians = $this->whereIn('id', $custodianIds)->get();
        $records = [self::EXCEL_EXPORT_TITLE];
        foreach ($custodians as $custodian) {
            if (!$custodian->user) continue;
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
     * @param null $id
     * @return array
     */
    function compose($id = null) {
    
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
        $custodianId = $id ?? Request::route('id');
        if ($custodianId && Request::method() == 'GET') {
            $custodian = $this->find($custodianId);
            $mobiles = $custodian ? $custodian->user->mobiles : null;
            $relations = CustodianStudent::whereCustodianId($custodianId)->get();
        }
        
        return [
            '新增监护关系', $grades, $classes, $students ?? [], $relations ?? [], $mobiles ?? []
        ];
        
    }
    
    /**
     * 保存家长&学生 、家长&部门绑定关系数据
     *
     * @param Custodian $custodian
     * @param array $data
     * @throws Throwable
     */
    function storeProperties(Custodian $custodian, array $data) {
    
        # 更新监护人&部门绑定关系
        (new DepartmentUser)->storeByUserId(
            $custodian->user_id,
            $data['departmentIds'] ?? [],
            true
        );
        # 更新监护人&学生关系
        (new CustodianStudent)->storeByCustodianId(
            $custodian->id,
            $data['relationships'] ?? []
        );
        # 更新监护人手机号码
        (new Mobile)->store(
            $data['mobile'],
            $custodian->user->id
        );
        
    }
    
}
