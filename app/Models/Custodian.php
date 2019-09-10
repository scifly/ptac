<?php
namespace App\Models;

use App\Facades\Datatable;
use App\Helpers\{HttpStatusCode, ModelTrait, Snippet};
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
 * @method static Builder|Custodian newModelQuery()
 * @method static Builder|Custodian newQuery()
 * @method static Builder|Custodian query()
 * @mixin Eloquent
 */
class Custodian extends Model {
    
    use ModelTrait;
    
    const EXCEL_TITLES = [
        '监护人姓名', '性别', '电子邮箱',
        '手机号码', '创建于', '更新于',
    ];
    
    protected $fillable = ['user_id', 'enabled'];
    
    /** Properties -------------------------------------------------------------------------------------------------- */
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
            'custodian_student',
            'custodian_id',
            'student_id'
        );
        
    }
    
    /** crud -------------------------------------------------------------------------------------------------------- */
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
                    
                    return join(',', $realnames);
                },
            ],
            ['db' => 'User.mobile', 'dt' => 5],
            ['db' => 'Custodian.created_at', 'dt' => 6, 'dr' => true],
            ['db' => 'Custodian.updated_at', 'dt' => 7, 'dr' => true],
            [
                'db'        => 'Custodian.enabled', 'dt' => 8,
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
        ];
        
        return Datatable::simple(
            $this, $columns, $joins,
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
                $user = User::create($data['user']);
                (new Card)->store($user);
                $data['user_id'] = $user->id;
                $custodian = $this->create($data);
                # 保存绑定关系
                $this->bindings($custodian, $data);
                # 如果同时也是教职员工
                if (!$data['singular']) {
                    $schoolId = $this->schoolId();
                    $groupId = Group::where([
                        'school_id' => $schoolId,
                        'name'      => '教职员工',
                    ])->first()->id;
                    $user->update(['group_id' => $groupId]);
                    # 创建教职员工(Educator)记录
                    Educator::create(
                        array_combine(
                            (new Educator)->getFillable(),
                            [$user->id, $schoolId, 0, 1,]
                        )
                    );
                }
                # 创建企业微信成员
                $user->sync([[$user->id, '监护人', 'create']]);
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
                $ids = $id ? [$id] : array_values(Request::input('ids'));
                if (!$id) {
                    $this->batch($this);
                } else {
                    $custodian = $this->find($id);
                    $user = $custodian->user;
                    $user->update($data['user']);
                    (new Card)->store($user);
                    $custodian->update($data);
                    # 保存绑定关系
                    $this->bindings($custodian, $data);
                    # 如果同时也是教职员工
                    $educator = $user->educator;
                    if (!$educator && !$data['singular']) {
                        Educator::create(
                            array_combine(
                                (new Educator)->getFillable(),
                                [$custodian->user_id, $this->schoolId(), 0, 1]
                            )
                        );
                    } elseif ($educator && $data['singular']) {
                        $educator->remove($educator->id);
                    }
                }
                # 同步企业微信
                (new User)->sync(
                    array_map(
                        function ($userId) { return [$userId, '监护人', 'update']; },
                        $this->whereIn('id', $ids)->pluck('user_id')->toArray()
                    )
                );
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
        
        try {
            DB::transaction(function () use ($id) {
                $ids = $id ? [$id] : array_values(Request::input('ids'));
                # 隶属于当前学校的学生id，监护人id，部门id，以及需要删除(部门绑定关系)的用户id
                list($sIds, $cIds, $dIds, $rUIds, $uUIds) = value(
                    function () use ($ids) {
                        foreach ($ids as $id) {
                            $custodian = $this->find($id);
                            $csCnt = CustodianStudent::whereCustodianId($id)->count();
                            (!$custodian->user->educator && $csCnt <= 1)
                                ? $rUIds[] = $custodian->user_id
                                : $uUIds[] = $custodian->user_id;
                            $students = $custodian->students->filter(
                                function (Student $student) {
                                    return $student->squad->grade->school_id == $this->schoolId();
                                }
                            );
                            if ($students->isNotEmpty()) {
                                $sIds = array_merge(
                                    $sIds ?? [],
                                    $students->pluck('id')->toArray()
                                );
                                $cIds[] = $id;
                                /** @var Student $student */
                                foreach ($students as $student) {
                                    $dIds[] = $student->squad->department_id;
                                }
                            }
                        }
                        
                        return array_map('array_unique', [
                            $sIds ?? [], $cIds ?? [], $dIds ?? [],
                            $rUIds ?? [], $uUIds ?? [],
                        ]);
                    }
                );
                $user = new User;
                # 删除用户 & 部门 / 监护人 & 学生绑定关系
                if (!empty($uUIds)) {
                    (new DepartmentUser)->where([
                        ['user_id', 'in', $uUIds],
                        ['department_id', 'in', $dIds],
                    ])->delete();
                    (new CustodianStudent)->where([
                        ['custodian_id', 'in', $cIds],
                        ['student_id', 'in', $sIds],
                    ])->delete();
                    Request::replace(['ids' => $uUIds]);
                    $user->update(['position' => '教职员工']);
                }
                # 删除用户 & 监护人
                if (!empty($rUIds)) {
                    Request::replace(['ids' => $rUIds]);
                    $user->remove();
                    Request::replace([
                        'ids' => $this->whereIn('user_id', $rUIds)->pluck('id')->toArray(),
                    ]);
                    $this->purge([class_basename($this), 'CustodianStudent'], 'custodian_id');
                }
            });
        } catch (Exception $e) {
            throw $e;
        }
        
        return true;
        
    }
    
    /**
     * 批量发卡
     *
     * @return JsonResponse|string
     * @throws Throwable
     */
    function issue() {
        
        $card = new Card;
        if (Request::has('sectionId')) {
            $snHtml = $card->input();
            $tpl = <<<HTML
                <tr>
                    <td>%s</td>
                    <td class="text-center">%s</td>
                    <td class="text-center">%s</td>
                    <td class="text-center">%s</td>
                    <td>$snHtml</td>
                </tr>
            HTML;
            $list = '';
            $i = 0;
            [$class, $contacts] = $this->custodians(Request::input('sectionId'));
            /** @var User $contact */
            foreach ($contacts as $contact) {
                $student = $this->student($contact, $class);
                $sn = $contact->card ? $contact->card->sn : null;
                $list .= sprintf(
                    $tpl,
                    $contact->id, $contact->realname,
                    $student->user->realname,
                    $student->sn, $contact->id,
                    $i, $sn
                );
                $i++;
            }
            
            return $list;
        }
        
        return $card->store(null, true);
        
    }
    
    /**
     *
     * 批量授权
     *
     * @return JsonResponse|string
     * @throws Throwable
     */
    function grant() {
        
        return (new Card)->grant('Custodian');
        
    }
    
    /**
     * 批量设置人脸识别
     *
     * @return bool|JsonResponse|string
     * @throws Throwable
     */
    function face() {
        
        try {
            $face = new Face;
            # 上传人脸照片
            if (Request::file('file')) return $face->import();
            # 返回指定部门联系人列表
            throw_if(
                !Request::has('sectionId'),
                new Exception(__('messages.bad_request'))
            );
            [$class, $users] = $this->custodians(Request::input('sectionId'));
            $list = '';
            $tpl = <<<HTML
                <tr>
                    <td>%s</td>
                    <td class="text-center">%s</td>
                    <td class="text-center">%s</td>
                    <td class="text-center">%s</td>
                    <td>%s</td><td>%s</td>
                    <td class="text-center">%s</td>
                </tr>
            HTML;
            $cameras = (new Camera)->cameras();
            /** @var User $user */
            foreach ($users as $user) {
                $student = $this->student($user, $class);
                $list .= sprintf(
                    $tpl,
                    $user->id, $user->realname, $student->user->realname, $student->sn,
                    $face->uploader($user), $face->selector($cameras, $user),
                    $face->state(
                        $user->face ? $user->face->state : 1,
                        $user->id
                    )
                );
            }
            
            return $list;
        } catch (Exception $e) {
            throw $e;
        }
        
    }
    
    /** Helper functions -------------------------------------------------------------------------------------------- */
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
        [$classes, $classId] = (new Grade)->classList($id);
        $result['html']['students'] = (new Squad)->studentList($classId);
        if (Request::input('field') == 'grade') {
            $result['html']['classes'] = $classes;
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
        
        $custodian = User::find($userId ?? Auth::id())->custodian;
        $corpId = $corpId ?? session('corpId');
        
        return $custodian->students->filter(
            function (Student $student) use ($corpId) {
                return $student->squad->grade->school->corp_id == $corpId;
            }
        )->pluck('user.realname', 'id')->toArray();
        
    }
    
    /**
     * 返回create/edit view使用的数据
     *
     * @param null $id
     * @return array
     */
    function compose($id = null) {
        
        $action = explode('/', Request::path())[1];
        switch ($action) {
            case 'index':
                $nil = collect([null => '全部']);
                [$grades, $classes] = $this->gcList();
                $data = [
                    'buttons' => [
                        'issue' => [
                            'id'    => 'issue',
                            'label' => '发卡',
                            'icon'  => 'fa fa-credit-card',
                        ],
                        'grant' => [
                            'id'    => 'grant',
                            'label' => '一卡通授权',
                            'icon'  => 'fa fa-credit-card',
                        ],
                        'face'  => [
                            'id'    => 'face',
                            'label' => '人脸设置',
                            'icon'  => 'fa fa-camera',
                        ],
                    ],
                    'batch'   => true,
                    'filter'  => true,
                    'titles'  => [
                        '#', '姓名', '头像',
                        [
                            'title' => '性别',
                            'html'  => $this->htmlSelect(
                                $nil->union(['女', '男']), 'filter_gender'
                            ),
                        ],
                        '学生', '手机号码',
                        [
                            'title' => '创建于',
                            'html'  => $this->htmlDTRange('创建于'),
                        ],
                        [
                            'title' => '更新于',
                            'html'  => $this->htmlDTRange('更新于'),
                        ],
                        [
                            'title' => '状态 . 操作',
                            'html'  => $this->htmlSelect(
                                $nil->union(['未启用', '已启用']), 'filter_enabled'
                            ),
                        ],
                    ],
                    'grades'  => $grades,
                    'classes' => $classes,
                    'title'   => '导出监护人',
                ];
                break;
            case 'issue':
            case 'face':
                $titles = <<<HTML
                    <th>#</th>
                    <th class="text-center">家长</th>
                    <th class="text-center">学生</th>
                    <th class="text-center">学号</th>
                HTML;
                $titles .= $action == 'issue'
                    ? '<th>卡号</th>'
                    : '<th>人脸</th><th>设备</th><th class="text-center">状态</th>';
                $classes = Squad::whereIn('id', $this->classIds())
                    ->get()->pluck('name', 'id')->toArray();
                $data = [
                    'prompt'  => '家长列表',
                    'formId'  => 'formCustodian',
                    'classes' => ['(请选择一个班级)'] + $classes,
                    'titles'  => $titles,
                    'columns' => 7,
                ];
                break;
            case 'grant':
                $data = (new Card)->compose('Custodian');
                break;
            default:    # 创建/编辑
                [$grades, $classes] = $this->gcList();
                $records = Student::with('user:id,realname')
                    ->where(['class_id' => array_key_first($classes), 'enabled' => 1])
                    ->get()->toArray();
                foreach ($records as $record) {
                    if (!isset($record['user'])) continue;
                    $students[$record['id']] = $record['user']['realname'] . '-' . $record['sn'];
                }
                $custodian = $this->find($id ?? Request::route('id'));
                if ($custodian && Request::method() == 'GET') {
                    $custodian->{'card'} = $custodian->user->card;
                    $custodian->user->ent_attrs = json_decode(
                        $custodian->user->ent_attrs, true
                    );
                    $relations = CustodianStudent::whereCustodianId($custodian->id)->get()->filter(
                        function (CustodianStudent $cs) {
                            return $this->schoolId() == Student::find($cs->student_id)->squad->grade->school_id;
                        }
                    );
                }
                $data = array_merge(
                    array_combine(
                        [
                            'custodian', 'title', 'grades', 'classes',
                            'students', 'relations', 'relationship',
                        ],
                        [
                            $custodian, '新增监护关系',
                            $grades, $classes, $students ?? [],
                            $relations ?? collect([]), true,
                        ]
                    )
                );
                break;
        }
        
        return $data;
        
    }
    
    /**
     * 返回对当前登录用户可见的年级与班级列表
     *
     * @return array
     */
    private function gcList() {
        
        $grades = Grade::whereIn('id', $this->gradeIds())
            ->where('enabled', 1)
            ->pluck('name', 'id')->toArray();
        $classes = Squad::where([
            'grade_id' => array_key_first($grades),
            'enabled'  => 1,
        ])->pluck('name', 'id')->toArray();
        
        return [$grades, $classes];
        
    }
    
    /**
     * @param $classId
     * @return array
     */
    private function custodians($classId) {
        
        $class = Squad::find($classId);
        $userIds = DepartmentUser::whereDepartmentId($class->department_id)
            ->pluck('user_id')->toArray();
        
        return [
            $class,
            User::whereIn('id', $userIds)->get()->filter(
                function (User $user) { return $user->group->name == '监护人'; }
            ),
        ];
        
    }
    
    /**
     * 保存家长&学生 、家长&部门绑定关系数据
     *
     * @param Custodian $custodian
     * @param array $data
     * @throws Throwable
     */
    private function bindings(Custodian $custodian, array $data) {
        
        # 更新监护人&部门绑定关系
        (new DepartmentUser)->storeByUserId(
            $custodian->user_id,
            $data['departmentIds'] ?? [],
            true
        );
        # 更新监护人&学生关系
        (new CustodianStudent)->store($custodian->id, $data['relationships'] ?? []);
        
    }
    
    /**
     * @param User $user
     * @param Squad $class
     * @return mixed
     */
    private function student(User $user, Squad $class) {
        
        $students = $user->custodian->students;
        if ($students->count() > 1) {
            $students = $students->filter(
                function (Student $student) use ($class) {
                    return $student->class_id == $class->id;
                }
            );
        }
        
        return $students->first();
        
    }
    
}
