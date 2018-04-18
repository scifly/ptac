<?php
namespace App\Models;

use App\Events\ContactImportTrigger;
use App\Events\StudentImported;
use App\Events\StudentUpdated;
use App\Facades\DatatableFacade as Datatable;
use App\Helpers\Constant;
use App\Helpers\HttpStatusCode;
use App\Helpers\ModelTrait;
use App\Helpers\Snippet;
use App\Rules\Mobiles;
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
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Throwable;

/**
 * App\Models\Student 学生
 *
 * @property int $id
 * @property int $user_id 用户ID
 * @property int $class_id 班级ID
 * @property string $student_number 学号
 * @property string $card_number 卡号
 * @property int $oncampus 是否住校
 * @property string $birthday 生日
 * @property string $remark 备注
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int $enabled
 * @property-read Collection|Custodian[] $custodians
 * @property-read Collection|ScoreTotal[] $scoreTotals
 * @property-read Collection|Score[] $scores
 * @property-read Squad $squad
 * @property-read User $user
 * @method static Builder|Student whereBirthday($value)
 * @method static Builder|Student whereCardNumber($value)
 * @method static Builder|Student whereClassId($value)
 * @method static Builder|Student whereCreatedAt($value)
 * @method static Builder|Student whereEnabled($value)
 * @method static Builder|Student whereId($value)
 * @method static Builder|Student whereOncampus($value)
 * @method static Builder|Student whereRemark($value)
 * @method static Builder|Student whereStudentNumber($value)
 * @method static Builder|Student whereUpdatedAt($value)
 * @method static Builder|Student whereUserId($value)
 * @mixin Eloquent
 */
class Student extends Model {
    
    use ModelTrait;
    
    const EXCEL_FILE_TITLE = [
        '姓名', '性别', '生日', '学校',
        '年级', '班级', '手机号码',
        '学号', '卡号', '住校',
        '备注', '监护关系',
    ];
    const EXPORT_TITLES = [
        '姓名', '性别', '班级', '学号',
        '卡号', '住校', '手机',
        '生日', '创建于', '更新于',
        '状态',
    ];
    const EXPORT_RANGES = [
        'class' => 0,
        'grade' => 1,
        'all'   => 2
    ];
    protected $fillable = [
        'user_id', 'class_id', 'student_number',
        'card_number', 'oncampus', 'birthday',
        'remark', 'enabled',
    ];
    
    
    /**
     * 返回指定学生所属的班级对象
     *
     * @return BelongsTo
     */
    function squad() { return $this->belongsTo('App\Models\Squad', 'class_id', 'id'); }
    
    /**
     * 获取指定学生的所有监护人对象
     *
     * @return BelongsToMany
     */
    function custodians() {
        
        return $this->belongsToMany('App\Models\Custodian', 'custodians_students');
        
    }
    
    /**
     * 获取指定学生对应的用户对象
     *
     * @return BelongsTo
     */
    function user() { return $this->belongsTo('App\Models\User'); }
    
    /**
     * 获取指定学生所有的分数对象
     *
     * @return HasMany
     */
    function scores() { return $this->hasMany('App\Models\Score'); }
    
    /**
     * 获取指定学生所有的总分对象
     *
     * @return HasMany
     */
    function scoreTotals() { return $this->hasMany('App\Models\ScoreTotal'); }
    
    /**
     * 获取指定学生的所有消费/充值记录
     *
     * @return HasMany
     */
    function consumptions() { return $this->hasMany('App\Models\Consumption'); }
    
