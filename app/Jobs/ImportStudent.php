<?php
namespace App\Jobs;

use App\Helpers\{Broadcaster, HttpStatusCode, JobTrait, ModelTrait};
use App\Models\{Custodian, CustodianStudent, DepartmentUser, Grade, Group, Mobile, School, Squad, Student, User};
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
class ImportStudent implements ShouldQueue {
    
    use Dispatchable, InteractsWithQueue, Queueable,
        SerializesModels, ModelTrait, JobTrait;
    
    const EXCEL_FILE_TITLE = [
        '姓名', '性别', '学校', '生日',
        '年级', '班级', '手机号码',
        '学号', '卡号', '住校',
        '备注', '监护关系',
    ];
    
    public $data, $userId, $response, $broadcaster;
    
    /**
     * Create a new job instance.
     *
     * @param array $data
     * @param integer $userId
     * @throws PusherException
     */
    function __construct(array $data, $userId) {
        
        $this->data = $data;
        $this->userId = $userId;
        $this->response = [
            'userId'     => $this->userId,
            'title'      => __('messages.student.title'),
            'statusCode' => HttpStatusCode::OK,
            'message'    => __('messages.student.import_completed')
        ];
        $this->broadcaster = new Broadcaster();
        
    }
    
    /**
     * @throws Exception
     * @throws Throwable
     */
    function handle() {
        
        return $this->import($this, $this->response);
        
    }
    
    /**
     * 任务异常处理
     *
     * @param Exception $exception
     * @throws PusherException
     */
    function failed(Exception $exception) {
        
        $this->eHandler($exception, $this->response);
        
    }
    
    /**
     * 验证导入数据
     *
     * @param array $data
     * @return array
     */
    function validate(array $data): array {
        
        $rules = [
            'name'           => 'required|string|between:2,60',
            'gender'         => ['required', Rule::in(['男', '女'])],
            'birthday'       => 'required|date',
            'school'         => 'required|string|between:4,20',
            'grade'          => 'required|string|between:3,20',
            'class'          => 'required|string|between:2,20',
            'mobile'         => 'required|regex:/^1[3456789][0-9]{9}$/',
            'student_number' => 'required|alphanum|between:2,32',
            'card_number'    => 'required|alphanum|between:2,32',
            'oncampus'       => ['required', Rule::in(['住读', '走读'])],
            'remark'         => 'string|nullable',
            'relationship'   => 'string',
        ];
        for ($i = 0; $i < count($data); $i++) {
            $datum = $data[$i];
            $schoolName = $datum['C'];
            $gradeName = $datum['E'];
            $className = $datum['F'];
            $sn = $datum['H'];
            $user = [
                'name'           => trim($datum['A']),
                'gender'         => trim($datum['B']),
                'birthday'       => trim($datum['D']),
                'school'         => trim($schoolName),
                'grade'          => trim($gradeName),
                'class'          => trim($className),
                'mobile'         => trim($datum['G']),
                'student_number' => trim($sn),
                'card_number'    => trim($datum['I']),
                'oncampus'       => trim($datum['J']),
                'remark'         => $datum['K'],
                'relationship'   => trim($datum['L']),
                'class_id'       => 0,
                'department_id'  => 0,
            ];
            $result = Validator::make($user, $rules);
            $failed = $result->fails();
            $school = !$failed ? School::whereName($schoolName)->first() : null;
            $isSchoolValid = $school ? in_array($school->id, $this->schoolIds($this->userId)) : false;
            $grade = $school ? Grade::whereName($gradeName)->where('school_id', $school->id)->first() : null;
            $isGradeValid = $grade ? in_array($grade->id, $this->gradeIds($school->id, $this->userId)) : false;
            $class = $grade ? Squad::whereName($className)->where('grade_id', $grade->id)->first() : null;
            $isClassValid = $class ? in_array($class->id, $this->classIds($school->id, $this->userId)) : false;
            # 数据非法
            if (!(!$failed && $isSchoolValid && $isGradeValid && $isClassValid)) {
                $datum['M'] = $failed
                    ? json_encode($result->errors())
                    : __('messages.student.import_validation_error');
                $illegals[] = $datum;
                continue;
            }
            $student = Student::whereStudentNumber($sn)->where('class_id', $class->id)->first();
            $user['class_id'] = $class->id;
            $user['department_id'] = $class->department_id;
            # 学生数据已存在 更新操作
            $student ? $updates[] = $user : $inserts[] = $user;
        }
        
        return [$inserts ?? [], $updates ?? [], $illegals ?? []];
        
    }
    
