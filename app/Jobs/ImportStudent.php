<?php
namespace App\Jobs;

use App\Apis\MassImport;
use App\Helpers\{Broadcaster, Constant, HttpStatusCode, JobTrait, ModelTrait};
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
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;
use Validator;

/**
 * Class ImportStudent
 * @package App\Jobs
 */
class ImportStudent implements ShouldQueue, MassImport {
    
    use Dispatchable, InteractsWithQueue, Queueable,
        SerializesModels, ModelTrait, JobTrait;
    
    public $data, $userId, $response, $broadcaster, $members, $corpId;
    
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
        $this->response = array_combine(Constant::BROADCAST_FIELDS, [
            $this->userId, __('messages.student.title'),
            HttpStatusCode::OK, __('messages.student.import_completed'),
        ]);
        $this->broadcaster = new Broadcaster();
        
    }
    
    /**
     * @throws Exception
     * @throws Throwable
     */
    function handle() {
    
        $imported = $this->import($this, $this->response);
        !$imported ?: (new User)->sync(
            $this->members, $this->userId
        );
        
        return true;
        
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
        
        $fields = [
            'name', 'gender', 'school', 'birthday', 'grade', 'class',
            'student_number', 'card_number', 'oncampus', 'remark',
            'relationship',
        ];
        $rules = array_combine($fields, [
            'required|string|between:2,60',
            ['required', Rule::in(['男', '女'])],
            'required|string|between:4,20',
            'required|date',
            'required|string|between:3,20',
            'required|string|between:2,20',
            'required|alphanum|between:2,32',
            'required|alphanum|between:2,32',
            ['required', Rule::in(['住读', '走读'])],
            'nullable',
            'string',
        ]);
        $fields = array_merge($fields, ['class_id', 'department_id']);
        for ($i = 0; $i < count($data); $i++) {
            $datum = $data[$i];
            $schoolName = $datum['C'];
            $gradeName = $datum['E'];
            $className = $datum['F'];
            $studentNumber = $datum['G'];
            $user = array_combine($fields, [
                trim($datum['A']), trim($datum['B']), $schoolName,
                trim($datum['D']), $gradeName, $className,
                $studentNumber, trim($datum['H']),
                trim(strval($datum['I'])), trim($datum['J']),
                trim($datum['K']), 0, 0,
            ]);
            $result = Validator::make($user, $rules);
            $failed = $result->fails();
            $school = !$failed ? School::whereName($schoolName)->first() : null;
            if ($school && !$this->corpId) $this->corpId = $school->corp_id;
            $isSchoolValid = $school ? in_array($school->id, $this->schoolIds($this->userId)) : false;
            $grade = $school ? Grade::whereName($gradeName)->where('school_id', $school->id)->first() : null;
            $isGradeValid = $grade ? in_array($grade->id, $this->gradeIds($school->id, $this->userId)) : false;
            $class = $grade ? Squad::whereName($className)->where('grade_id', $grade->id)->first() : null;
            $isClassValid = $class ? in_array($class->id, $this->classIds($school->id, $this->userId)) : false;
            if (!(!$failed && $isSchoolValid && $isGradeValid && $isClassValid)) {
                $datum['L'] = $failed
                    ? json_encode($result->errors(), JSON_UNESCAPED_UNICODE)
                    : __('messages.student.import_validation_error');
                $illegals[] = $datum;
                continue;
            }
            $student = Student::where([
                'student_number' => $studentNumber,
                'class_id'       => $class->id,
            ])->first();
            $user['class_id'] = $class->id;
            $user['department_id'] = $class->department_id;
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
                $groupId = Group::whereName('学生')->first()->id;
                foreach ($inserts as $insert) {
                    $userid = uniqid('ptac_');
                    # 创建用户
                    $user = User::create(
                        array_combine(Constant::USER_FIELDS, [
                            $userid, $groupId, $password, $insert['name'],
                            $insert['gender'] == '男' ? 1 : 0, $userid, '学生', 1,
                        ])
                    );
                    # 创建学生
                    $student = Student::create(
                        array_combine(Constant::STUDENT_FIELDS, [
                            $user->id, $insert['class_id'], $insert['student_number'],
                            $insert['card_number'], $insert['oncampus'] == '住读' ? 1 : 0,
                            $insert['birthday'], $insert['remark'] ?? '导入', $user->enabled,
                        ])
                    );
                    # 保存监护关系
                    $this->binding($student, $insert, $password);
                    # 保存部门 & 用户绑定关系
                    DepartmentUser::create(
                        array_combine(Constant::DU_FIELDS, [
                            $insert['department_id'], $user->id, $user->enabled,
                        ])
                    );
                    $this->members[] = [$user->id, '学生', 'create'];
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
                    $ex = new NotFoundHttpException(__('messages.not_found'));
                    $student = Student::whereStudentNumber($update['student_number'])->first();
                    throw_if(!$student, $ex);
                    $student->update(
                        array_combine(Constant::STUDENT_FIELDS, [
                            $student->user_id,
                            $update['class_id'],
                            $update['card_number'],
                            $update['oncampus'] == '住读' ? 1 : 0,
                            $update['birthday'],
                            '导入', $student->enabled,
                        ])
                    );
                    throw_if(!$student->user, $ex);
                    $student->user->update([
                        'realname' => $update['name'],
                        'gender'   => $update['gender'] == '男' ? 1 : 0,
                    ]);
                    # 保存监护关系
                    $this->binding($student, $update);
                    # 更新部门 & 用户绑定关系
                    DepartmentUser::updateOrCreate(
                        ['user_id' => $student->user_id, 'enabled' => 1],
                        ['department_id' => $update['department_id']]
                    );
                    $this->members[] = [$student->user_id, '学生', 'update'];
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
        $groupId = Group::whereName('监护人')->first()->id;
        foreach ($relationships as $r) {
            if (count($paths = explode(':', $r)) != 4) continue;
            if (!($mobile = Mobile::whereMobile($paths[3])->first())) {
                $userid = uniqid('ptac_');
                $user = User::create(
                    array_combine(Constant::USER_FIELDS, [
                        $userid, $groupId, $password, $paths[1],
                        $paths[2] == '男' ? 1 : 0, $userid, '监护人', 1,
                    ])
                );
                Mobile::create(
                    array_combine(Constant::MOBILE_FIELDS, [
                        $user->id, $paths[3], 1, $user->enabled,
                    ])
                );
            } else {
                $user = User::find($mobile->user_id);
                !$user ?: $user->update([
                    'realname' => $paths[1],
                    'gender'   => $paths[2] == '男' ? 1 : 0,
                ]);
            }
            # 更新/创建监护人
            $custodian = Custodian::updateOrCreate(
                ['user_id' => $user->id], ['enabled' => $user->enabled]
            );
            # 更新/创建监护人 & 学生绑定关系
            CustodianStudent::updateOrCreate(
                ['custodian_id' => $custodian->id, 'student_id' => $student->id],
                ['relationship' => $paths[0], 'enabled' => $user->enabled]
            );
            # 更新/创建部门 & 用户绑定关系
            DepartmentUser::updateOrCreate(
                ['user_id' => $user->id, 'enabled' => 0],
                ['department_id' => $record['department_id']]
            );
            # 需要同步至企业微信的监护人
            $this->members[] = [$user->id, '监护人', !$mobile ? 'create' : 'update'];
        }
        
    }
    
}