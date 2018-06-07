<?php
namespace App\Models;

use Eloquent;
use Illuminate\Support\Facades\Log;
use Throwable;
use Exception;
use Carbon\Carbon;
use App\Rules\Mobiles;
use App\Helpers\Snippet;
use App\Helpers\ModelTrait;
use App\Jobs\ImportEducator;
use App\Helpers\HttpStatusCode;
use Illuminate\Validation\Rule;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\EducatorRequest;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Model;
use PhpOffice\PhpSpreadsheet\IOFactory;
use App\Http\Requests\CustodianRequest;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\Collection;
use App\Facades\DatatableFacade as Datatable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

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
 * @property-read Collection|Team[] $teams
 * @property-read User $user
 * @method static Builder|Educator whereCreatedAt($value)
 * @method static Builder|Educator whereEnabled($value)
 * @method static Builder|Educator whereId($value)
 * @method static Builder|Educator whereSchoolId($value)
 * @method static Builder|Educator whereSmsQuote($value)
 * @method static Builder|Educator whereUpdatedAt($value)
 * @method static Builder|Educator whereUserId($value)
 * @mixin Eloquent
 * @property-read Collection|EducatorClass[] $educatorClasses
 */
class Educator extends Model {
    
    use ModelTrait;
    
    const EXPORT_TITLES = [
        '#', '姓名', '所属学校', '手机号码',
        '创建于', '更新于', '状态',
    ];
    protected $fillable = [
        'user_id', 'team_ids', 'school_id',
        'sms_quote', 'enabled',
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
     * 获取指定教职员工所属的所有教职员工组对象
     *
     * @return BelongsToMany
     */
    function teams() {
        
        return $this->belongsToMany(
            'App\Models\Team',
            'educators_teams'
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
     * 保存新创建的教职员工记录
     *
     * @param EducatorRequest|CustodianRequest $request
     * @return bool|mixed
     * @throws Exception
     * @throws \Throwable
     */
    function store(EducatorRequest $request) {
        
        try {
            DB::transaction(function () use ($request) {
                # 创建用户
                $user = $request->input('user');
                $u = User::create([
                    'username'     => $user['username'],
                    'group_id'     => $user['group_id'],
                    'password'     => bcrypt($user['password']),
                    'email'        => $user['email'],
                    'realname'     => $user['realname'],
                    'gender'       => $user['gender'],
                    'avatar_url'   => '00001.jpg',
                    'userid'       => 'user_' . uniqid(),
                    'isleader'     => 0,
                    'english_name' => $user['english_name'],
                    'telephone'    => $user['telephone'],
                    'enabled'      => $user['enabled'],
                    'synced'       => $user['synced']
                ]);
                
                # 创建教职员工(当角色选择学校管理员时，也同时创建教职员工数据20180207 by wenw)
                $educatorInputData = $request->input('educator');
                $educator = self::create([
                    'user_id'   => $u->id,
                    'school_id' => $educatorInputData['school_id'],
                    'sms_quote' => 0,
                    'enabled'   => $user['enabled'],
                ]);
                if ($u->group_id != Group::whereName('学校')->first()->id) {
                    # 保存班级科目绑定关系
                    $classSubjectData = $request->input('classSubject');
                    if ($classSubjectData['class_ids'] && $classSubjectData['subject_ids']) {
                        $uniqueArray = [];
                        foreach ($classSubjectData['class_ids'] as $index => $class) {
                            $uniqueArray[] = [
                                'class_id'   => $class,
                                'subject_id' => $classSubjectData['subject_ids'][$index],
                            ];
                        }
                        $classSubjects = self::array_unique_fb($uniqueArray);
                        foreach ($classSubjects as $key => $row) {
                            if ($row['class_id'] != 0 && $row['class_id'] != 0) {
                                EducatorClass::create([
                                    'educator_id' => $educator->id,
                                    'class_id'    => $row['class_id'],
                                    'subject_id'  => $row['subject_id'],
                                    'enabled'     => $user['enabled'],
                                ]);
                            }
                        }
                    }
                    if (isset($educatorInputData['team_id'])) {
                        foreach ($educatorInputData['team_id'] as $key => $row) {
                            EducatorTeam::create([
                                'educator_id' => $educator->id,
                                'team_id'     => $row,
                                'enabled'     => $user['enabled'],
                            ]);
                        }
                    }
                }
                # 创建部门信息
                $selectedDepartments = $request->input('selectedDepartments');
                if (!empty($selectedDepartments)) {
                    foreach ($selectedDepartments as $department) {
                        DepartmentUser::create([
                            'user_id'       => $u->id,
                            'department_id' => $department,
                            'enabled'       => $user['enabled'],
                        ]);
                    }
                }
                # 当选择了学校角色没有选择学校部门时
                $schoolId = $this->schoolId();
                $schoolDeptId = School::find($schoolId)->department_id;
                $deptUser = DepartmentUser::whereDepartmentId($schoolDeptId)
                    ->where('user_id', $u->id)->first();
                if ($u->group_id == Group::whereName('学校')->first()->id && empty($deptUser)) {
                    DepartmentUser::create([
                        'user_id'       => $u->id,
                        'department_id' => School::find($schoolId)->department_id,
                        'enabled'       => $user['enabled'],
                    ]);
                }
                $mobiles = $request->input('mobile');
                if ($mobiles) {
                    foreach ($mobiles as $k => $mobile) {
                        Mobile::create([
                            'user_id'   => $u->id,
                            'mobile'    => $mobile['mobile'],
                            'isdefault' => $mobile['isdefault'],
                            'enabled'   => $mobile['enabled'],
                        ]);
                    }
                }
                // # 创建企业号成员
                $u->createWechatUser($u->id);
            });
        } catch (Exception $e) {
            throw $e;
        }
        
        return true;
        
    }
    
    /**
     * 修改教职员工
     *
     * @param EducatorRequest $request
     * @param $id
     * @return bool|mixed
     * @throws Exception
     */
    function modify(EducatorRequest $request, $id = null) {
        
        if (!isset($id)) {
            return $this->batch($this);
        }
        $educator = $this->find($id);
        if (!$educator) { return false; }
        try {
            DB::transaction(function () use ($request) {
                $user = $request->input('user');
                User::find($request->input('user_id'))->update([
                    'username'     => $user['username'],
                    'group_id'     => $user['group_id'],
                    'email'        => $user['email'],
                    'realname'     => $user['realname'],
                    'gender'       => $user['gender'],
                    'avatar_url'   => '00001.jpg',
                    'isleader'     => 0,
                    'english_name' => $user['english_name'],
                    'telephone'    => $user['telephone'],
                    'enabled'      => $user['enabled'],
                ]);
                $selectedDepartments = $request->input('selectedDepartments');
                if (!empty($selectedDepartments)) {
                    DepartmentUser::whereUserId($request->input('user_id'))->delete();
                    foreach ($selectedDepartments as $department) {
                        DepartmentUser::create([
                            'user_id'       => $request->input('user_id'),
                            'department_id' => $department,
                            'enabled'       => $user['enabled'],
                        ]);
                        
                    }
                }
                # 当选择了学校角色没有选择学校部门时
                $schoolId = $this->schoolId();
                $schoolDeptId = School::find($schoolId)->department_id;
                $deptUser = DepartmentUser::whereDepartmentId($schoolDeptId)
                    ->where('user_id', $request->input('user_id'))
                    ->first();
                if ($user['group_id'] == Group::whereName('学校')->first()->id && empty($deptUser)) {
                    DepartmentUser::create([
                        'user_id'       => $request->input('user_id'),
                        'department_id' => School::find($schoolId)->department_id,
                        'enabled'       => $user['enabled'],
                    ]);
                }
                $educator = $request->input('educator');
                self::find($request->input('id'))->update([
                    'user_id'   => $request->input('user_id'),
                    'school_id' => $educator['school_id'],
                    'sms_quote' => 0,
                    'enabled'   => $user['enabled'],
                ]);
                if (isset($educator['team_id'])) {
                    EducatorTeam::whereEducatorId($request->input('id'))->delete();
                    foreach ($educator['team_id'] as $key => $row) {
                        EducatorTeam::create([
                            'educator_id' => $request->input('id'),
                            'team_id'     => $row,
                            'enabled'     => $user['enabled'],
                        ]);
                    }
                }
                if ($user['group_id'] != Group::whereName('学校')->first()->id) {
                    $classSubjectData = $request->input('classSubject');
                    if ($classSubjectData) {
                        EducatorClass::whereEducatorId($request->input('id'))->delete();
                        $uniqueArray = [];
                        foreach ($classSubjectData['class_ids'] as $index => $class) {
                            $uniqueArray[] = [
                                'class_id'   => $class,
                                'subject_id' => $classSubjectData['subject_ids'][$index],
                            ];
                        }
                        $classSubjects = self::array_unique_fb($uniqueArray);
                        foreach ($classSubjects as $key => $row) {
                            if ($row['class_id'] != 0 && $row['subject_id'] != 0) {
                                EducatorClass::create([
                                    'educator_id' => $request->input('id'),
                                    'class_id'    => $row['class_id'],
                                    'subject_id'  => $row['subject_id'],
                                    'enabled'     => $user['enabled'],
                                ]);
                            }
                        }
                    } else {
                        EducatorClass::whereEducatorId($request->input('id'))->delete();
                    }
                }
                $mobiles = $request->input('mobile');
                if ($mobiles) {
                    Mobile::whereUserId($request->input('user_id'))->delete();
                    foreach ($mobiles as $k => $mobile) {
                        Mobile::create([
                            'user_id'   => $request->input('user_id'),
                            'mobile'    => $mobile['mobile'],
                            'isdefault' => $mobile['isdefault'],
                            'enabled'   => $mobile['enabled'],
                        ]);
                    }
                }
                # 更新企业号成员
                $user = new User();
                $user->UpdateWechatUser($request->input('user_id'));
                unset($user);
            });
        } catch (Exception $e) {
            throw $e;
        }
        
        return true;
        
    }
    
    /**
     * 删除教职员工
     *
     * @param $id
     * @return bool
     * @throws Exception
     */
    function remove($id = null) {
        
        if (!isset($id)) { return $this->batch($this); }
        try {
            DB::transaction(function () use ($id) {
                $educator = self::find($id);
                (new User())->remove($educator->user_id);
                if (!self::removable($educator)) {
                    throw new Exception(__('messages.del_fail'));
                }
                $educator->delete();
            });
        } catch (Exception $e) {
            throw $e;
        }
        
        return true;
        
    }
    
    /**
     * 批量导入
     *
     * @param UploadedFile $file
     * @return array
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Reader\Exception
     */
    function upload(UploadedFile $file) {
        
        $ext = $file->getClientOriginalExtension();     // 扩展名//xls
        $realPath = $file->getRealPath();   //临时文件的绝对路径
        // 上传文件
        $filename = date('His') . uniqid() . '.' . $ext;
        $stored = Storage::disk('uploads')->put($filename, file_get_contents($realPath));
        if ($stored) {
            $spreadsheet = IOFactory::load(
                $this->uploadedFilePath($filename)
            );
            $educators = $spreadsheet->getActiveSheet()->toArray(
                null, true, true, true
            );
            abort_if(
                $this->checkFileFormat(self::EXPORT_TITLES, $educators[0]),
                HttpStatusCode::NOT_ACCEPTABLE,
                '文件格式错误'
            );
            unset($educators[0]);
            $educators = array_values($educators);
            if (count($educators) != 0) {
                # 去除表格的空数据
                foreach ($educators as $key => $v) {
                    if ((array_filter($v)) == null) {
                        unset($educators[$key]);
                    }
                }
                $rows = self::validateData($educators);
                if (!empty($rows)) {
                    ImportEducator::dispatch($rows, Auth::id());
                }
            }
            Storage::disk('uploads')->delete($filename);
            
            return [
                'statusCode' => HttpStatusCode::OK,
                'message'    => '上传成功',
            ];
            
        }
        
        return abort(HttpStatusCode::INTERNAL_SERVER_ERROR, '上传失败');
        
    }
    
    /**
     * 批量导出
     *
     * @param integer $range , 0 - 指定部门(含所有子部门), 1 - 所有
     * @param null $departmentId
     * @return mixed
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    function export($range, $departmentId = null) {
        
        $educatorIds = $range == 0
            ? $this->educatorIds($departmentId)
            : $this->contactIds('educator');
        Log::debug(json_encode($this->educatorIds(58)));
        $educators = $this->whereIn('id', $educatorIds)->get();
        $records = [self::EXPORT_TITLES];
        foreach ($educators as $educator) {
            if (!$educator->user) { continue; }
            $records = [
                $educator->user->realname,
                $educator->school->name,
                implode(', ', $educator->user->mobiles->pluck('mobile')->toArray()),
                $educator->sms_quote,
                $educator->created_at,
                $educator->updated_at,
                $educator->enabled ? '启用' : '禁用',
            ];
        }
        
        return $this->excel($records);
        
    }
    
    /**
     * 教职员工列表
     *
     * @return array
     */
    function datatable() {
        
        $columns = [
            ['db' => 'Educator.id', 'dt' => 0],
            ['db' => 'User.realname as username', 'dt' => 1],
            ['db' => 'Educator.created_at', 'dt' => 2],
            ['db' => 'Educator.updated_at', 'dt' => 3],
            [
                'db'        => 'Educator.enabled', 'dt' => 4,
                'formatter' => function ($d, $row) {
                    $id = $row['id'];
                    $user = Auth::user();
                    $editLink = sprintf(Snippet::DT_LINK_EDIT, 'edit_' . $id);
                    $delLink = sprintf(Snippet::DT_LINK_DEL, $id);
                    $rechargeLink = sprintf(Snippet::DT_LINK_RECHARGE, 'recharge_' . $id);
                    
                    return
                        Snippet::status($d) .
                        ($user->can('act', self::uris()['edit']) ? $editLink : '') .
                        ($user->can('act', self::uris()['destroy']) ? $delLink : '') .
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
        ];
        $condition = 'Educator.id IN (' . implode(',', $this->contactIds('educator')) . ')';
        
        return Datatable::simple(
            $this->getModel(), $columns, $joins, $condition
        );
        
    }
    
    /**
     * 去掉二维数组中的重复值
     *
     * @param $array2D
     * @return array
     */
    private function array_unique_fb($array2D) {
        
        $temp = [];
        foreach ($array2D as $v) {
            # 降维,也可以用implode,将一维数组转换为用逗号连接的字符串
            $v = join(',', $v);
            $temp[] = $v;
        }
        # 去掉重复的字符串,也就是重复的一维数组
        $tempUnique = array_unique($temp);
        $csArray = [];
        foreach ($tempUnique as $k => $v) {
            # 再将拆开的数组重新组装
            $tempArray = explode(',', $v);
            $csArray[$k]['class_id'] = $tempArray[0];
            $csArray[$k]['subject_id'] = $tempArray[1];
        }
        
        return $csArray;
        
    }
    
    /**
     * 验证数据合法性
     *
     * @param array $data
     * @return array
     */
    private function validateData(array $data) {
        
        $rules = [
            'name'              => 'required|string|between:2,20',
            'gender'            => ['required', Rule::in(['男', '女'])],
            'birthday'          => 'required|date',
            // 'birthday' => ['required', 'string', 'regex:/^((19\d{2})|(20\d{2}))-([1-12])-([1-31])$/'],
            'school'            => 'required|string|between:4,20',
            'mobile'            => 'required', new Mobiles(),
            'grades'            => 'string',
            'classes'           => 'string',
            'subjects'          => 'string',
            'educators_classes' => 'string',
            'departments'       => 'required|string',
        ];
        // Validator::make($data,$rules);
        # 不合法的数据
        $invalidRows = [];
        # 需要添加的数据
        $rows = [];
        for ($i = 0; $i < count($data); $i++) {
            $user = [
                'name'              => $data[$i][0],
                'gender'            => $data[$i][1],
                'birthday'          => $data[$i][2],
                'school'            => $data[$i][3],
                'mobile'            => $data[$i][4],
                'grades'            => $data[$i][5],
                'classes'           => $data[$i][6],
                'subjects'          => $data[$i][7],
                'educators_classes' => $data[$i][8],
                'departments'       => $data[$i][9],
            ];
            $status = Validator::make($user, $rules);
            if ($status->fails()) {
                $invalidRows[] = $data[$i];
                unset($data[$i]);
                continue;
            }
            $school = School::whereName($user['school'])->first();
            if (!$school) {
                $invalidRows[] = $data[$i];
                unset($data[$i]);
                continue;
            }
            $departments = explode(',', $user['departments']);
            foreach ($departments as $d) {
                $department = Department::whereName($d)->first();
                if (empty($department)) {
                    $invalidRows[] = $data[$i];
                    unset($data[$i]);
                    continue;
                }
            }
            $user['departments'] = $departments;
            $user['school_id'] = $school->id;
            $rows[] = $user;
            unset($user);
        }
        
        return $rows;
        
    }
    
}

