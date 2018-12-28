<?php
namespace App\Models;

use App\Facades\Datatable;
use App\Helpers\{Constant, HttpStatusCode, ModelTrait, Snippet};
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
use Illuminate\Support\Facades\{Auth, DB, Request, Storage};
use PhpOffice\PhpSpreadsheet\IOFactory;
use ReflectionClass;
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
 * @mixin Eloquent
 */
class Educator extends Model {
    
    use ModelTrait;
    
    const EXCEL_TITLES = [
        '姓名', '性别', '员工编号', '职务', '部门',
        '学校', '手机号码', '年级主任', '班级主任', '班级科目'
    ];
    protected $fillable = [
        'user_id', 'team_ids', 'school_id', 'singular',
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
     * 获取指定年级的年级主任教职员工对象
     *
     * @param $gradeId
     * @return Collection|static[]
     */
    function gradeDeans($gradeId) {
        
        $conditions = ['id' => $gradeId, 'enabled' => 1];
        $educatorIds = Grade::where($conditions)->first()->educator_ids;
        
        return $this->whereIn('id', explode(',', $educatorIds))
            ->where('enabled', 1)->get();
        
    }
    
    /**
     * 获取指定班级的班级主任教职员工对象
     *
     * @param $classId
     * @return Collection|static[]
     */
    function classDeans($classId) {
    
        $conditions = ['id' => $classId, 'enabled' => 1];
        $educatorIds = Squad::where($conditions)->first()->educator_ids;
        
        return self::whereIn('id', explode(',', $educatorIds))
            ->where('enabled', 1)->get();
        
    }
    
    /**
     * 返回教职员工列表
     *
     * @param array $ids
     * @return array
     */
    function educatorList(array $ids) {
        
        return $this->whereIn('id', $ids)
            ->with('user')->get()
            ->pluck('user.realname', 'id')
            ->toArray();
        
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
                'db' => 'User.avatar_url', 'dt' => 2,
                'formatter' => function ($d) {
                    return Snippet::avatar($d);
                }
            ],
            [
                'db' => 'User.gender', 'dt' => 3,
                'formatter' => function ($d) {
                    return Snippet::gender($d);
                }
            ],
            ['db' => 'User.position', 'dt' => 4],
            ['db' => 'Mobile.mobile', 'dt' => 5],
            ['db' => 'Educator.created_at', 'dt' => 6, 'dr' => true],
            ['db' => 'Educator.updated_at', 'dt' => 7, 'dr' => true],
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
                'db'        => 'Educator.enabled', 'dt' => 10,
                'formatter' => function ($d, $row) {
                    $id = $row['id'];
                    $user = Auth::user();
                    $rechargeLink = sprintf(Snippet::DT_LINK_RECHARGE, 'recharge_' . $id);
                    
                    return Datatable::status($d, $row, false) .
                        ($user->can('act', self::uris()['recharge']) ? $rechargeLink : '');
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
                'table' => 'mobiles',
                'alias' => 'Mobile',
                'type' => 'LEFT',
                'conditions' => [
                    'User.id = Mobile.user_id',
                    'Mobile.isdefault = 1'
                ]
            ]
        ];
        
