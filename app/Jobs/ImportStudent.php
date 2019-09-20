<?php
namespace App\Jobs;

use App\Apis\MassImport;
use App\Helpers\{Broadcaster, Constant, JobTrait, ModelTrait};
use App\Models\{Custodian, CustodianStudent, DepartmentUser, Grade, Group, School, Squad, Student, User};
use Exception;
use Illuminate\{Bus\Queueable,
    Contracts\Queue\ShouldQueue,
    Foundation\Bus\Dispatchable,
    Queue\InteractsWithQueue,
    Queue\SerializesModels,
    Support\Facades\DB,
    Validation\Rule};
use Pusher\PusherException;
use Throwable;
use Validator;

/**
 * Class ImportStudent
 * @package App\Jobs
 */
class ImportStudent implements ShouldQueue, MassImport {
    
    use Dispatchable, InteractsWithQueue, Queueable,
        SerializesModels, ModelTrait, JobTrait;
    
    protected $data, $schoolId, $userId, $corpId;
    protected $members, $response, $broadcaster;

    /**
     * Create a new job instance.
     *
     * @param array $data
     * @param $schoolId
     * @param integer $userId
     * @throws PusherException
     */
    function __construct(array $data, $schoolId, $userId) {
        
        $this->data = $data;
        $this->schoolId = $schoolId;
        $this->userId = $userId;
        $this->response = array_combine(Constant::BROADCAST_FIELDS, [
            $this->userId, __('messages.student.title'),
            Constant::OK, __('messages.student.import_completed'),
        ]);
        $this->broadcaster = new Broadcaster();
        
    }
    
    /**
     * @throws Exception
     * @throws Throwable
     */
    function handle() {
        
        try {
            DB::transaction(function () {
                $this->import($this);
                # 同步企业微信通讯录
                (new User)->sync($this->members, $this->userId);
            });
        } catch (Exception $e) {
            $this->eHandler($this, $e);
            throw $e;
        }
        $this->broadcaster->broadcast($this->response);
        
        return true;
        
    }
    
    /**
     * 任务异常处理
     *
     * @param Exception $e
     * @throws Exception
     */
    function failed(Exception $e) {
        
        $this->eHandler($this, $e);
        
    }
    
    /**
     * 验证导入数据
     *
     * @param array $data
     * @return array
     */
    function validate(array $data): array {
        
        $fields = [
            'name', 'gender', 'birthday', 'grade', 'class',
            'sn', 'oncampus', 'remark', 'relationship',
        ];
        $rules = array_combine($fields, [
            'required|string|between:2,60',
            ['required', Rule::in(['男', '女'])],
            'required|date',
            'required|string|between:3,20',
            'required|string|between:2,20',
            'required|alphanum|between:2,32',
            ['required', Rule::in(['住读', '走读'])],
            'nullable',
            'string',
        ]);
        $fields = array_merge($fields, ['class_id', 'department_id']);
        $this->corpId = School::find($this->schoolId)->corp_id;
        $isSchoolValid = in_array($this->schoolId, $this->schoolIds($this->userId));
        for ($i = 0; $i < count($data); $i++) {
            $datum = $data[$i];
            $gradeName = $datum['D'];
            $className = $datum['E'];
            $sn = $datum['F'];
            $user = array_combine($fields, [
                trim($datum['A']), trim($datum['B']),
                trim($datum['C']), $gradeName, $className,
                $sn, trim(strval($datum['G'])),
                trim($datum['H']), trim($datum['I']), 0, 0,
            ]);
            $result = Validator::make($user, $rules);
            $failed = $result->fails();
            $grade = Grade::where(['name' => $gradeName, 'school_id' => $this->schoolId])->get()->first();
            $isGradeValid = $grade ? in_array($grade->id, $this->gradeIds($this->schoolId, $this->userId)) : false;
            $class = $grade ? Squad::where(['name' => $className, 'grade_id' => $grade->id])->get()->first() : null;
            $isClassValid = $class ? in_array($class->id, $this->classIds($this->schoolId, $this->userId)) : false;
            if (!(!$failed && $isSchoolValid && $isGradeValid && $isClassValid)) {
                $datum['J'] = $failed
                    ? json_encode($result->errors(), JSON_UNESCAPED_UNICODE)
                    : __('messages.student.import_validation_error');
                $illegals[] = $datum;
                continue;
            }
            $student = Student::where(['sn' => $sn, 'class_id' => $class->id])->first();
            $user['class_id'] = $class->id;
            $user['department_id'] = $class->department_id;
            $student ? $updates[] = $user : $inserts[] = $user;
        }
        
        return [$inserts ?? [], $updates ?? [], $illegals ?? []];
        
    }
    
