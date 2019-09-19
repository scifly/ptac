<?php
namespace App\Models;

use App\Facades\Datatable;
use App\Helpers\{HttpStatusCode, ModelTrait};
use App\Jobs\{ExportEducator, ImportEducator};
use Carbon\Carbon;
use Eloquent;
use Exception;
use Illuminate\Database\Eloquent\{Builder,
    Collection,
    Model,
    Relations\BelongsTo,
    Relations\BelongsToMany,
    Relations\HasMany};
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection as SCollection;
use Illuminate\Support\Facades\{Auth, DB, Request};
use PhpOffice\PhpSpreadsheet\Exception as PssException;
use PhpOffice\PhpSpreadsheet\Reader\Exception as PssrException;
use ReflectionException;
use Throwable;

/**
 * App\Models\Educator 教职员工
 *
 * @property int $id
 * @property int $user_id 教职员工用户ID
 * @property int $school_id 所属学校ID
 * @property int $sms_balance 可用短信条数
 * @property int $sms_used 已使用短信条数
 * @property int $singular 是否为单角色
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int $enabled
 * @property-read Collection|Squad[] $classes
 * @property-read School $school
 * @property-read Collection|Tag[] $tags
 * @property-read User $user
 * @property-read int|null $classes_count
 * @property-read Collection|Evaluate[] $evals
 * @property-read int|null $evals_count
 * @property-read Collection|Participant[] $participants
 * @property-read int|null $participants_count
 * @method static Builder|Educator whereCreatedAt($value)
 * @method static Builder|Educator whereEnabled($value)
 * @method static Builder|Educator whereId($value)
 * @method static Builder|Educator whereSchoolId($value)
 * @method static Builder|Educator whereSmsBalance($value)
 * @method static Builder|Educator whereSmsUsed($value)
 * @method static Builder|Educator whereUpdatedAt($value)
 * @method static Builder|Educator whereUserId($value)
 * @method static Builder|Educator whereSingular($value)
 * @method static Builder|Educator newModelQuery()
 * @method static Builder|Educator newQuery()
 * @method static Builder|Educator query()
 * @mixin Eloquent
 */
class Educator extends Model {
    
    use ModelTrait;
    
    const EXCEL_TITLES = [
        '姓名', '性别', '员工编号', '职务', '部门',
        '手机号码', '年级主任', '班级主任', '班级科目',
    ];
    protected $fillable = [
        'user_id', 'school_id', 'sms_balance', 'sms_used', 'enabled',
    ];
    
    /** @return BelongsTo */
    function user() { return $this->belongsTo('App\Models\User'); }
    
    /** @return BelongsTo */
    function school() { return $this->belongsTo('App\Models\School'); }
    
    /** @return BelongsToMany */
    function classes() { return $this->belongsToMany('App\Models\Squad', 'class_educator'); }
    
    /** @return HasMany */
    function evals() { return $this->hasMany('App\Models\Evaluate'); }
    
    /** @return HasMany */
    function participants() { return $this->hasMany('App\Models\Participant'); }
    
    /**
     * 教职员工列表
     *
     * @return array
     */
    function index() {
        
        $columns = [
            ['db' => 'Educator.id', 'dt' => 0],
            ['db' => 'User.realname', 'dt' => 1],
            [
                'db'        => 'User.avatar_url', 'dt' => 2,
                'formatter' => function ($d) {
                    return $this->avatar($d);
                },
            ],
            [
                'db'        => 'User.gender', 'dt' => 3,
                'formatter' => function ($d) {
                    return $this->gender($d);
                },
            ],
            ['db' => 'Groups.name', 'dt' => 4],
            ['db' => 'User.mobile', 'dt' => 5],
            ['db' => 'Educator.created_at', 'dt' => 6, 'dr' => true],
            ['db' => 'Educator.updated_at', 'dt' => 7, 'dr' => true],
            [
                'db'        => 'Educator.enabled', 'dt' => 8,
                'formatter' => function ($d, $row) {
                    $rechargeLink = $this->anchor(
                        'recharge_' . $row['id'],
                        '短信充值 & 查询',
                        'fa-money'
                    );
                    
                    return Datatable::status($d, $row, false) .
                        (Auth::user()->can('act', self::uris()['recharge']) ? $rechargeLink : '');
                },
            ],
        ];
        $joins = [
            [
                'table'      => 'users',
                'alias'      => 'User',
                'type'       => 'LEFT',
                'conditions' => [
                    'User.id = Educator.user_id',
                ],
            ],
            [
                'table'      => 'groups',
                'alias'      => 'Groups',
                'type'       => 'INNER',
                'conditions' => [
                    'Groups.id = User.group_id',
                ],
            ],
        ];
        // $condition = 'Educator.user_id IN (%s) AND Educator.school_id = %s';
        $condition = 'Educator.school_id = ' . $this->schoolId();
        
        return Datatable::simple(
            $this, $columns, $joins, $condition
        // sprintf($condition, $this->visibleUserIds(), $this->schoolId())
        );
        
    }
    