    /**
     * 保存新创建的学生记录
     *
     * @param array $data
     * @return bool|mixed
     * @throws Exception
     * @throws \Throwable
     */
    function store(array $data) {
        
        try {
            DB::transaction(function () use ($data) {
                # 创建用户
                $userid = uniqid('student_');
                $user = User::create([
                    'username'     => $userid,
                    'userid'       => $userid,
                    'group_id'     => Group::whereName('学生')->first()->id,
                    'password'     => bcrypt('student8888'),
                    'email'        => $data['user']['email'],
                    'realname'     => $data['user']['realname'],
                    'gender'       => $data['user']['gender'],
                    'english_name' => $data['user']['english_name'],
                    'telephone'    => $data['user']['telephone'],
                    'enabled'      => $data['user']['enabled'],
                    'avatar_url'   => '',
                    'isleader'     => 0,
                ]);

                # 创建学籍
                $student = $this->create([
                    'user_id'        => $user->id,
                    'class_id'       => $data['class_id'],
                    'student_number' => $data['student_number'],
                    'card_number'    => $data['card_number'],
                    'oncampus'       => $data['oncampus'],
                    'birthday'       => $data['birthday'],
                    'remark'         => $data['remark'],
                    'enabled'        => $user->enabled,
                ]);

                # 保存手机号码
                $mobile = new Mobile();
                $mobile->store($data['mobile'], $user);
                unset($mobile);

                # 保存用户所处部门
                $du = new DepartmentUser();
                $du->store([
                    'department_id' => $student->squad->department_id,
                    'user_id' => $student->user_id,
                    'enabled' => Constant::ENABLED
                ]);
                unset($du);

                # 创建企业号成员
                $user->createWechatUser($user->id);
            });
        } catch (Exception $e) {
            throw $e;
        }
        
        return true;
        
    }
    
