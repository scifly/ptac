<?php
namespace App\Models;

use App\Facades\Datatable;
use App\Helpers\{Constant, HttpStatusCode, ModelTrait, Snippet};
use App\Jobs\ExportEducator;
use App\Jobs\ImportEducator;
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
use Illuminate\Support\Facades\{Auth, DB, Request};
use ReflectionException;
use Throwable;

/**
 * App\Models\Educator 教职员工
 *
 * @property int $id
 * @property int $user_id 教职员工用户ID
 * @property int $school_id 所属学校ID
 * @property int $sms_quote 可用短信条数
 * @property int $singular 是否为单角色
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int $enabled
 * @property-read Collection|Squad[] $classes
 * @property-read School $school
 * @property-read Collection|Tag[] $tags
 * @property-read User $user
 * @property-read Collection|EducatorClass[] $educatorClasses
 * @method static Builder|Educator whereCreatedAt($value)
 * @method static Builder|Educator whereEnabled($value)
 * @method static Builder|Educator whereId($value)
 * @method static Builder|Educator whereSchoolId($value)
 * @method static Builder|Educator whereSmsQuote($value)
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
        '学校', '手机号码', '年级主任', '班级主任', '班级科目',
    ];
    protected $fillable = [
        'user_id', 'team_ids', 'school_id',
        'position', 'sms_quote', 'enabled',
    ];
    
    /**
     * 返回指定教职员工对应的用户对象
     *
     * @return BelongsTo
     */
    function user() { return $this->belongsTo('App\Models\User'); }
    
    /**
     * 返回指定教职员工所属的学校对象
     *
     * @return BelongsTo
     */
    function school() { return $this->belongsTo('App\Models\School'); }
    
    /**
     * 获取指定教职员工所属的所有班级对象
     *
     * @return BelongsToMany
     */
    function classes() {
        
        return $this->belongsToMany(
            'App\Models\Squad',
            'educators_classes',
            'educator_id',
            'class_id'
        );
        
    }
    
    /**
     * 获取指定教职员工所属的所管理班级科目对象
     *
     * @return HasMany
     */
    function educatorClasses() {
        
        return $this->hasMany(
            'App\Models\EducatorClass',
            'educator_id',
            'id'
        );
        
    }
    
    /**
     * 返回教职员工列表
     *
     * @param array $ids
     * @return array
     */
    function educatorList(array $ids) {
        
        return $this->with('user')->whereIn('id', $ids)->get()
            ->pluck('user.realname', 'id')->toArray();
        
    }
    
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
                    return Snippet::avatar($d);
                },
            ],
            [
                'db'        => 'User.gender', 'dt' => 3,
                'formatter' => function ($d) {
                    return Snippet::gender($d);
                },
            ],
            ['db' => 'User.position', 'dt' => 4],
            ['db' => 'Mobile.mobile', 'dt' => 5],
            ['db' => 'Educator.created_at', 'dt' => 6, 'dr' => true],
            ['db' => 'Educator.updated_at', 'dt' => 7, 'dr' => true],
            [
                'db'        => 'User.synced', 'dt' => 8,
                'formatter' => function ($d) {
                    return $this->synced($d);
                },
            ],
            [
                'db'        => 'User.subscribed', 'dt' => 9,
                'formatter' => function ($d) {
                    return $this->subscribed($d);
                },
            ],
            [
                'db'        => 'Educator.enabled', 'dt' => 10,
                'formatter' => function ($d, $row) {
                    $rechargeLink = sprintf(
                        Snippet::DT_ANCHOR,
                        'recharge_' . $row['id'],
                        '充值', 'fa-money'
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
                'table'      => 'mobiles',
                'alias'      => 'Mobile',
                'type'       => 'LEFT',
                'conditions' => [
                    'User.id = Mobile.user_id',
                    'Mobile.isdefault = 1',
                ],
            ],
        ];
        $condition = 'Educator.user_id IN (%s) AND Educator.school_id = %s';
        
        return Datatable::simple(
            $this, $columns, $joins,
            sprintf($condition, $this->visibleUserIds(), $this->schoolId())
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
                (new Card)->store($user);
                # 教职员工
                $data['user_id'] = $user->id;
                $educator = $this->create($data);
                # 班级科目绑定关系
                (new EducatorClass)->storeByEducatorId($educator->id, $data['cs']);
                # 部门用户绑定关系
                (new DepartmentUser)->storeByUserId($user->id, $data['selectedDepartments']);
                # 手机号码
                (new Mobile)->store($data['mobile'], $user->id);
                # 如果同时也是监护人
                $data['singular'] ?: $custodian = Custodian::create([
                    'user_id' => $user->id,
                    'enabled' => Constant::ENABLED,
                ]);
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
                    $educator = $this->find($id);
                    $user = $educator->user;
                    $user->update($data['user']);
                    $educator->update($data);
                    (new Card)->store($user);
                    (new EducatorClass)->storeByEducatorId($educator->id, $data['cs']);
                    (new DepartmentUser)->storeByUserId($educator->user_id, $data['selectedDepartments']);
                    (new Mobile)->store($data['mobile'], $educator->user_id);
                    # 如果同时也是监护人
                    $custodian = $user->custodian;
                    if (!$data['singular']) {
                        $custodian ?: Custodian::create(
                            array_combine(Constant::CUSTODIAN_FIELDS, [
                                $educator->user_id, $educator->enabled
                            ])
                        );
                    } else {
                        !$custodian ?: (new Custodian)->remove($custodian->id);
                    }
                }
                # 同步企业微信
                (new User)->sync(
                    array_map(
                        function ($userId) {return [$userId, '', 'update']; },
                        $this->whereIn('id', $ids)->get()->pluck('user_id')->toArray()
                    )
                );
            });
        } catch (Exception $e) {
            throw $e;
        }
        
        return true;
        
    }
    
    /**
     * 短信条数充值
     *
     * @param $id
     * @param array $data
     * @return JsonResponse
     */
    function recharge($id, array $data) {
        
        $educator = $this->find($id);
        abort_if(
            !$educator,
            HttpStatusCode::NOT_FOUND,
            __('messages.educator.not_found')
        );
        $updated = $educator->update([
            'sms_quote' => $educator->sms_quote + $data['charge'],
        ]);
        abort_if(
            !$updated,
            HttpStatusCode::INTERNAL_SERVER_ERROR,
            __('messages.fail')
        );
        
        return response()->json([
            'title'   => __('messages.educator.title'),
            'message' => __('messages.ok'),
            'quote'   => $this->find($id)->sms_quote,
        ]);
        
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
                list($rUIds, $uUIds) = value(
                    function () use ($ids) {
                        $uIds = $this->whereIn('id', $ids)
                            ->pluck('user_id')->toArray();
                        $rUIds = User::whereIn('id', $uIds)->get()
                            ->filter(function (User $user) { return !$user->custodian; })
                            ->pluck('id')->toArray();
                        $uUIds = array_diff($uIds, $rUIds);
                        
                        return [$rUIds, $uUIds];
                    }
                );
                $user = new User;
                # 更新同时也是监护人的用户
                if (!empty($uUIds)) {
                    # 删除部门绑定关系
                    (new DepartmentUser)->where([
                        ['user_id', 'in', $uUIds],
                        ['enabled', '=', 1]
                    ])->delete();
                    Request::replace(['ids' => $uUIds]);
                    $user->modify(['group_id' => Group::whereName('监护人')->first()->id]);
                }
                # 删除用户
                if (!empty($rUIds)) {
                    Request::replace(['ids' => $rUIds]);
                    $user->remove();
                }
                # 删除教职员工
                Request::replace(['ids' => $ids]);
                $this->purge([
                    class_basename($this), 'ConferenceParticipant',
                    'EducatorAppeal', 'EducatorClass', 'Event', 'SmsEducator'
                ], 'educator_id');
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
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Reader\Exception
     */
    function import() {
        
        $records = $this->upload();
        $mobiles = array_count_values(
            array_map('strval', Arr::pluck($records, 'G'))
        );
        foreach ($mobiles as $mobile => $count) {
            $count <= 1 ?: $duplicates[] = $mobile;
        }
        abort_if(
            isset($duplicates),
            HttpStatusCode::NOT_ACCEPTABLE,
            implode('', [
                '手机号码',
                implode(',', $duplicates ?? []),
                '有重复，请检查后重试。',
            ])
        );
        ImportEducator::dispatch($records, Auth::id());
        
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
            $userIds = DepartmentUser::whereDepartmentId(Request::input('sectionId'))->pluck('user_id')->toArray();
            $users = User::whereIn('id', $userIds)->get()->filter(
                function (User $user) { return !in_array($user->group->name, ['监护人', '学生']); }
            );
            $snHtml = $card->input();
            $record = <<<HTML
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
                    $record,
                    $user->id,
                    $user->realname,
                    $user->username,
                    $user->id,
                    $i,
                    $sn
                );
                $i++;
            }
            return $list;
        }
        
        return $card->issue();
        
    }
    
    /**
     *
     * 批量授权
     *
     * @return JsonResponse|string
     * @throws Throwable
     */
    function permit() {
    
        return (new Card)->permit('Educator');
        
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
            $educatorIds = User::with('educator')->whereIn('id', $userIds)
                ->get()->pluck('educator.id')->toArray();
        } else {
            $educatorIds = $this->contactIds('educator');
        }
        $educators = $this->whereIn('id', $educatorIds)
            ->where('school_id', $this->schoolId())->get();
        ExportEducator::dispatch($educators, self::EXCEL_TITLES, Auth::id());
        
        return true;
        
    }
    
    /** Helper functions -------------------------------------------------------------------------------------------- */
    /**
     * 返回对当前登录用户可见的班级与科目列表
     *
     * @param null $id
     * @return array
     */
    function compose($id = null) {
        
        $classes = Squad::whereIn('id', $this->classIds())->where('enabled', 1)->get();
        $gradeIds = array_unique($classes->pluck('grade_id')->toArray());
        $subjects = Subject::where(['enabled' => 1, 'school_id' => $this->schoolId()])
            ->get()->filter(function (Subject $subject) use ($gradeIds) {
                return !empty(array_intersect($gradeIds, explode(',', $subject->grade_ids)));
            }
        );
        if (($educatorId = $id ?? Request::route('id')) && Request::method() == 'GET') {
            $educator = $this->find($educatorId);
            $educator->{'card'} = $educator->user->card;
            $mobiles = $educator ? $educator->user->mobiles : null;
            $selectedDepartmentIds = !$educator ? []
                : $educator->user->deptIds($educator->user_id);
            $selectedDepartments = $this->selectedNodes($selectedDepartmentIds);
        }
        $firstOption = [0 => '(请选择)'];
        
        return [
            $educator ?? null,
            $firstOption + $classes->pluck('name', 'id')->toArray(),
            $firstOption + $subjects->pluck('name', 'id')->toArray(),
            (new Group)->groupList(),
            implode(',', $selectedDepartmentIds ?? []),
            $selectedDepartments ?? [],
            $mobiles ?? [],
        ];
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
            $dType = DepartmentType::find($department['department_type_id'])->name;
            $nodes[] = [
                'id'     => $department->id,
                'parent' => $department->parent_id ?? '#',
                'text'   => $department->name,
                'icon'   => Constant::NODE_TYPES[$dType]['icon'],
                'type'   => Constant::NODE_TYPES[$dType]['type'],
            ];
        }
        
        return $nodes ?? [];
        
    }
    
}