    /**
     * 保存职员工
     *
     * @param array $data
     * @return bool|mixed
     * @throws Throwable
     */
    function store(array $data) {
        
        try {
            DB::transaction(function () use ($data) {
                # 用户
                $data['user']['password'] = bcrypt($data['user']['password']);
                $user = User::create($data['user']);
                # 一卡通
                (new Card)->store($user);
                # 教职员工
                $data['user_id'] = $user->id;
                $educator = $this->create($data);
                # 绑定关系
                $this->bindings($educator, $data);
                # 如果同时也是监护人
                if (!$data['singular']) {
                    Custodian::create(
                        array_combine(
                            ['user_id', 'enabled'],
                            [$user->id, $educator->enabled]
                        )
                    );
                }
                # 创建企业微信会员
                $user->sync([[$user->id, '', 'create']]);
            });
        } catch (Exception $e) {
            throw $e;
        }
        
        return true;
        
    }
    
    /**
     * 更新教职员工
     *
     * @param array $data
     * @param $id
     * @return bool|mixed
     * @throws Throwable
     */
    function modify(array $data, $id = null) {
        
        try {
            DB::transaction(function () use ($data, $id) {
                $ids = $id ? [$id] : array_values(Request::input('ids'));
                if (!$id) {
                    $this->batch($this);
                } else {
                    ($educator = $this->find($id))->update($data);
                    # 用户
                    ($user = $educator->user)->update($data['user']);
                    # 一卡通
                    (new Card)->store($user);
                    # 绑定关系
                    $this->bindings($educator, $data);
                    # 如果同时也是监护人
                    $custodian = $user->custodian;
                    if (!$data['singular'] && !$custodian) {
                        Custodian::create(
                            array_combine(
                                ['user_id', 'enabled'],
                                [$educator->user_id, $educator->enabled]
                            )
                        );
                    } elseif ($data['singular'] && $custodian) {
                        $custodian->remove($custodian->id);
                    }
                }
                # 同步企业微信
                (new User)->sync(
                    array_map(
                        function ($userId) { return [$userId, '', 'update']; },
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
     * 短信充值
     *
     * @param $id
     * @param array $data
     * @return JsonResponse
     * @throws Throwable
     */
    function recharge($id, array $data) {
        
        return (new SmsCharge)->recharge(
            $this, $id, $data
        );
        
    }
    
    /**
     * 删除教职员工
     *
     * @param $id
     * @return bool
     * @throws Throwable
     */
    function remove($id = null) {
        
        try {
            DB::transaction(function () use ($id) {
                $ids = $id ? [$id] : array_values(Request::input('ids'));
                $user = new User;
                [$rUIds, $uUIds] = value(
                    function () use ($ids, $user) {
                        $uIds = $this->whereIn('id', $ids)
                            ->pluck('user_id')->toArray();
                        $rUIds = $user->whereIn('id', $uIds)->get()
                            ->filter(function (User $user) { return !$user->custodian; })
                            ->pluck('id')->toArray();
                        $uUIds = array_diff($uIds, $rUIds);
                        
                        return [$rUIds, $uUIds];
                    }
                );
                # 更新同时也是监护人的用户
                if (!empty($uUIds)) {
                    # 删除部门绑定关系
                    (new DepartmentUser)->where([
                        ['user_id', 'in', $uUIds],
                        ['enabled', '=', 1],
                    ])->delete();
                    Request::replace(['ids' => $uUIds]);
                    $cGId = Group::whereName('监护人')->first()->id;
                    $user->modify(['group_id' => $cGId]);
                }
                # 删除用户
                if (!empty($rUIds)) {
                    Request::replace(['ids' => $rUIds]);
                    $user->remove();
                }
                # 删除教职员工
                Request::replace(['ids' => $ids]);
                $this->purge(['Educator', 'Participant', 'ClassEducator', 'Evaluate'], 'educator_id');
            });
        } catch (Exception $e) {
            throw $e;
        }
        
        return true;
        
    }
    
    /**
     * 导入教职员工
     *
     * @return bool
     * @throws PssException
     * @throws PssrException
     */
    function import() {
        
        $records = $this->uploader();
        $mobiles = array_count_values(
            array_map('strval', Arr::pluck($records, 'G'))
        );
        foreach ($mobiles as $mobile => $count) {
            $count <= 1 ?: $duplicates[] = $mobile;
        }
        abort_if(
            isset($duplicates),
            HttpStatusCode::NOT_ACCEPTABLE,
            join('', [
                '手机号码',
                join(',', $duplicates ?? []),
                '有重复，请检查后重试。',
            ])
        );
        abort_if(
            !$group = Group::where(['name' => '教职员工', 'school_id' => $this->schoolId()])->first(),
            HttpStatusCode::NOT_ACCEPTABLE, __('messages.educator.role_nonexistent')
        );
        ImportEducator::dispatch(
            $records, $this->schoolId(), $group->id, Auth::id()
        );
        
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
            $users = $this->users(Request::input('sectionId'));
            $snHtml = $card->input();
            $tpl = <<<HTML
                <tr>
                    <td>%s</td>
                    <td class="text-center">%s</td>
                    <td class="text-center">%s</td>
                    <td>$snHtml</td>
                </tr>
            HTML;
            $list = '';
            $i = 0;
            foreach ($users as $user) {
                $card = $user->card;
                $sn = $card ? $card->sn : null;
                $list .= sprintf(
                    $tpl,
                    $user->id, $user->realname, $user->username,
                    $user->id, $i, $sn
                );
                $i++;
            }
            
            return $list;
        }
        
        return $card->store(null, true);
        
    }
    
    /**
     * 批量授权
     *
     * @return JsonResponse|string
     * @throws Throwable
     */
    function grant() {
        
        return (new Card)->grant('Educator');
        
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
            if (Request::file()) return $face->import();
            # 返回指定部门联系人列表
            throw_if(
                !Request::has('sectionId'),
                new Exception(__('messages.bad_request'))
            );
            $users = $this->users(Request::input('sectionId'));
            $tpl = <<<HTML
                <tr>
                    <td>%s</td>
                    <td class="text-center">%s</td>
                    <td class="text-center">%s</td>
                    <td>%s</td><td>%s</td>
                    <td class="text-center">%s</td>
                </tr>
            HTML;
            $list = '';
            $cameras = (new Camera)->cameras();
            /** @var User $user */
            foreach ($users as $user) {
                $list .= sprintf(
                    $tpl,
                    $user->id, $user->realname, $user->username,
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
    
    /**
     * 批量导出教职员工
     *
     * @return bool
     * @throws ReflectionException
     */
    function export() {
        
        if (Request::input('range') == 0) {
            $userIds = $this->userIds(Request::input('id'), 'educator');
            $ids = User::with('educator')->whereIn('id', $userIds)
                ->get()->pluck('educator.id');
        } else {
            $ids = $this->contactIds('educator');
        }
        $educators = $this->whereIn('id', $ids)
            ->where('school_id', $this->schoolId())->get();
        ExportEducator::dispatch($educators, self::EXCEL_TITLES, Auth::id());
        
        return true;
        
    }
    
    /** Helper functions -------------------------------------------------------------------------------------------- */
    /**
     * 返回composer所需的view数据
     *
     * @param null $id
     * @return array
     */
    function compose($id = null) {
        
        $action = explode('/', Request::path())[1];
        switch ($action) {
            case 'index':
                $nil = collect([null => '全部']);
                $departments = Department::whereIn('id', $this->departmentIds(Auth::id()))
                    ->pluck('name', 'id')->toArray();
                $data = [
                    'buttons'        => [
                        'import' => [
                            'id'    => 'import',
                            'label' => '批量导入',
                            'icon'  => 'fa fa-upload',
                        ],
                        'export' => [
                            'id'    => 'export',
                            'label' => '批量导出',
                            'icon'  => 'fa fa-download',
                        ],
                        'issue'  => [
                            'id'    => 'issue',
                            'label' => '发卡',
                            'icon'  => 'fa fa-credit-card',
                        ],
                        'grant'  => [
                            'id'    => 'grant',
                            'label' => '一卡通授权',
                            'icon'  => 'fa fa-credit-card',
                        ],
                        'face'   => [
                            'id'    => 'face',
                            'label' => '人脸设置',
                            'icon'  => 'fa fa-camera',
                        ],
                    ],
                    'batch'          => true,
                    'filter'         => true,
                    'titles'         => [
                        '#', '姓名', '头像',
                        [
                            'title' => '性别',
                            'html'  => $this->htmlSelect(
                                $nil->union(['女', '男']), 'filter_gender'
                            ),
                        ],
                        '职务', '手机号码',
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
                    'departments'    => $departments,
                    'importTemplate' => 'files/educators.xlsx',
                    'title'          => '导出教职员工',
                ];
                break;
            case 'issue':
            case 'face':
                $titles = <<<HTML
                    <th>#</th>
                    <th class="text-center">姓名</th>
                    <th class="text-center">员工编号/用户名</th>
                HTML;
                $titles .= $action == 'issue'
                    ? '<th>卡号</th>'
                    : '<th>人脸</th><th>设备</th><th class="text-center">状态</th>';
                $departments = Department::whereIn('id', $this->departmentIds())
                    ->pluck('name', 'id')->toArray();
                $data = [
                    'prompt'  => '教师列表',
                    'formId'  => 'formEducator',
                    'classes' => ['(请选择一个部门)'] + $departments,
                    'titles'  => $titles,
                    'columns' => 6,
                ];
                break;
            case 'grant':
                $data = (new Card)->compose('Educator');
                break;
            case 'recharge':
                $data = (new Message)->compose('recharge');
                break;
            default:    # 创建/编辑
                $classes = Squad::whereIn('id', $this->classIds())->where('enabled', 1)->get();
                $gradeIds = array_unique($classes->pluck('grade_id')->toArray());
                $subjects = Subject::where(['enabled' => 1, 'school_id' => $this->schoolId()])->get()->filter(
                    function (Subject $subject) use ($gradeIds) {
                        return !empty(array_intersect($gradeIds, explode(',', $subject->grade_ids)));
                    }
                );
                ;
                if (($educator = $this->find($id ?? Request::route('id'))) && Request::method() == 'GET') {
                    $user = $educator->user;
                    $educator->{'card'} = $user->card;
                    $selectedDepartmentIds = !$educator ? []
                        : $educator->user->deptIds($educator->user_id);
                    $selectedDepartments = $this->selectedNodes($selectedDepartmentIds);
                }
                $nil = collect([0 => '(请选择)']);
                $data = array_merge(
                    array_combine(
                        [
                            'educator', 'squads', 'subjects', 'groups',
                            'selectedDepartmentIds', 'selectedDepartments',
                        ],
                        [
                            $educator,
                            $nil->union($classes->pluck('name', 'id')),
                            $nil->union($subjects->pluck('name', 'id')),
                            (new Group)->list(),
                            join(',', $selectedDepartmentIds ?? []),
                            $selectedDepartments ?? [],
                        ]
                    ),
                    (new Tag)->compose('user', $user ?? null)
                );
                break;
        }
        
        return $data;
        
    }
    
    /**
     * 选中的部门节点
     *
     * @param $departmentIds
     * @return array
     */
    private function selectedNodes($departmentIds) {
        
        $departments = Department::whereIn('id', $departmentIds)->get();
        foreach ($departments as $department) {
            $departmentType = DepartmentType::find($department['department_type_id']);
            $nodes[] = [
                'id'     => $department->id,
                'parent' => $department->parent_id ?? '#',
                'text'   => $department->name,
                'icon'   => $departmentType->icon,
                'type'   => $departmentType->remark,
            ];
        }
        
        return $nodes ?? [];
        
    }
    
    /**
     * @param $deptId
     * @return SCollection
     */
    private function users($deptId) {
        
        $userIds = DepartmentUser::whereDepartmentId($deptId)
            ->pluck('user_id')->toArray();
        
        return User::whereIn('id', $userIds)->get()->filter(
            function (User $user) {
                return !in_array($user->group->name, ['监护人', '学生']);
            }
        );
        
    }
    
    /**
     * 保存绑定关系(任教班级科目、部门用户、标签用户)
     *
     * @param Educator $educator
     * @param array $data
     * @throws Throwable
     */
    private function bindings(Educator $educator, array $data) {
        
        # 班级科目绑定关系
        (new ClassEducator)->storeByEducatorId($educator->id, $data['cs']);
        # 部门用户绑定关系
        (new DepartmentUser)->storeByUserId($educator->user_id, $data['selectedDepartments']);
        # 标签用户绑定关系
        (new TagUser)->storeByUserId($educator->user_id, $data['tag_ids'] ?? []);
        
    }
    
}

