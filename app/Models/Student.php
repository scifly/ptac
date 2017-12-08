<?php

namespace App\Models;

use App\Events\StudentImported;
use App\Events\StudentUpdated;
use App\Facades\DatatableFacade as Datatable;
use App\Http\Requests\StudentRequest;
use App\Rules\Mobiles;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Readers\LaravelExcelReader;

/**
 * App\Models\Student
 *
 * @property int $id
 * @property int $user_id 用户ID
 * @property int $class_id 班级ID
 * @property string $student_number 学号
 * @property string $card_number 卡号
 * @property int $oncampus 是否住校
 * @property string $birthday 生日
 * @property string $remark 备注
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \App\Models\User $user
 * @method static Builder|Student whereBirthday($value)
 * @method static Builder|Student whereCardNumber($value)
 * @method static Builder|Student whereClassId($value)
 * @method static Builder|Student whereCreatedAt($value)
 * @method static Builder|Student whereId($value)
 * @method static Builder|Student whereOncampus($value)
 * @method static Builder|Student whereRemark($value)
 * @method static Builder|Student whereStudentNumber($value)
 * @method static Builder|Student whereUpdatedAt($value)
 * @method static Builder|Student whereUserId($value)
 * @mixin \Eloquent
 * @property int $enabled
 * @property-read \App\Models\Squad $beLongsToSquad
 * @property-read Collection|CustodianStudent[] $custodianStudent
 * @property-read Collection|Score[] $score
 * @property-read Collection|ScoreTotal[] $scoreTotal
 * @property-read \App\Models\Squad $squad
 * @method static Builder|Student whereEnabled($value)
 * @property-read Collection|Custodian[] $custodians
 * @property-read Collection|ScoreTotal[] $scoreTotals
 * @property-read Collection|Score[] $scores
 * @property-read Collection|CustodianStudent[] $custodianStudents
 */
class Student extends Model {

    const EXCEL_FILE_TITLE = [
        '姓名', '性别', '生日', '学校',
        '年级', '班级', '手机号码',
        '学号', '卡号', '住校',
        '备注', '监护关系',
    ];
    const EXCEL_EXPORT_TITLE = [
        '姓名', '性别', '班级', '学号',
        '卡号', '住校', '手机',
        '生日', '创建于', '更新于',
        '状态',
    ];
    protected $fillable = [
        'user_id', 'class_id', 'student_number',
        'card_number', 'oncampus', 'birthday',
        'remark', 'enabled',
    ];

    /**
     * 返回指定学生所属的班级对象
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function squad() { return $this->belongsTo('App\Models\Squad', 'class_id', 'id'); }

    /**
     * 获取指定学生的所有监护人对象
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function custodians() {

        return $this->belongsToMany('App\Models\Custodian', 'custodians_students');

    }

    /**
     * 获取指定学生对应的用户对象
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user() { return $this->belongsTo('App\Models\User'); }

    /**
     * 获取指定学生所有的分数对象
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function scores() { return $this->hasMany('App\Models\Score'); }

    /**
     * 获取指定学生所有的总分对象
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function scoreTotals() { return $this->hasMany('App\Models\ScoreTotal'); }

    /**
     * 返回学生列表
     *
     * @param array $classIds
     * @return array
     */
    public function students(array $classIds = []) {

        $studentList = [];
        if (empty($classIds)) {
            $students = $this->all();
        } else {
            $students = $this->whereIn('class_id', $classIds)->get();
        }
        foreach ($students as $student) {
            $studentList[$student->id] = $student->user->realname;
        }
        return $studentList;

    }

