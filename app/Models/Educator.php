<?php

namespace App\Models;

use App\Events\EducatorImported;
use App\Events\SchoolDeleted;
use App\Facades\DatatableFacade as Datatable;
use App\Helpers\ControllerTrait;
use App\Helpers\ModelTrait;
use App\Http\Requests\CustodianRequest;
use App\Http\Requests\EducatorRequest;
use App\Rules\Mobiles;
use Carbon\Carbon;
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
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Readers\LaravelExcelReader;
use PHPExcel_Exception;

/**
 * App\Models\Educator 教职员工
 *
 * @property int $id
 * @property int $user_id 教职员工用户ID
 * @property string $team_ids 所属组
 * @property int $school_id 所属学校ID
 * @property int $sms_quote 可用短信条数
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int $enabled
 * @method static Builder|Educator whereCreatedAt($value)
 * @method static Builder|Educator whereId($value)
 * @method static Builder|Educator whereSchoolId($value)
 * @method static Builder|Educator whereSmsQuote($value)
 * @method static Builder|Educator whereTeamIds($value)
 * @method static Builder|Educator whereUpdatedAt($value)
 * @method static Builder|Educator whereUserId($value)
 * @method static Builder|Educator whereEnabled($value)
 * @mixin \Eloquent
 * @property-read User $user
 * @property-read School $school
 * @property-read Squad[] $classes
 * @property-read EducatorClass $educatorClass
 * @property-read Team[] $teams
 * @property-read Collection|EducatorClass[] $educatorClasses
 */
class Educator extends Model {
    
    use ControllerTrait, ModelTrait;
    