    /**
     * 插入需导入的学籍数据
     *
     * @param array $inserts
     * @return bool
     * @throws Throwable
     */
    function insert(array $inserts) {
        
        try {
            DB::transaction(function () use ($inserts) {
                $password = bcrypt('12345678');
                foreach ($inserts as $insert) {
                    $userid = uniqid('ptac_');
                    # 创建用户
                    $user = User::create([
                        'username'   => $userid,
                        'group_id'   => Group::whereName('学生')->first()->id,
                        'password'   => $password,
                        'realname'   => $insert['name'],
                        'gender'     => $insert['gender'] == '男' ? 1 : 0,
                        'userid'     => $userid,
                        'enabled'    => 1,
                    ]);
                    # 创建学生
                    $student = Student::create([
                        'user_id'        => $user->id,
                        'class_id'       => $insert['class_id'],
                        'student_number' => $insert['student_number'],
                        'card_number'    => $insert['card_number'],
                        'oncampus'       => $insert['oncampus'] == '住读' ? 1 : 0,
                        'birthday'       => $insert['birthday'],
                        'remark'         => $insert['remark'] ?? '导入',
                        'enabled'        => $user->enabled,
                    ]);
                    $this->binding($student, $insert, $password);
                    # 保存学生用户手机号码
                    Mobile::create([
                        'user_id'   => $user->id,
                        'mobile'    => $insert['mobile'],
                        'isdefault' => 1,
                        'enabled'   => $user->enabled,
                    ]);
                    # 保存部门 & 用户绑定关系
                    DepartmentUser::create([
                        'department_id' => $insert['department_id'],
                        'user_id'       => $user->id,
                        'enabled'       => $user->enabled,
                    ]);
                    # 创建企业号成员
                    $user->sync($user->id, 'create', false);
                }
            });
        } catch (Exception $e) {
            $this->eHandler($e, $this->response);
            throw $e;
        }
        
        return true;
        
    }
    
    /**
     * 更新已导入的学籍数据
     *
     * @param array $updates
     * @return bool
     * @throws Throwable
     */
    function update(array $updates) {
        
        try {
            DB::transaction(function () use ($updates) {
                foreach ($updates as $update) {
                    $student = Student::whereStudentNumber($update['student_number'])->first();
                    $student->class_id = $update['class_id'];
                    $student->card_number = $update['card_number'];
                    $student->oncampus = $update['oncampus'] == '住读' ? 1 : 0;
                    $student->birthday = $update['birthday'];
                    $student->remark = '导入';
                    $student->save();
                    User::find($student->user_id)->update([
                        'realname' => $update['name'],
                        'gender'   => $update['gender'] == '男' ? 1 : 0,
                    ]);
                    Mobile::whereUserId($student->user_id)->update(['isdefault' => 0, 'enabled' => 0]);
                    Mobile::create([
                        'user_id'   => $student->user_id,
                        'mobile'    => $update['mobile'],
                        'isdefault' => 1,
                        'enabled'   => 1,
                    ]);
                    # 创建监护人 & 学生绑定关系
                    $this->binding($student, $update);
                    # 更新部门 & 用户绑定关系
                    DepartmentUser::whereUserId($student->user_id)->delete();
                    DepartmentUser::create([
                        'department_id' => $update['department_id'],
                        'user_id'       => $student->user_id,
                        'enabled'       => 1,
                    ]);
                    # 更新企业微信会员
                    $student->user->sync($student->user_id, 'update');
                }
            });
        } catch (Exception $e) {
            $this->eHandler($e, $this->response);
            throw $e;
        }
        
        return true;
        
    }
    
