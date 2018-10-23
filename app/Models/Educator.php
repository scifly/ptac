<?php
namespace App\Models;

use App\Facades\Datatable;
use App\Helpers\Constant;
use App\Helpers\HttpStatusCode;
use App\Helpers\ModelTrait;
use App\Helpers\Snippet;
use App\Http\Requests\EducatorRequest;
use App\Jobs\ImportEducator;
use Carbon\Carbon;
use Eloquent;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\IOFactory;
use ReflectionException;
use Throwable;

/**
 * App\Models\Educator 教职员工
 *
 * @property int $id
 * @property int $user_id 教职员工用户ID
 * @property int $school_id 所属学校ID
 * @property int $sms_quote 可用短信条数
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
 * @mixin Eloquent
 */
class Educator extends Model {
    
    use ModelTrait;
    
    const EXPORT_TITLES = [
        '#', '姓名', '所属学校', '手机号码', '职务',
        '创建于', '更新于', '状态',
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
        
        $educatorIds = Grade::whereEnabled(1)
            ->where('id', $gradeId)
            ->first()
            ->educator_ids;
        
        return self::whereIn('id', explode(',', $educatorIds))
            ->where('enabled', 1)
            ->get();
        
    }
    
    /**
     * 获取指定班级的班级主任教职员工对象
     *
     * @param $classId
     * @return Collection|static[]
     */
    function classDeans($classId) {
        
        $educatorIds = Squad::whereEnabled(1)
            ->where('id', $classId)
            ->first()
            ->educator_ids;
        
        return self::whereIn('id', explode(',', $educatorIds))
            ->where('enabled', 1)
            ->get();
        
    }
    
    /**
     * 返回教职员工列表
     *
     * @param array $ids
     * @return array
     */
    function educatorList(array $ids) {
        
        $educators = [];
        foreach ($ids as $id) {
            $educator = self::find($id);
            if ($educator) {
                $educators[$id] = $educator->user->realname;
            }
        }
        
        return $educators;
        
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
                'type'       => 'INNER',
                'conditions' => [
                    'User.id = Educator.user_id',
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
            $this->getModel(), $columns, $joins, $this->contactCondition()
        );
        
    }
    
    /**
     * 保存新创建的教职员工记录
     *
     * @param array $data
     * @return bool|mixed
     * @throws Throwable
     */
    function store(array $data) {
        
        try {
            DB::transaction(function () use ($data) {
                # 创建用户
                $user = User::create($data['user']);
                # 创建教职员工
                $data['educator']['user_id'] = $user->id;
                $educator = $this->create($data);
                # 保存班级科目绑定关系
                (new EducatorClass)->storeByEducatorId($educator->id, $data['cs']);
                # 保存部门用户绑定关系
                (new DepartmentUser)->storeByUserId($user->id, $data['selectedDepartments'], false);
                # 保存手机号码
                (new Mobile)->store($data['mobile'], $user->id);
                # 创建企业号成员
                $user->createWechatUser($user->id);
            });
        } catch (Exception $e) {
            throw $e;
        }
        
        return true;
        
    }
    
    /**
     * 修改教职员工
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
                $educator = $this->find($id);
                # 更新用户
                User::find($educator->user_id)->update($data['user']);
                # 更新教职员工
                $educator->update($data['educator']);
                # 保存班级科目绑定关系
                (new EducatorClass)->storeByEducatorId($educator->id, $data['cs']);
                # 更新教职员工&部门绑定关系
                (new DepartmentUser)->storeByUserId($educator->user_id, $data['selectedDepartments']);
                # 保存手机号码
                (new Mobile)->store($data['mobile'], $educator->user_id);
                # 更新企业号成员
                (new User)->UpdateWechatUser($educator->user_id);
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
     * @throws ReflectionException
     */
    function remove($id = null) {
        
        return (new User)->removeContact($this, $id);
        
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
                (new User)->remove($educator->user_id, $broadcast);
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
    
        return $this->upload($file);
        
    }
    
    /**
     * 上传教职员工excel文件
     *
     * @param UploadedFile $file
     * @return bool
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Reader\Exception
     */
    function upload(UploadedFile $file) {
        
        $ext = $file->getClientOriginalExtension();     // 扩展名//xls
        $realPath = $file->getRealPath();   //临时文件的绝对路径
        // 上传文件
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
            $this->checkFileFormat(
                self::EXPORT_TITLES,
                array_values($educators[1])
            ),
            HttpStatusCode::NOT_ACCEPTABLE,
            __('messages.invalid_file_format')
        );
        array_shift($educators);
        $educators = array_values($educators);
        # 去除表格的空数据
        foreach ($educators as $key => $value) {
            if ((array_filter($value)) == null) {
                unset($educators[$key]);
            }
        }
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
     */
    function export() {
        
        $range = Request::query('range');
        $departmentId = Request::query('id');
        $educatorIds = $range
            ? $this->educatorIds($departmentId)
            : $this->contactIds('educator');
        $educators = $this->whereIn('id', $educatorIds)
            ->where('school_id', $this->schoolId())->get();
        $records = [self::EXPORT_TITLES];
        foreach ($educators as $educator) {
            if (!$educator->user) {
                continue;
            }
            $records[] = [
                $educator->id,
                $educator->user->realname,
                $educator->school->name,
                implode(', ', $educator->user->mobiles->pluck('mobile')->toArray()),
                $educator->user->position,
                $educator->created_at,
                $educator->updated_at,
                $educator->enabled ? '启用' : '禁用',
            ];
        }
        
        return $this->excel($records);
        
    }
    
    /** Helper functions -------------------------------------------------------------------------------------------- */
    /**
     * 返回对当前登录用户可见的班级与科目列表
     *
     * @return array
     */
    function compose() {
        
        $classes = $this->whereIn('id', $this->classIds())->where('enabled', 1)->get();
        $gradeIds = Grade::whereIn('id', array_unique($classes->pluck('grade_id')->toArray()))->pluck('id')->toArray();
        $subjects = Subject::where(['enabled' => 1, 'school_id' => $this->schoolId()])->get()->filter(
            function (Subject $subject) use ($gradeIds) {
                return !empty(array_intersect($gradeIds, explode(',', $subject->grade_ids)));
            }
        );
        if (Request::route('id')) {
            $educator = $this->find(Request::route('id'));
            $mobiles = $educator->user->mobiles;
            $selectedDepartmentIds = $educator->user->departments->pluck('id')->toArray();
            $selectedDepartments = $this->selectedNodes($selectedDepartmentIds);
        }
        return [
            $classes->pluck('name', 'id')->toArray(),
            $subjects->pluck('name', 'id')->toArray(),
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