    /**
     * 保存新创建的学生记录
     * @param StudentRequest $request
     * @return bool|mixed
     * @throws Exception
     */
    public function store(StudentRequest $request) {

        try {
            DB::transaction(function () use ($request) {

                $user = $request->input('user');
                $userData = [
                    'username' => uniqid('custodian_'),
                    'group_id' => Group::whereName('学生')->first()->id,
                    'password' => 'student8888',
                    'email' => $user['email'],
                    'realname' => $user['realname'],
                    'gender' => $user['gender'],
                    'avatar_url' => '00001.jpg',
                    'userid' => uniqid('student_'),
                    'isleader' => 0,
                    'english_name' => $user['english_name'],
                    'telephone' => $user['telephone'],
                    'wechatid' => '',
                    'enabled' => $user['enabled'],
                ];
                $userModel = new User();
                $u = $userModel->create($userData);
                $student = $request->all();
                $studentData = [
                    'user_id' => $u->id,
                    'class_id' => $student['class_id'],
                    'student_number' => $student['student_number'],
                    'card_number' => $student['card_number'],
                    'oncampus' => $student['oncampus'],
                    'birthday' => $student['birthday'],
                    'remark' => $user['remark'],
                    'enabled' => $userData['enabled'],
                ];
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
                    unset($mobileModel);
                }
                # 向student表添加数据
                $this->create($studentData);

                # 创建部门成员
                $school = new School();
                $schoolName = School::whereId($school->getSchoolId())->first();
                $gradeName = Grade::whereId($student['grade_id'])->first();
                $className = Squad::whereId($student['class_id'])->first();
                $deptId = $this->getDeptId($schoolName->name, $gradeName->name, $className->name);

                $departmentUser = [
                    'department_id' => $deptId,
                    'user_id' => $u->id,
                    'enabled' => 1,
                ];
                DepartmentUser::create($departmentUser);
                # 创建企业号成员
                $userModel->createWechatUser($u->id);
                unset($user);
            });
        } catch (Exception $e) {
            throw $e;
        }
        return true;
    }

    public function getDeptId($school, $grade, $class) {
        $deptSchool = Department::where('name', $school)->first();
        if ($deptSchool) {
            $deptGrade = Department::where('name', $grade)->where('parent_id', $deptSchool->id)->first();
            if ($deptGrade) {
                $deptClass = Department::where('name', $class)->where('parent_id', $deptGrade->id)->first();
                return $deptClass->id;
            }
            return 0;
        }
        return 0;
    }

    /**
     * 返回表单 年级和班级的下拉框数据
     * @param int $gradeId
     * @return array
     */
    public function getGradeClass($gradeId = 0) {
        $school = new School();
        $schoolId = $school->getSchoolId();
        $grades = Grade::whereEnabled(1)
            ->where('school_id', $schoolId)
            ->pluck('name', 'id')
            ->toArray();
        $gradeId = $gradeId == 0 ? array_keys($grades)[0] : $gradeId;
        $classes = Squad::whereEnabled(1)
            ->where('grade_id', $gradeId)
            ->pluck('name', 'id')
            ->toArray();
        return [
            'grades' => $grades,
            'classes' => $classes,
        ];
    }

    /**
     * 返回学生学号姓名列表
     *
     * @param $classIds
     * @return array
     */
    public function studentsNum($classIds) {

        $studentList = [];
        $students = $this->whereIn('class_id', explode(',', $classIds))->get();
        foreach ($students as $student) {
            $studentList[] = [$student->student_number, $student->user->realname];
        }
        return $studentList;

    }

    /**
     * 更新指定的学生记录
     *
     * @param StudentRequest $request
     * @param $studentId
     * @return bool|mixed
     * @throws Exception
     */
    public function modify(StudentRequest $request, $studentId) {

        $student = $this->find($studentId);
        if (!isset($student)) {
            return false;
        }
        try {
            DB::transaction(function () use ($request, $studentId, $student) {

                $userId = $request->input('user_id');
                $userData = $request->input('user');
                $user = new User();
                $user->where('id', $userId)
                    ->update([
                        'group_id' => Group::whereName('学生')->first()->id,
                        'email' => $userData['email'],
                        'realname' => $userData['realname'],
                        'gender' => $userData['gender'],
                        'isleader' => 0,
                        'english_name' => $userData['english_name'],
                        'telephone' => $userData['telephone'],
                        'enabled' => $userData['enabled'],
                    ]);
                $studentData = $request->all();
                $student->update([
                    'user_id' => $userId,
                    'class_id' => $studentData['class_id'],
                    'student_number' => $studentData['student_number'],
                    'card_number' => $studentData['card_number'],
                    'oncampus' => $studentData['oncampus'],
                    'birthday' => $studentData['birthday'],
                    'remark' => $userData['remark'],
                    'enabled' => $userData['enabled'],
                ]);
                $mobiles = $request->input('mobile');
                if ($mobiles) {
                    $mobileModel = new Mobile();
                    $delMobile = $mobileModel->where('user_id', $userId)->delete();
                    if ($delMobile) {
                        foreach ($mobiles as $k => $mobile) {
                            $mobileData = [
                                'user_id' => $request->input('user_id'),
                                'mobile' => $mobile['mobile'],
                                'isdefault' => $mobile['isdefault'],
                                'enabled' => $mobile['enabled'],
                            ];
                            $mobileModel->create($mobileData);
                        }
                    }
                    unset($mobile);
                }
                # 创建部门成员
                DepartmentUser::where('user_id', $userId)->delete();
                $school = new School();
                $schoolName = School::whereId($school->getSchoolId())->first();
                $gradeName = Grade::whereId($studentData['grade_id'])->first();
                $className = Squad::whereId($studentData['class_id'])->first();
                $deptId = $this->getDeptId($schoolName->name, $gradeName->name, $className->name);

                $departmentUser = [
                    'department_id' => $deptId,
                    'user_id' => $userId,
                    'enabled' => 1,
                ];
                DepartmentUser::create($departmentUser);
                # 更新企业号成员
                $user->UpdateWechatUser($userId);
                unset($user);
            });
        } catch (Exception $e) {
            throw $e;
        }
        return true;

    }

    /**
     * 删除指定的学生记录
     *
     * @param $studentId
     * @return bool|mixed
     * @throws Exception
     */
    public function remove($studentId) {

        $student = $this->find($studentId);
        if (!isset($custodian)) {
            return false;
        }
        try {
            DB::transaction(function () use ($studentId, $student) {
                # 删除指定的学生记录
                $student->delete();
                # 删除与指定学生绑定的监护人记录
                CustodianStudent::where('student_id', $studentId)->delete();
                # 删除与指定学生绑定的部门记录
                DepartmentUser::where('user_id', $student['user_id'])->delete();
                # 删除与指定学生绑定的手机记录
                Mobile::where('user_id', $student['user_id'])->delete();

            });
        } catch (Exception $e) {
            throw $e;
        }
        return false;
    }
    
    /**
     * 导入
     *
     * @param UploadedFile $file
     * @return array
     * @throws \PHPExcel_Exception
     */
    public function upload(UploadedFile $file) {

        $ext = $file->getClientOriginalExtension();     // 扩展名//xls
        $realPath = $file->getRealPath();   //临时文件的绝对路径
        // 上传文件
        $filename = date('His') . uniqid() . '.' . $ext;
        $bool = Storage::disk('uploads')->put($filename, file_get_contents($realPath));
        if ($bool) {
            $filePath = 'storage/app/uploads/' . date('Y') . '/' . date('m') . '/' . date('d') . '/' . $filename;
            // var_dump($filePath);die;
            /** @var LaravelExcelReader $reader */
            $reader = Excel::load($filePath);
            $sheet = $reader->getExcel()->getSheet(0);
            $students = $sheet->toArray();
            if ($this->checkFileFormat($students[0])) {
                return [
                    'error' => 1,
                    'message' => '文件格式错误',
                ];
            }
            unset($students[0]);
            $students = array_values($students);
            if (count($students) != 0) {
                # 去除表格的空数据
                foreach ($students as $key => $v) {
                    if ((array_filter($v)) == null) {
                        unset($students[$key]);
                    }
                }
                $this->checkData($students);
            }
            return [
                'error' => 0,
                'message' => '上传成功',
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

    /**
     *  检查每行数据 是否符合导入数据
     * @param array $data
     */
    private function checkData(array $data) {
        $rules = [
            'name' => 'required|string|between:2,6',
            'gender' => [
                'required',
                Rule::in(['男', '女']),
            ],
            'birthday' => ['required', 'string', 'regex:/^((19\d{2})|(20\d{2}))-([1-12])-([1-31])$/'],
            'school' => 'required|string|between:4,20',
            'grade' => 'required|string|between:3,20',
            'class' => 'required|string|between:2,20',
            'mobile' => 'required', new Mobiles(),
            'student_number' => 'required|alphanum|between:2,32',
            'card_number' => 'required|alphanum|between:2,32',
            'oncampus' => [
                'required',
                Rule::in(['住读', '走读']),
            ],
            'remark' => 'string|nullable',
            'relationship' => 'string',
        ];
        // Validator::make($data,$rules);
        # 不合法的数据
        $invalidRows = [];
        # 更新的数据
        $updateRows = [];
        # 需要添加的数据
        $rows = [];
        for ($i = 0; $i < count($data); $i++) {
            $datum = $data[$i];
            $user = [
                'name' => $datum[0],
                'gender' => $datum[1],
                'birthday' => $datum[2],
                'school' => $datum[3],
                'grade' => $datum[4],
                'class' => $datum[5],
                'mobile' => $datum[6],
                'student_number' => $datum[7],
                'card_number' => $datum[8],
                'oncampus' => $datum[9],
                'remark' => $datum[10],
                'relationship' => $datum[11],
                'class_id' => 0,
                'department_id' => 0,
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
            $grade = Grade::where('name', $user['grade'])
                ->where('school_id', $school->id)
                ->first();
            # 数据非法
            if (!$grade) {
                $invalidRows[] = $datum;
                unset($data[$i]);
                continue;
            }
            $class = Squad::where('name', $user['class'])
                ->where('grade_id', $grade->id)
                ->first();
            if (!$class) {
                $invalidRows[] = $datum;
                unset($data[$i]);
                continue;
            }
            $student = Student::where('student_number', $user['student_number'])
                ->where('class_id', $class->id)
                ->first();
            $user['class_id'] = $class->id;
            $deptId = $this->getDeptId($user['school'], $user['grade'], $user['class']);
            $user['department_id'] = $deptId;


            if ($user['department_id'] == 0) {
                $invalidRows[] = $datum;
                unset($data[$i]);
                continue;
            }
            # 学生数据已存在 更新操作
            if ($student) {
                $updateRows[] = $user;
            } else {
                $rows[] = $user;
            }
            unset($user);
        }
        // print_r($rows);die;
        event(new StudentUpdated($updateRows));
        event(new StudentImported($rows));
    }

    /**
     *  导出数据
     * @param $id
     * @return array
     */
    public function export($id) {
        $students = $this->where('class_id', $id)->get();
        $data = array(self::EXCEL_EXPORT_TITLE);
        foreach ($students as $student) {
            if (!empty($student)) {
                $m = $student->user->mobiles;
                $mobile = [];
                foreach ($m as $key => $value) {
                    $mobile[] = $value->mobile;
                }
                $mobiles = implode(',', $mobile);
                $item = [
                    $student->user->realname,
                    $student->user->gender == 1 ? '男' : '女',
                    $student->squad->name,
                    $student->student_number,
                    $student->card_number . "\t",
                    $student->oncampus == 1 ? '是' : '否',
                    $mobiles,
                    substr($student->birthday, 0, -8),
                    $student->created_at,
                    $student->updated_at,
                    $student->enabled == 1 ? '启用' : '禁用',
                ];
                $data[] = $item;
                unset($item);
            }

        }

        return $data;
    }

    public function datatable() {

        $columns = [
            ['db' => 'Student.id', 'dt' => 0],
            ['db' => 'User.realname as realname', 'dt' => 1],
            [
                'db' => 'User.gender as gender', 'dt' => 2,
                'formatter' => function ($d) {
                    return $d == 1 ? '<i class="fa fa-mars"></i>' : '<i class="fa fa-venus"></i>';
                },
            ],
            [
                'db' => 'Squad.name as classname', 'dt' => 3,
                'formatter' => function ($d) {
                    return '<i class="fa fa-users"></i>&nbsp;' . $d;
                },
            ],
            ['db' => 'Student.student_number', 'dt' => 4],
            ['db' => 'Student.card_number', 'dt' => 5],
            [
                'db' => 'Student.oncampus', 'dt' => 6,
                'formatter' => function ($d) {
                    return $d == 1 ? '是' : '否';
                },
            ],
            [
                'db' => 'Student.id as mobile', 'dt' => 7,
                'formatter' => function ($d) {
                    $student = $this->find($d);
                    $mobiles = $student->user->mobiles;
                    $mobile = [];
                    foreach ($mobiles as $key => $value) {
                        $mobile[] = $value->mobile;
                    }
                    return implode(',', $mobile);
                },
            ],
            [
                'db' => 'Student.birthday', 'dt' => 8,
                'formatter' => function ($d) {
                    return substr($d, 0, -8);
                },
            ],
            ['db' => 'Student.created_at', 'dt' => 9],
            ['db' => 'Student.updated_at', 'dt' => 10],
            [
                'db' => 'Student.enabled', 'dt' => 11,
                'formatter' => function ($d, $row) {
                    return Datatable::dtOps($d, $row);
                },
            ],
        ];
        $joins = [
            [
                'table' => 'users',
                'alias' => 'User',
                'type' => 'INNER',
                'conditions' => [
                    'User.id = Student.user_id',
                ],
            ],
            [
                'table' => 'classes',
                'alias' => 'Squad',
                'type' => 'INNER',
                'conditions' => [
                    'Squad.id = Student.class_id',
                ],
            ],
            [
                'table' => 'grades',
                'alias' => 'Grade',
                'type' => 'INNER',
                'conditions' => [
                    'Grade.id = Squad.grade_id',
                ],
            ],
        ];
        $school = new School();
        $schoolId = $school->getSchoolId();
        $condition = 'Grade.school_id = ' . $schoolId;
        return Datatable::simple($this, $columns, $joins, $condition);

    }

}