    /**
     * 插入需导入的学籍数据
     *
     * @param array $records
     * @return bool
     * @throws Throwable
     */
    function insert(array $records) {
        
        try {
            DB::transaction(function () use ($records) {
                $groupId = Group::whereName('学生')->first()->id;
                foreach ($records as $record) {
                    $userid = uniqid('ptac_');
                    # 创建用户
                    $user = User::create(
                        array_combine(Constant::USER_FIELDS, [
                            $userid, $groupId, bcrypt('12345678'), $record['name'],
                            $record['gender'] == '男' ? 1 : 0, $userid, '学生', 1,
                        ])
                    );
                    # 创建学生
                    $student = Student::create(
                        array_combine(
                            (new Student)->getFillable(),
                            [
                                $user->id, $record['class_id'], $record['sn'],
                                $record['oncampus'] == '住读' ? 1 : 0, $record['birthday'],
                                $record['remark'] ?? '导入', $user->enabled,
                            ]
                        )
                    );
                    # 保存监护关系
                    $this->binding($student, $record, 'create');
                }
            });
        } catch (Exception $e) {
            throw $e;
        }
        
        return true;
        
    }
    
    /**
     * 更新已导入的学籍数据
     *
     * @param array $records
     * @return bool
     * @throws Throwable
     */
    function update(array $records) {
        
        try {
            DB::transaction(function () use ($records) {
                foreach ($records as $record) {
                    $student = Student::whereSn($record['sn'])->first();
                    throw_if(
                        !$student || !$student->user,
                        new Exception(__('messages.not_found'))
                    );
                    # 更新学生
                    $student->update(
                        array_combine(
                            (new Student)->getFillable(),
                            [
                                $student->user_id, $record['class_id'],
                                $record['sn'], $record['oncampus'] == '住读' ? 1 : 0,
                                $record['birthday'], '导入', $student->enabled,
                            ]
                        )
                    );
                    # 更新用户
                    $student->user->update([
                        'realname' => $record['name'],
                        'gender'   => $record['gender'] == '男' ? 1 : 0,
                    ]);
                    # 保存监护关系
                    $this->binding($student, $record, 'update');
                    
                }
            });
        } catch (Exception $e) {
            throw $e;
        }
        
        return true;
        
    }
    
    /**
     * 创建/更新监护人 & 学生绑定关系
     *
     * @param Student $student
     * @param $record
     * @param $type
     * @throws Throwable
     */
    private function binding(Student $student, $record, $type) {
        
        try {
            DB::transaction(function () use ($student, $record, $type) {
                $password = bcrypt('12345678');
                $relationships = explode(',', str_replace(
                    ['，', '：'], [',', ':'], $record['relationship'])
                );
                $groupId = Group::whereName('监护人')->first()->id;
                $deptId = $record['department_id'];
                foreach ($relationships as $r) {
                    if (count($paths = explode(':', $r)) != 4) continue;
                    [$relation, $realname, $gender, $mobile] = $paths;
                    $gender = ($gender == '男' ? 1 : 0);
                    # 用户
                    if (!$user = User::whereMobile($paths[3])->first()) {
                        $user = User::create(
                            array_combine(Constant::USER_FIELDS, [
                                $userid = uniqid('ptac_'), $groupId, $password,
                                $realname, $gender, $mobile, Constant::ENABLED,
                                json_encode(
                                    ['userid' => $userid, 'position' => '监护人'],
                                    JSON_UNESCAPED_UNICODE
                                )
                            ])
                        );
                    } else {
                        $data = ['realname' => $realname, 'gender' => $gender];
                        !$user->educator ?: $data = array_merge(
                            $data, ['ent_attrs->position' => $user->group->name . '/' . '监护人']
                        );
                        $user->update($data);
                    }
                    # 监护人
                    $custodian = Custodian::updateOrCreate(
                        ['user_id' => $user->id], ['enabled' => $user->enabled]
                    );
                    # 监护人学生绑定关系
                    CustodianStudent::updateOrCreate(
                        ['custodian_id' => $custodian->id, 'student_id' => $student->id],
                        ['relationship' => $relation, 'enabled' => $user->enabled]
                    );
                    # 部门用户绑定关系
                    DepartmentUser::updateOrCreate(
                        ['user_id' => $user->id, 'enabled' => 0],
                        ['department_id' => $deptId]
                    );
                    # 同步至企业微信通讯录
                    $this->members[] = [$user->id, '监护人', !$user ? 'create' : 'update'];
                }
                # 部门用户绑定关系(学生)
                DepartmentUser::updateOrCreate(
                    ['user_id' => $student->user_id, 'enabled' => 1],
                    ['department_id' => $deptId]
                );
                // $this->members[] = [$student->user_id, '学生', $type];
            });
        } catch (Exception $e) {
            throw $e;
        }
        
    }
    
}