    /**
     * 创建/更新监护人 & 学生绑定关系
     *
     * @param Student $student
     * @param $record
     * @param $password
     * @throws Throwable
     */
    private function binding(Student $student, $record, $password = null) {
        
        $password = $password ?? bcrypt('12345678');
        $relationship = str_replace(['，', '：'], [',', ':'], $record['relationship']);
        $relationships = explode(',', $relationship);
        foreach ($relationships as $r) {
            $paths = explode(':', $r);
            if (count($paths) != 4) continue;
            $mobile = Mobile::whereMobile($paths[3])->first();
            // if (!$m) continue;
            # 手机号码不存在时 增加监护人用户 如果存在则更新
            if (!$mobile) {
                # 创建监护人用户
                $userid = uniqid('ptac_');
                $user = User::create([
                    'username'   => $userid,
                    'group_id'   => Group::whereName('监护人')->first()->id,
                    'password'   => $password,
                    'realname'   => $paths[1],
                    'gender'     => $paths[2] == '男' ? 1 : 0,
                    'userid'     => $userid,
                    'enabled'    => 1,
                ]);
                # 创建监护人
                $custodian = Custodian::create(['user_id' => $user->id, 'enabled' => $user->enabled]);
                # 保存监护关系
                CustodianStudent::create([
                    'custodian_id' => $custodian->id,
                    'student_id'   => $student->id,
                    'relationship' => $paths[0],
                    'enabled'      => $user->enabled,
                ]);
                # 保存监护人用户手机号码
                Mobile::create([
                    'user_id'   => $user->id,
                    'mobile'    => $paths[3],
                    'isdefault' => 1,
                    'enabled'   => $user->enabled,
                ]);
                # 保存部门 & 用户绑定关系
                DepartmentUser::create([
                    'department_id' => $record['department_id'],
                    'user_id'       => $user->id,
                    'enabled'       => 0,
                ]);
            } else {
                # 手机号码存在时 更新user 再判断监护人是否存在 监护关系是否存在
                $user = User::find($mobile->user_id);
                $user->realname = $paths[1];
                $user->gender = $paths[2] == '男' ? 1 : 0;
                $user->save();
                $custodian = $user->custodian;
                # 监护人不存在时
                if (!$custodian) {
                    # 创建监护人
                    $custodian = Custodian::create([
                        'user_id' => $user->id,
                        'enabled' => $user->enabled
                    ]);
                    # 保存监护关系
                    CustodianStudent::create([
                        'custodian_id' => $custodian->id,
                        'student_id'   => $student->id,
                        'relationship' => $paths[0],
                        'enabled'      => $user->enabled,
                    ]);
                } else {
                    # 监护人存在 监护关系不存在时
                    $cs = CustodianStudent::where([
                        'custodian_id' => $custodian->id,
                        'student_id' => $student->id
                    ])->first();
                    # 创建 监护关系
                    $cs ?: CustodianStudent::create([
                        'custodian_id' => $custodian->id,
                        'student_id'   => $student->id,
                        'relationship' => $paths[0],
                        'enabled'      => $user->enabled,
                    ]);
                }
                # 更新部门 & 用户绑定关系
                DepartmentUser::where(['user_id' => $user->id, 'enabled' => 0])->delete();
                DepartmentUser::create([
                    'department_id' => $record['department_id'],
                    'user_id'       => $user->id,
                    'enabled'       => 0,
                ]);
            }
            # 同步企业微信会员
            $user->sync($user->id, !$mobile ? 'create' : 'update', false);
        }
        
    }
    
}