    const EXCEL_FILE_TITLE = [
        '姓名', '性别', '生日', '学校',
        '手机号码', '年级主任', '班级主任',
        '科目名称', '任课班级', '所属部门',
    ];
    const EXCEL_EXPORT_TITLE = [
        '教职工名称', '所属学校', '可用短信条数',
        '创建于', '更新于',
        '状态',
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
    public function user() { return $this->belongsTo('App\Models\User'); }

    /**
     * 返回指定教职员工所属的学校对象
     *
     * @return BelongsTo
     */
    public function school() { return $this->belongsTo('App\Models\School'); }

    /**
     * 获取指定教职员工所属的所有班级对象
     *
     * @return BelongsToMany
     */
    public function classes() {

        return $this->belongsToMany(
            'App\Models\Squad',
            'educators_classes',
            'educator_id',
            'class_id'
        );

    }

    /**
     * 获取指定教职员工所属的所有教职员工组对象
     *
     * @return BelongsToMany
     */
    public function teams() {

        return $this->belongsToMany(
            'App\Models\Team',
            'educators_teams'
        );

    }

    /**
     *  获取指定教职员工的班级科目关系
     *
     * @return HasMany
     */
    public function educatorClasses() { return $this->hasMany('App\Models\EducatorClass'); }

    /**
     * 获取指定年级的年级主任教职员工对象
     *
     * @param $gradeId
     * @return Collection|static[]
     */
    static function gradeDeans($gradeId) {

        $educatorIds = Grade::whereEnabled(1)->where('id', $gradeId)->first()->educator_ids;
        
        return self::whereIn('id', explode(',', $educatorIds))
            ->where('enabled', 1)->get();

    }

    /**
     * 获取指定班级的班级主任教职员工对象
     *
     * @param $classId
     * @return Collection|static[]
     */
    static function classDeans($classId) {

        $educatorIds = Squad::whereEnabled(1)
            ->where('id', $classId)
            ->first()
            ->educator_ids;
        
        return self::whereIn('id', explode(',', $educatorIds))
            ->where('enabled', 1)
            ->get();

    }

    /**
     * 获取指定学校的教职员工列表
     *
     * @param array $schoolIds
     * @return array
     */
    static function educators(array $schoolIds = []) {

        $educatorList = [];
        if (empty($schoolIds)) {
            $educators = self::all();
        } else {
            $educators = self::whereIn('school_id', $schoolIds)->get();
        }
        foreach ($educators as $educator) {
            $educatorList[$educator->id] = $educator->user->realname;
        }
        return $educatorList;

    }

    /**
     * 根据教职员工Id获取教职员工列表
     *
     * @param array $ids
     * @return array
     */
    static function educatorList(array $ids) {

        $educators = [];
        foreach ($ids as $id) {
            $educator = self::find($id);
            $educators[$id] = $educator->user->realname;
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
    static function store(EducatorRequest $request) {
        
        try {
            DB::transaction(function () use ($request) {
                # 创建用户
                $user = $request->input('user');
                $u = User::create([
                    'username' => $user['username'],
                    'group_id' => $user['group_id'],
                    'password' => bcrypt($user['password']),
                    'email' => $user['email'],
                    'realname' => $user['realname'],
                    'gender' => $user['gender'],
                    'avatar_url' => '00001.jpg',
                    'userid' => 'user_' . uniqid(),
                    'wechatid' => $user['wechatid'],
                    'isleader' => 0,
                    'english_name' => $user['english_name'],
                    'telephone' => $user['telephone'],
                    'enabled' => $user['enabled'],
                ]);
                # 创建教职员工
                $educatorInputData = $request->input('educator');
                $educator = self::create([
                    'user_id' => $u->id,
                    'school_id' => $educatorInputData['school_id'],
                    'sms_quote' => 0,
                    'enabled' => $user['enabled'],
                ]);
                # 创建部门信息
                $selectedDepartments = $request->input('selectedDepartments');
                if (!empty($selectedDepartments)) {
                    foreach ($selectedDepartments as $department) {
                        DepartmentUser::create([
                            'user_id' => $u->id,
                            'department_id' => $department,
                            'enabled' => $user['enabled'],
                        ]);
                    }
                }
                # 当选择了学校角色没有选择 学校部门时
                if ($u->group_id == Group::whereName('学校')->first()->id) {
                    DepartmentUser::create([
                        'user_id' => $u->id,
                        'department_id' => School::find(School::id())->department_id,
                        'enabled' => $user['enabled'],
                    ]);
                }
                # 保存班级科目绑定关系
                $classSubjectData = $request->input('classSubject');
                if ($classSubjectData['class_ids'] && $classSubjectData['subject_ids']) {
                    $uniqueArray = [];
                    foreach ($classSubjectData['class_ids'] as $index => $class) {
                        $uniqueArray[] = [
                            'class_id' => $class,
                            'subject_id' => $classSubjectData['subject_ids'][$index],
                        ];
                    }
                    $classSubjects = self::array_unique_fb($uniqueArray);
                    foreach ($classSubjects as $key => $row) {
                        if ($row['class_id'] != 0 && $row['class_id'] != 0) {
                            EducatorClass::create([
                                'educator_id' => $educator->id,
                                'class_id' => $row['class_id'],
                                'subject_id' => $row['subject_id'],
                                'enabled' => $user['enabled'],
                            ]);
                        }
                    }
                }
                if (isset($educatorInputData['team_id'])) {
                    foreach ($educatorInputData['team_id'] as $key => $row) {
                        EducatorTeam::create([
                            'educator_id' => $educator->id,
                            'team_id' => $row,
                            'enabled' => $user['enabled'],
                        ]);
                    }
                }
                if ($classSubjectData) {
                    $uniqueArray = [];
                    foreach ($classSubjectData['class_ids'] as $index => $class) {
                        $uniqueArray[] = [
                            'class_id' => $class,
                            'subject_id' => $classSubjectData['subject_ids'][$index],
                        ];
                    }
                    $classSubjects = self::array_unique_fb($uniqueArray);
                    foreach ($classSubjects as $key => $row) {
                        if ($row['class_id'] != 0 && $row['class_id'] != 0) {
                            EducatorClass::create([
                                'educator_id' => $educator->id,
                                'class_id' => $row['class_id'],
                                'subject_id' => $row['subject_id'],
                                'enabled' => $user['enabled'],
                            ]);
                        }
                    }
                }
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
                # 创建企业号成员
                User::createWechatUser($u->id);
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
     * @param bool $fireEvent
     * @return bool
     * @throws Exception
     */
    static function remove($id, $fireEvent = false) {
        
        $school = self::find($id);
        /** @var School $school */
        $removed = self::removable($school) ? $school->delete() : false;
        if ($removed && $fireEvent) {
            /** @var School $school */
            event(new SchoolDeleted($school));
            return true;
        }
        
        return $removed ? true : false;
        
    }
    
    /**
     * 修改教职员工
     *
     * @param EducatorRequest $request
     * @return bool|mixed
     * @throws Exception
     * @throws \Throwable
     */
    static function modify(EducatorRequest $request) {
        
        try {
            DB::transaction(function () use ($request) {
                $user = $request->input('user');
                User::find($request->input('user_id'))->update([
                    'username' => $user['username'],
                    'group_id' => $user['group_id'],
                    'email' => $user['email'],
                    'realname' => $user['realname'],
                    'gender' => $user['gender'],
                    'avatar_url' => '00001.jpg',
                    'userid' => '111111',
                    'wechatid' => $user['wechatid'],
                    'isleader' => 0,
                    'english_name' => $user['english_name'],
                    'telephone' => $user['telephone'],
                    'enabled' => $user['enabled'],
                ]);
                $selectedDepartments = $request->input('selectedDepartments');
                if (!empty($selectedDepartments)) {
                    DepartmentUser::whereUserId($request->input('user_id'))->delete();
                    foreach ($selectedDepartments as $department) {
                        DepartmentUser::create([
                            'user_id' => $request->input('user_id'),
                            'department_id' => $department,
                            'enabled' => $user['enabled'],
                        ]);
                    }
                }
                # 当选择了学校角色没有选择学校部门时
                if ($user['group_id'] == Group::whereName('学校')->first()->id) {
                    DepartmentUser::create([
                        'user_id' => $request->input('user_id'),
                        'department_id' => School::find(School::id())->department_id,
                        'enabled' => $user['enabled'],
                    ]);
                }
                $educator = $request->input('educator');
                self::find($request->input('id'))->update([
                    'user_id' => $request->input('user_id'),
                    'school_id' => $educator['school_id'],
                    'sms_quote' => 0,
                    'enabled' => $user['enabled'],
                ]);
                if (isset($educator['team_id'])) {
                    EducatorTeam::whereEducatorId($request->input('id'))->delete();
                    foreach ($educator['team_id'] as $key => $row) {
                        EducatorTeam::create([
                            'educator_id' => $request->input('id'),
                            'team_id' => $row,
                            'enabled' => $user['enabled'],
                        ]);
                    }
                }
                $classSubjectData = $request->input('classSubject');
                if ($classSubjectData) {
                    EducatorClass::whereEducatorId($request->input('id'))->delete();
                    $uniqueArray = [];
                    foreach ($classSubjectData['class_ids'] as $index => $class) {
                        $uniqueArray[] = [
                            'class_id' => $class,
                            'subject_id' => $classSubjectData['subject_ids'][$index],
                        ];
                    }
                    $classSubjects = self::array_unique_fb($uniqueArray);
                    foreach ($classSubjects as $key => $row) {
                        if ($row['class_id'] != 0 && $row['class_id'] != 0) {
                            EducatorClass::create([
                                'educator_id' => $request->input('id'),
                                'class_id' => $row['class_id'],
                                'subject_id' => $row['subject_id'],
                                'enabled' => $user['enabled'],
                            ]);
                        }
                    }
                }
                $mobiles = $request->input('mobile');
                if ($mobiles) {
                    Mobile::whereUserId($request->input('user_id'))->delete();
                    foreach ($mobiles as $k => $mobile) {
                        Mobile::create([
                            'user_id' => $request->input('user_id'),
                            'mobile' => $mobile['mobile'],
                            'isdefault' => $mobile['isdefault'],
                            'enabled' => $mobile['enabled'],
                        ]);
                    }
                }
                # 更新企业号成员
                User::UpdateWechatUser($request->input('user_id'));
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
     * @throws PHPExcel_Exception
     */
    static function upload(UploadedFile $file) {
        
        $ext = $file->getClientOriginalExtension();     // 扩展名//xls
        $realPath = $file->getRealPath();   //临时文件的绝对路径
        // 上传文件
        $filename = date('His') . uniqid() . '.' . $ext;
        $stored = Storage::disk('uploads')->put($filename, file_get_contents($realPath));
        if ($stored) {
            $filePath = 'storage/app/uploads/' . date('Y') . '/' . date('m') . '/' . date('d') . '/' . $filename;
            // var_dump($filePath);die;
            /** @var LaravelExcelReader $reader */
            $reader = Excel::load($filePath);
            try {
                $sheet = $reader->getExcel()->getSheet(0);
            } catch (PHPExcel_Exception $e) {
                throw $e;
            }
            $educators = $sheet->toArray();
            if (self::checkFileFormat($educators[0])) {
                return [
                    'error' => 1,
                    'message' => '文件格式错误',
                ];
            }
            unset($educators[0]);
            $educators = array_values($educators);
            if (count($educators) != 0) {
                # 去除表格的空数据
                foreach ($educators as $key => $v) {
                    if ((array_filter($v)) == null) {
                        unset($educators[$key]);
                    }
                }
                $rows = self::checkData($educators);
                if (!empty($rows)) {
                    event(new EducatorImported($rows));
                }
            }
            Storage::disk('uploads')->delete($filename);
            return [
                'error' => 0,
                'message' => '上传成功'
            ];
            
        }
        return [
            'error' => 2,
            'message' => '上传失败',
        ];
        
    }
    
    /**
     * 批量导出
     *
     * @param $id
     * @return array
     */
    static function export($id) {
        
        $educators = self::whereSchoolId($id)->get();
        $data = [self::EXCEL_EXPORT_TITLE];
        foreach ($educators as $educator) {
            if (!empty($educator)) {
                $item = [
                    $educator->user->realname,
                    $educator->school->name,
                    $educator->sms_quote,
                    $educator->created_at,
                    $educator->updated_at,
                    $educator->enabled == 1 ? '启用' : '禁用',
                ];
                $data[] = $item;
                unset($item);
            }
        }
        
        return $data;
        
    }
    
    /**
     * 教职员工列表
     *
     * @return array
     */
    static function datatable() {
        
        $columns = [
            ['db' => 'Educator.id', 'dt' => 0],
            ['db' => 'User.realname as username', 'dt' => 1],
            ['db' => 'School.name', 'dt' => 2],
            ['db' => 'Educator.sms_quote', 'dt' => 3],
            ['db' => 'Educator.created_at', 'dt' => 4],
            ['db' => 'Educator.updated_at', 'dt' => 5],
            [
                'db' => 'Educator.enabled', 'dt' => 6,
                'formatter' => function ($d, $row) {
                    $id = $row['id'];
                    $status = $d ? Datatable::DT_ON : Datatable::DT_OFF;
                    $user = Auth::user();
                    $showLink = sprintf(Datatable::DT_LINK_SHOW, 'show_' . $id) .
                        str_repeat(Datatable::DT_SPACE, 3);
                    $editLink = sprintf(Datatable::DT_LINK_EDIT, 'edit_' . $id) .
                        str_repeat(Datatable::DT_SPACE, 2);
                    $delLink = sprintf(Datatable::DT_LINK_DEL, $id) .
                        str_repeat(Datatable::DT_SPACE, 2);
                    $rechargeLink = sprintf(Datatable::DT_LINK_RECHARGE, 'recharge_' . $id);
                    return
                        $status . str_repeat(Datatable::DT_SPACE, 3) .
                        ($user->can('act', self::uris()['show']) ? $showLink : '') .
                        ($user->can('act', self::uris()['edit']) ? $editLink : '') .
                        ($user->can('act', self::uris()['destroy']) ? $delLink : '') .
                        ($user->can('act', self::uris()['recharge']) ? $rechargeLink : '');
                },
            ],
        ];
        $joins = [
            [
                'table' => 'users',
                'alias' => 'User',
                'type' => 'INNER',
                'conditions' => [
                    'User.id = Educator.user_id',
                ],
            ],
            [
                'table' => 'schools',
                'alias' => 'School',
                'type' => 'INNER',
                'conditions' => [
                    'School.id = Educator.school_id',
                ],
            ],
        ];
        $condition = 'Educator.school_id = ' . School::id();
        
        return Datatable::simple(self::getModel(), $columns, $joins, $condition);

    }
    
    /**
     * 去掉二维数组中的重复值
     *
     * @param $array2D
     * @return array
     */
    private static function array_unique_fb($array2D) {
        
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
     * 检查表头是否合法
     *
     * @param array $fileTitle
     * @return bool
     */
    private static function checkFileFormat(array $fileTitle) {

        return count(array_diff(self::EXCEL_FILE_TITLE, $fileTitle)) != 0;

    }

    private static function checkData(array $data) {
        
        $rules = [
            'name' => 'required|string|between:2,6',
            'gender' => ['required', Rule::in(['男', '女'])],
            'birthday' => ['required', 'string', 'regex:/^((19\d{2})|(20\d{2}))-([1-12])-([1-31])$/'],
            'school' => 'required|string|between:4,20',
            'mobile' => 'required', new Mobiles(),
            'grades' => 'string',
            'classes' => 'string',
            'subjects' => 'string',
            'educators_classes' => 'string',
            'departments' => 'required|string',
        ];
        // Validator::make($data,$rules);
        # 不合法的数据
        $invalidRows = [];
        # 需要添加的数据
        $rows = [];
        for ($i = 0; $i < count($data); $i++) {
            $datum = $data[$i];
            $user = [
                'name' => $datum[0],
                'gender' => $datum[1],
                'birthday' => $datum[2],
                'school' => $datum[3],
                'mobile' => $datum[4],
                'grades' => $datum[5],
                'classes' => $datum[6],
                'subjects' => $datum[7],
                'educators_classes' => $datum[8],
                'departments' => $datum[9],
            ];
            $status = Validator::make($user, $rules);
            if ($status->fails()) {
                $invalidRows[] = $datum;
                unset($data[$i]);
                continue;
            }
            $school = School::whereName($user['school'])->first();
            if (!$school) {
                $invalidRows[] = $datum;
                unset($data[$i]);
                continue;
            }
            $departments = explode(',', $user['departments']);
            foreach ($departments as $d) {
                $department = Department::whereName($d)->first();
                if (empty($department)) {
                    $invalidRows[] = $datum;
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