        return Datatable::simple(
            $this->getModel(), $columns, $joins,
            'Educator.user_id IN (' . $this->visibleUserIds() . ')'
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
                if (!$educator->singular) {
                    # 监护人(Custodian)
                    $custodian = $this->create($data);
                    # 监护人&部门绑定关系
                    (new DepartmentUser)->storeByUserId($custodian->user_id, $data['departmentIds'], true);
                    # 监护人&学生关系
                    (new CustodianStudent)->storeByCustodianId($custodian->id, $data['relationships']);
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
        
        if (!$id) { return $this->batchUpdateContact($this); }
        try {
            DB::transaction(function () use ($data, $id) {
                if ($id) {
                    $educator = $this->find($id);
                    # 更新用户
                    $educator->user->update($data['user']);
                    # 更新教职员工
                    $educator->update($data);
                    # 保存班级科目绑定关系
                    (new EducatorClass)->storeByEducatorId($educator->id, $data['cs']);
                    # 更新教职员工&部门绑定关系
                    (new DepartmentUser)->storeByUserId($educator->user_id, $data['selectedDepartments']);
                    # 保存手机号码
                    (new Mobile)->store($data['mobile'], $educator->user_id);
                    # 如果同时也是监护人
                    $custodian = $educator->user->custodian;
                    if (!$educator->singular) {
                        $custodian ? $custodian->update($data) : $custodian = Custodian::create($data);
                        # 更新监护人&部门绑定关系
                        (new DepartmentUser)->storeByUserId($custodian->user_id, $data['departmentIds'], true);
                        # 更新监护人&学生关系
                        (new CustodianStudent)->storeByCustodianId($custodian->id, $data['relationships']);
                    } else {
                        !$custodian ?: (new Custodian)->purge($custodian->id);
                    }
                    $userIds = [$educator->user_id];
                } else {
                    $this->batchUpdateContact($this);
                    $ids = array_values(Request::input('ids'));
                    $userIds = $this->whereIn('id', $ids)->pluck('user_id')->toArray();
                }
                # 同步企业微信
                (new User)->sync(
                    array_map(
                        function ($userId) {
                            return [$userId, '', 'update'];
                        }, $userIds
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
     * @return \Illuminate\Http\JsonResponse
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
        
        return (new User)->clean($this, $id);
        
    }
    
    /**
     * 删除指定教职员工的所有数据
     *
     * @param $id
     * @param bool $broadcast
     * @return bool
     * @throws Throwable
     */
    function purge($id, $broadcast = true) {
        
        try {
            DB::transaction(function () use ($id, $broadcast) {
                $educator = $this->find($id);
                ConferenceParticipant::whereEducatorId($id)->delete();
                (new ConferenceQueue)->removeEducator($id);
                (new EducatorAppeal)->removeEducator($id);
                EducatorAttendance::whereEducatorId($id)->delete();
                EducatorClass::whereEducatorId($id)->delete();
                TagUser::whereUserId($educator->user_id)->delete();
                Event::whereEducatorId($id)->delete();
                (new Grade)->removeEducator($id);
                (new Squad)->removeEducator($id);
                if (!$educator->user->custodian) {
                    $educator->user->purge($educator->user_id);
                } else {
                    $educator->user->find($educator->user_id)->update([
                        'group_id' => Group::whereName('监护人')->first()->id
                    ]);
                }
                $educator->delete();
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
    
        abort_if(
            Request::method() != 'POST',
            HttpStatusCode::INTERNAL_SERVER_ERROR,
            __('messages.file_upload_failed')
        );
        $file = Request::file('file');
        abort_if(
            empty($file) || !$file->isValid(),
            HttpStatusCode::INTERNAL_SERVER_ERROR,
            __('messages.empty_file')
        );
        $ext = $file->getClientOriginalExtension();
        $realPath = $file->getRealPath();
        $filename = date('His') . uniqid() . '.' . $ext;
        $stored = Storage::disk('uploads')->put(
            date('Y/m/d/', time()) . $filename,
            file_get_contents($realPath)
        );
        abort_if(
            !$stored,
            HttpStatusCode::INTERNAL_SERVER_ERROR,
            __('messages.file_upload_failed')
        );
        $spreadsheet = IOFactory::load(
            $this->uploadedFilePath($filename)
        );
        $educators = $spreadsheet->getActiveSheet()->toArray(
            null, true, true, true
        );
        abort_if(
            !$this->checkFileFormat(
                self::EXCEL_TITLES,
                array_values($educators[1])
            ),
            HttpStatusCode::NOT_ACCEPTABLE,
            __('messages.invalid_file_format')
        );
        $educators = array_filter(array_values($educators), 'implode');
        array_shift($educators);
        $mobiles = array_count_values(
            array_map('strval', array_pluck($educators, 'G'))
        );
        foreach ($mobiles as $mobile => $count) {
            $count <= 1 ?: $duplicates[] = $mobile;
        }
        abort_if(
            isset($duplicates),
            HttpStatusCode::NOT_ACCEPTABLE,
            '手机号码: ' . implode(',', $duplicates ?? []) . '有重复，请检查后重试。'
        );
        ImportEducator::dispatch($educators, Auth::id());
        Storage::disk('uploads')->delete($filename);
    
        return true;
        
    }
    
    /**
     * 批量导出教职员工
     *
     * @return bool
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     * @throws ReflectionException
     */
    function export() {
    
        if (Request::query('range') == 0) {
            $userIds = $this->userIds(Request::query('id'), 'educator');
            $educatorIds = User::whereIn('id', $userIds)->with('educator')
                ->get()->pluck('educator.id')->toArray();
        } else {
            $educatorIds = $this->contactIds('educator');
        }
        $educators = $this->whereIn('id', $educatorIds)
            ->where('school_id', $this->schoolId())->get();
        $records = [];
        foreach ($educators as $educator) {
            if (!($user = $educator->user)) continue;
            list($grades, $squads) = array_map(
                function ($name) use ($educator) {
                    $className = 'App\\Models\\' . ucfirst($name);
                    $model = (new ReflectionClass($className))->newInstance();
                    /** @var Collection $collection */
                    $collection = $model->whereRaw($educator->id . ' IN (educator_ids)')->get();
                
                    return $collection->isEmpty() ? ''
                        : implode(',', $collection->pluck('name')->toArray());
                }, ['squad', 'grade']
            );
            $eces = EducatorClass::whereEducatorId($educator->id)->get();
            foreach ($eces as $ec) {
                $squad = $ec->squad;
                $subject = $ec->subject;
                if (isset($squad, $subject)) {
                    $cses[] = implode(':', [$squad->name, $subject->name]);
                }
            }
            $records[] = [
                $user->realname,
                $user->gender ? '男' : '女',
                strval($user->username),
                $user->position,
                $user->departments->first()->name,
                $educator->school->name,
                $user->mobiles->where('isdefault', 1)->first()->mobile,
                $grades,
                $squads,
                implode(',', $cses ?? [])
            ];
        }
        usort($records, function ($a, $b) {
            return strcmp($a[4], $b[4]);     # 按部门排序
        });
    
        return $this->excel(
            array_merge([self::EXCEL_TITLES], $records)
        );
        
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
        // Grade::whereIn('id', )->pluck('id')->toArray();
        $subjects = Subject::where(['enabled' => 1, 'school_id' => $this->schoolId()])->get()->filter(
            function (Subject $subject) use ($gradeIds) {
                return !empty(array_intersect($gradeIds, explode(',', $subject->grade_ids)));
            }
        );
        $educatorId = $id ?? Request::route('id');
        if ($educatorId && Request::method() == 'GET') {
            $educator = $this->find($educatorId);
            $mobiles = $educator ? $educator->user->mobiles : null;
            $selectedDepartmentIds = $educator
                ? $educator->user->depts($educator->user_id)->pluck('id')->toArray() : [];
            $selectedDepartments = $this->selectedNodes($selectedDepartmentIds);
        }
        $firstOption = [0 => '(请选择)'];
    
        return [
            $firstOption + $classes->pluck('name', 'id')->toArray(),
            $firstOption + $subjects->pluck('name', 'id')->toArray(),
            (new Group)->groupList(),
            implode(',', $selectedDepartmentIds ?? []),
            $selectedDepartments ?? [],
            $mobiles ?? []
        ];
    }
    
    /**
     * 选中的部门节点
     *
     * @param $departmentIds
     * @return array
     */
    private function selectedNodes($departmentIds) {
        
        $departments = Department::whereIn('id', $departmentIds)->get()->toArray();
        $nodes = [];
        foreach ($departments as $department) {
            $parentId = isset($department['parent_id']) ? $department['parent_id'] : '#';
            $text = $department['name'];
            $departmentType = DepartmentType::find($department['department_type_id'])->name;
            $nodes[] = [
                'id'     => $department['id'],
                'parent' => $parentId,
                'text'   => $text,
                'icon'   => Constant::NODE_TYPES[$departmentType]['icon'],
                'type'   => Constant::NODE_TYPES[$departmentType]['type'],
            ];
        }
        
        return $nodes;
        
    }
    
}

