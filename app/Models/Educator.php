<?php

namespace App\Models;

use App\Events\EducatorImported;
use App\Events\SchoolDeleted;
use App\Facades\DatatableFacade as Datatable;
use App\Helpers\ModelTrait;
use App\Http\Requests\CustodianRequest;
use App\Http\Requests\EducatorRequest;
use App\Rules\Mobiles;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Http\UploadedFile;
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
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
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

    use ModelTrait;
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
    public function gradeDeans($gradeId) {

        $educatorIds = Grade::whereId($gradeId)->where('enabled', 1)->first()->educator_ids;
        return $this->whereIn('id', explode(',', $educatorIds))->whereEnabled(1)->get();

    }

    /**
     * 获取指定班级的班级主任教职员工对象
     *
     * @param $classId
     * @return Collection|static[]
     */
    public function classDeans($classId) {

        $educatorIds = Squad::whereId($classId)->where('enabled', 1)->first()->educator_ids;
        return $this->whereIn('id', explode(',', $educatorIds))->whereEnabled(1)->get();

    }

    /**
     * 获取指定学校的教职员工列表
     *
     * @param array $schoolIds
     * @return array
     */
    public function educators(array $schoolIds = []) {

        $educatorList = [];
        if (empty($schoolIds)) {
            $educators = $this->all();
        } else {
            $educators = $this->whereIn('school_id', $schoolIds)->get();
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
    public function getEducatorListByIds(array $ids) {

        $educators = [];
        foreach ($ids as $id) {
            $educator = $this->find($id);
            $educators[$id] = $educator->user->realname;
        }
        return $educators;

    }

    public function datatable() {

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
                    $showLink = sprintf(Datatable::DT_LINK_SHOW, 'show_' . $id);
                    $editLink = sprintf(Datatable::DT_LINK_EDIT, 'edit_' . $id);
                    $delLink = sprintf(Datatable::DT_LINK_DEL, $id);
                    $rechargeLink = sprintf(Datatable::DT_LINK_RECHARGE, 'recharge_' . $id);
                    return
                        $status . str_repeat(Datatable::DT_SPACE, 3) .
                        $showLink . str_repeat(Datatable::DT_SPACE, 3) .
                        $editLink . str_repeat(Datatable::DT_SPACE, 2) .
                        $delLink . str_repeat(Datatable::DT_SPACE, 2) .
                        $rechargeLink;
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
        $school = new School();
        $schoolId = $school->getSchoolId();
        $condition = 'Educator.school_id = ' . $schoolId;
        return Datatable::simple($this, $columns, $joins, $condition);

    }

    /**
     * 保存新创建的教职员工记录
     *
     * @param EducatorRequest|CustodianRequest $request
     * @return bool|mixed
     * @throws Exception
     */
    public function store(EducatorRequest $request) {

        try {
            DB::transaction(function () use ($request) {
                // dd($request->all());
                $userInputData = $request->input('user');
                $userData = [
                    'username' => $userInputData['username'],
                    'group_id' => $userInputData['group_id'],
                    'password' => bcrypt($userInputData['password']),
                    'email' => $userInputData['email'],
                    'realname' => $userInputData['realname'],
                    'gender' => $userInputData['gender'],
                    'avatar_url' => '00001.jpg',
                    'userid' => 'user_' . uniqid(),
                    'wechatid' => $userInputData['wechatid'],
                    'isleader' => 0,
                    'english_name' => $userInputData['english_name'],
                    'telephone' => $userInputData['telephone'],
                    'enabled' => $userInputData['enabled'],
                ];
                $user = new User();
                $u = $user->create($userData);
                # 教职员工
                $educatorInputData = $request->input('educator');
                $educatorData = [
                    'user_id' => $u->id,
                    'school_id' => $educatorInputData['school_id'],
                    'sms_quote' => 0,
                    'enabled' => $userInputData['enabled'],
                ];
                $educator = $this->create($educatorData);
                # 部门信息
                $selectedDepartments = $request->input('selectedDepartments');
                if (!empty($selectedDepartments)) {
                    foreach ($selectedDepartments as $department) {
                        $departmentData = [
                            'user_id' => $u->id,
                            'department_id' => $department,
                            'enabled' => $userInputData['enabled'],
                        ];
                        DepartmentUser::create($departmentData);
                    }
                }
                # 班级科目
                $classSubjectData = $request->input('classSubject');
                if ($classSubjectData['class_ids'] && $classSubjectData['subject_ids']) {
                    $uniqueArray = [];
                    foreach ($classSubjectData['class_ids'] as $index => $class) {
                        $uniqueArray[] = [
                            'class_id' => $class,
                            'subject_id' => $classSubjectData['subject_ids'][$index],
                        ];
                    }
                    $classSubjects = $this->array_unique_fb($uniqueArray);
                    foreach ($classSubjects as $key => $row) {
                        if ($row['class_id'] != 0 && $row['class_id'] != 0) {
                            $educatorClassData = [
                                'educator_id' => $educator->id,
                                'class_id' => $row['class_id'],
                                'subject_id' => $row['subject_id'],
                                'enabled' => $userInputData['enabled'],
                            ];
                            $educatorClass->create($educatorClassData);

                        }

                    }
                    unset($educatorClass);
                }


//
//                $classIds = array_unique($classSubjectData['class_ids']);
//                $departmentIds = [];
//                foreach ($classIds as $classId) {
//                    if ($classId == 0) break;
//                    $departmentIds[] = Squad::find($classId)->department_id;
//                }
//                $selectedDepartments = array_unique(array_merge(
//                    $departmentIds, $request->input('selectedDepartments')
//                ));
//                if (!empty($selectedDepartments)) {
//                    $departmentUserModel = new DepartmentUser();
//                    foreach ($selectedDepartments as $department) {
//                        $departmentData = [
//                            'user_id' => $u->id,
//                            'department_id' => $department,
//                            'enabled' => $userInputData['enabled'],
//                        ];
//                        $departmentUserModel->create($departmentData);
//                    }
//                    unset($departmentUserModel);
//                }





                if (isset($educatorInputData['team_id'])) {
                    $edTeam = new EducatorTeam();
                    foreach ($educatorInputData['team_id'] as $key => $row) {
                        $edData = [
                            'educator_id' => $educator->id,
                            'team_id' => $row,
                            'enabled' => $userInputData['enabled'],
                        ];
                        $edTeam->create($edData);
                    }
                    unset($edTeam);
                }

                if ($classSubjectData) {
                    $uniqueArray = [];
                    foreach ($classSubjectData['class_ids'] as $index => $class) {
                        $uniqueArray[] = [
                            'class_id' => $class,
                            'subject_id' => $classSubjectData['subject_ids'][$index],
                        ];
                    }
                    $classSubjects = $this->array_unique_fb($uniqueArray);
                    foreach ($classSubjects as $key => $row) {
                        if ($row['class_id'] != 0 && $row['class_id'] != 0) {
                            $educatorClassData = [
                                'educator_id' => $educator->id,
                                'class_id' => $row['class_id'],
                                'subject_id' => $row['subject_id'],
                                'enabled' => $userInputData['enabled'],
                            ];
                            EducatorClass::create($educatorClassData);

                        }

                    }
                }
                $mobiles = $request->input('mobile');
                if ($mobiles) {
                    $mobileModel = new Mobile();
                    foreach ($mobiles as $k => $mobile) {
                        $mobileData = [
                            'user_id' => $u->id,
                            'mobile' => $mobile['mobile'],
                            'isdefault' => $mobile['isdefault'],
                            'enabled' => $mobile['enabled'],
                        ];
                        $mobileModel->create($mobileData);
                    }
                    unset($mobile);
                }
                # 创建企业号成员
                $user->createWechatUser($u->id);
                unset($user);
            });
        } catch (Exception $e) {
            throw $e;
        }
        return true;
    }

    //二维数组去掉重复值
    function array_unique_fb($array2D) {
        $temp = [];
        foreach ($array2D as $v) {
            $v = join(',', $v); //降维,也可以用implode,将一维数组转换为用逗号连接的字符串
            $temp[] = $v;
        }
        $tempUnique = array_unique($temp); //去掉重复的字符串,也就是重复的一维数组
        $csArray = [];
        foreach ($tempUnique as $k => $v) {
            $tempArray = explode(',', $v); //再将拆开的数组重新组装
            $csArray[$k]['class_id'] = $tempArray[0];
            $csArray[$k]['subject_id'] = $tempArray[1];
        }
        return $csArray;
    }

    /**
     * 修改教职员工
     *
     * @param EducatorRequest $request
     * @return bool|mixed
     * @throws Exception
     */
    public function modify(EducatorRequest $request) {

        try {
            DB::transaction(function () use ($request) {

                // dd($request->all());die;
                $userInputData = $request->input('user');
                $userData = [
                    'username' => $userInputData['username'],
                    'group_id' => $userInputData['group_id'],
                    'email' => $userInputData['email'],
                    'realname' => $userInputData['realname'],
                    'gender' => $userInputData['gender'],
                    'avatar_url' => '00001.jpg',
                    'userid' => '111111',
                    'wechatid' => $userInputData['wechatid'],
                    'isleader' => 0,
                    'english_name' => $userInputData['english_name'],
                    'telephone' => $userInputData['telephone'],
                    'enabled' => $userInputData['enabled'],
                ];
                $user = new User();
                $user->where('id', $request->input('user_id'))->update($userData);
                $selectedDepartments = $request->input('selectedDepartments');
                if (!empty($selectedDepartments)) {
                    $departmentUserModel = new DepartmentUser();
                    $departmentUserModel->where('user_id', $request->input('user_id'))->delete();
                    foreach ($selectedDepartments as $department) {
                        $departmentData = [
                            'user_id' => $request->input('user_id'),
                            'department_id' => $department,
                            'enabled' => $userInputData['enabled'],
                        ];
                        $departmentUserModel->create($departmentData);
                    }
                    unset($departmentUserModel);
                }
                $educator = $request->input('educator');
                $educatorData = [
                    'user_id' => $request->input('user_id'),
                    'school_id' => $educator['school_id'],
                    'sms_quote' => 0,
                    'enabled' => $userInputData['enabled'],
                ];
                $this->where('id', $request->input('id'))->update($educatorData);
                if (isset($educator['team_id'])) {
                    $edTeam = new EducatorTeam();
                    $edTeam->where('educator_id', $request->input('id'))->delete();
                    foreach ($educator['team_id'] as $key => $row) {
                        $edData = [
                            'educator_id' => $request->input('id'),
                            'team_id' => $row,
                            'enabled' => $userInputData['enabled'],
                        ];
                        $edTeam->create($edData);
                    }
                    unset($edTeam);
                }
                $classSubjectData = $request->input('classSubject');
                if ($classSubjectData) {
                    $educatorClass = new EducatorClass();
                    $educatorClass->where('educator_id', $request->input('id'))->delete();
                    $uniqueArray = [];
                    foreach ($classSubjectData['class_ids'] as $index => $class) {
                        $uniqueArray[] = [
                            'class_id' => $class,
                            'subject_id' => $classSubjectData['subject_ids'][$index],
                        ];
                    }
                    $classSubjects = $this->array_unique_fb($uniqueArray);
                    foreach ($classSubjects as $key => $row) {
                        if ($row['class_id'] != 0 && $row['class_id'] != 0) {
                            $educatorClassData = [
                                'educator_id' => $request->input('id'),
                                'class_id' => $row['class_id'],
                                'subject_id' => $row['subject_id'],
                                'enabled' => $userInputData['enabled'],
                            ];
                            $educatorClass->create($educatorClassData);

                        }

                    }
                    unset($educatorClass);
                }
                $mobiles = $request->input('mobile');
                if ($mobiles) {
                    $mobileModel = new Mobile();
                    $mobileModel->where('user_id', $request->input('user_id'))->delete();
                    foreach ($mobiles as $k => $mobile) {
                        $mobileData = [
                            'user_id' => $request->input('user_id'),
                            'mobile' => $mobile['mobile'],
                            'isdefault' => $mobile['isdefault'],
                            'enabled' => $mobile['enabled'],
                        ];
                        $mobileModel->create($mobileData);
                    }
                    unset($mobile);
                }
                # 更新企业号成员
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
     * @param bool $fireEvent
     * @return bool
     * @throws Exception
     */
    public function remove($id, $fireEvent = false) {

        $school = $this->find($id);
        /** @var School $school */
        $removed = $this->removable($school) ? $school->delete() : false;
        if ($removed && $fireEvent) {
            /** @var School $school */
            event(new SchoolDeleted($school));
            return true;
        }
        
        return $removed ? true : false;

    }
    
    /**
     * 导入
     *
     * @param UploadedFile $file
     * @return array
     * @throws PHPExcel_Exception
     */
    public function upload(UploadedFile $file) {

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
            if ($this->checkFileFormat($educators[0])) {
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
                $rows = $this->checkData($educators);
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
     * 检查表头是否合法
     * @param array $fileTitle
     * @return bool
     */
    private function checkFileFormat(array $fileTitle) {

        return count(array_diff(self::EXCEL_FILE_TITLE, $fileTitle)) != 0;

    }

    private function checkData(array $data) {
        
        $rules = [
            'name' => 'required|string|between:2,6',
            'gender' => [
                'required',
                Rule::in(['男', '女']),
            ],
            'birthday' => ['required', 'string', 'regex:/^((19\d{2})|(20\d{2}))-([1-12])-([1-31])$/'],
            'school' => 'required|string|between:4,20',
            'mobile' => 'required', new Mobiles(),
            'grades' => 'string',
            'classes' => 'string',
//            'subjects' => 'string',
//            'educators_classes' => 'string',
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
        // print_r($rows);die;
        return $rows;
    }

    public function export($id) {
        
        $educators = $this->where('school_id', $id)->get();
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
    
}