    /**
     * 更新指定的学生记录
     *
     * @param array $data
     * @param $id
     * @return bool|mixed
     * @throws Exception
     * @throws Throwable
     */
    function modify(array $data, $id) {
        
        $student = $this->find($id);
        abort_if(!$student, HttpStatusCode::NOT_FOUND, '找不到该学籍');
        try {
            DB::transaction(function () use ($data, $id, $student) {
                $user = User::find($data['user_id']);
                
                # 更新用户
                $user->update([
                    'group_id'     => Group::whereName('学生')->first()->id,
                    'email'        => $data['user']['email'],
                    'realname'     => $data['user']['realname'],
                    'gender'       => $data['user']['gender'],
                    'isleader'     => 0,
                    'english_name' => $data['user']['english_name'],
                    'telephone'    => $data['user']['telephone'],
                    'enabled'      => $data['user']['enabled'],
                ]);
                
                # 更新学籍
                $student->update([
                    'user_id'        => $data['user_id'],
                    'class_id'       => $data['class_id'],
                    'student_number' => $data['student_number'],
                    'card_number'    => $data['card_number'],
                    'oncampus'       => $data['oncampus'],
                    'birthday'       => $data['birthday'],
                    'remark'         => $data['remark'],
                    'enabled'        => $data['user']['enabled'],
                ]);
                
                # 更新手机号码
                Mobile::whereUserId($user->id)->delete();
                $mobile = new Mobile();
                $mobile->store($data['mobile'], $student->user);
                unset($mobile);
                
                # 更新用户所在部门
                DepartmentUser::whereUserId($user->id)->delete();
                $du = new DepartmentUser();
                $du->store([
                    'department_id' => $student->squad->department_id,
                    'user_id' => $student->user_id,
                    'enabled' => Constant::ENABLED
                ]);
                unset($du);
                
                # 更新企业号成员
                $user->UpdateWechatUser($user->id);
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
     * @throws \Throwable
     */
    function remove($studentId) {
        
        $student = $this->find($studentId);
        #if (!isset($custodian)) { return false; }
        try {
            DB::transaction(function () use ($studentId, $student) {
                $userId = $student->user_id;
                #删除关联监护人
                $custodians = $student->custodians;
                foreach ($custodians as $custodian) {
                    #判断当前监护人下是否只有当前学生，是则删除监护人
                    $cusStuents = $custodian->students;
                    if (count($cusStuents) == 1) {
                        $custodian->remove($custodian->id);
                    }
                }
                # 删除与指定学生绑定的监护人记录
                CustodianStudent::whereStudentId($studentId)->delete();
                # 删除指定的学生记录
                $student->delete();
                # 删除user数据
                $this->user->remove($userId);
            });
        } catch (Exception $e) {
            throw $e;
        }
        
        return true;
        
    }
    
    /**
     * 导入
     *
     * @param UploadedFile $file
     * @return bool
     * @throws \PhpOffice\PhpSpreadsheet\Reader\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     */
    function upload(UploadedFile $file) {
        
        $ext = $file->getClientOriginalExtension();     // 扩展名//xls
        $realPath = $file->getRealPath();   // 临时文件的绝对路径
        // 上传文件
        $filename = date('His') . uniqid() . '.' . $ext;
        $stored = Storage::disk('uploads')->put(
            $filename, file_get_contents($realPath)
        );
        if ($stored) {
            $spreadsheet = IOFactory::load(
                $this->uploadedFilePath($filename)
            );
            $students = $spreadsheet->getActiveSheet()->toArray(
                null, true, true, true
            );
            abort_if(
                !empty(array_diff(self::EXCEL_FILE_TITLE, $students[0])),
                HttpStatusCode::NOT_ACCEPTABLE,
                '文件格式错误'
            );
            unset($students[0]);
            $students = array_values($students);
            if (!empty($students)) {
                # 去除表格的空数据
                foreach ($students as $key => $v) {
                    if ((array_filter($v)) == null) {
                        unset($students[$key]);
                    }
                }
                $this->validateData($students);
            }
            $data['user'] = Auth::user();
            $data['type'] = 'student';
            event(new ContactImportTrigger($data));

            return true;
        }
        
        return false;
        
    }
    
    /**
     * 导出学籍
     *
     * @param $range - 导出范围
     * @param null|integer $id - 班级/年级id
     * @return mixed
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    function export($range, $id = null) {

        abort_if(
            !in_array($range, [0, 1, 2]),
            
        )
        $students = null;
        switch ($range) {
            case self::EXPORT_RANGES['class']:
                $students = Squad::find($id)->students;
                break;
            case self::EXPORT_RANGES['grade']:
                $students = Grade::find($id)->students;
                break;
            case self::EXPORT_RANGES['all']:
                $students = $this->whereIn('id', $this->contactIds('student'))->get();
                break;
            default:
                break;
        }
        $records = [self::EXPORT_TITLES];
        foreach ($students as $student) {
            if (!$student->user) { continue; }
            $records[] = [
                $student->user->realname,
                $student->user->gender ? '男' : '女',
                $student->squad->name,
                $student->student_number,
                $student->card_number . "\t",
                $student->oncampus ? '是' : '否',
                implode(', ', $student->user->mobiles->pluck('mobile')->toArray()),
                $student->birthday,
                $student->created_at->toDateTimeString(),
                $student->updated_at->toDateTimeString(),
                $student->enabled ? '启用' : '禁用',
            ];
        }
        
        return $this->excel($records);
        
    }
    
    /**
     * 返回年级和班级列表
     *
     * @param int $gradeId
     * @return array
     */
    function gcList($gradeId = 0): array {
        
        $grades = Grade::whereEnabled(1)
            ->where('school_id', $this->schoolId())
            ->pluck('name', 'id')
            ->toArray();
        if (empty($grades)) {
            $classes = [];
        } else {
            $gradeId = $gradeId == 0 ? array_keys($grades)[0] : $gradeId;
            $classes = Squad::whereEnabled(1)
                ->where('grade_id', $gradeId)
                ->pluck('name', 'id')
                ->toArray();
        }
        
        return [$grades, $classes];
        
    }
    
    /**
     * 学生列表
     *
     * @return array
     */
    function datatable() {
        
        $columns = [
            ['db' => 'Student.id', 'dt' => 0],
            ['db' => 'User.realname as realname', 'dt' => 1],
            [
                'db'        => 'User.gender as gender', 'dt' => 2,
                'formatter' => function ($d) {
                    return $d == 1 ? Snippet::MALE : Snippet::FEMALE;
                },
            ],
            [
                'db'        => 'Squad.name as classname', 'dt' => 3,
                'formatter' => function ($d) {
                    return sprintf(Snippet::ICON, 'fa-users', '') . $d;
                },
            ],
            ['db' => 'Student.student_number', 'dt' => 4],
            ['db' => 'Student.card_number', 'dt' => 5],
            [
                'db'        => 'Student.oncampus', 'dt' => 6,
                'formatter' => function ($d) {
                    return $d == 1 ? '是' : '否';
                },
            ],
            [
                'db'        => 'Student.id as mobile', 'dt' => 7,
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
                'db'        => 'Student.birthday', 'dt' => 8,
                'formatter' => function ($d) {
                    return $d ? substr($d, 0, 10) : '';
                },
            ],
            ['db' => 'Student.created_at', 'dt' => 9],
            ['db' => 'Student.updated_at', 'dt' => 10],
            [
                'db'        => 'Student.enabled', 'dt' => 11,
                'formatter' => function ($d, $row) {
                    return Datatable::dtOps($d, $row);
                },
            ],
        ];
        $joins = [
            [
                'table'      => 'users',
                'alias'      => 'User',
                'type'       => 'INNER',
                'conditions' => [
                    'User.id = Student.user_id',
                ],
            ],
            [
                'table'      => 'classes',
                'alias'      => 'Squad',
                'type'       => 'INNER',
                'conditions' => [
                    'Squad.id = Student.class_id',
                ],
            ],
        ];
        $condition = 'Student.id In (' . implode(',', $this->contactIds('student')) . ')';
        
        return Datatable::simple(
            $this->getModel(), $columns, $joins, $condition
        );
        
    }
    
    /**
     * 检查每行数据 是否符合导入数据
     *
     * @param array $data
     */
    private function validateData(array $data) {
        
        $rules = [
            'name'           => 'required|string|between:2,6',
            'gender'         => ['required', Rule::in(['男', '女'])],
            'birthday'       => 'required|date',
            'school'         => 'required|string|between:4,20',
            'grade'          => 'required|string|between:3,20',
            'class'          => 'required|string|between:2,20',
            'mobile'         => 'required', new Mobiles(),
            'student_number' => 'required|alphanum|between:2,32',
            'card_number'    => 'required|alphanum|between:2,32',
            'oncampus'       => ['required', Rule::in(['住读', '走读'])],
            'remark'         => 'string|nullable',
            'relationship'   => 'string',
        ];
        # 不合法的数据
        $invalidRows = [];
        # 更新的数据
        $updateRows = [];
        # 需要添加的数据
        $rows = [];
        for ($i = 0; $i < count($data); $i++) {
            $user = [
                'name'           => $data[$i][0],
                'gender'         => $data[$i][1],
                'birthday'       => $data[$i][2],
                'school'         => $data[$i][3],
                'grade'          => $data[$i][4],
                'class'          => $data[$i][5],
                'mobile'         => $data[$i][6],
                'student_number' => $data[$i][7],
                'card_number'    => $data[$i][8],
                'oncampus'       => $data[$i][9],
                'remark'         => $data[$i][10],
                'relationship'   => $data[$i][11],
                'class_id'       => 0,
                'department_id'  => 0,
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
            $grade = Grade::whereName($user['grade'])
                ->where('school_id', $school->id)
                ->first();
            # 数据非法
            if (!$grade) {
                $invalidRows[] = $data[$i];
                unset($data[$i]);
                continue;
            }
            $class = Squad::whereName($user['class'])
                ->where('grade_id', $grade->id)
                ->first();
            if (!$class) {
                $invalidRows[] = $data[$i];
                unset($data[$i]);
                continue;
            }
            $student = Student::whereStudentNumber($user['student_number'])
                ->where('class_id', $class->id)
                ->first();
            $user['class_id'] = $class->id;
            $user['department_id'] = $class->department_id;
            # 学生数据已存在 更新操作
            if ($student) {
                $updateRows[] = $user;
            } else {
                $rows[] = $user;
            }
            unset($user);
        }
        event(new StudentUpdated($updateRows));
        event(new StudentImported($rows));
        
    }
    
}
