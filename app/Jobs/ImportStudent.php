<?php
namespace App\Jobs;

use Exception;
use Illuminate\Support\Facades\Log;
use Validator;
use App\Models\User;
use App\Models\Grade;
use App\Models\Squad;
use App\Models\Group;
use App\Models\Mobile;
use App\Models\School;
use App\Rules\Mobiles;
use App\Models\Student;
use App\Models\Custodian;
use App\Helpers\ModelTrait;
use App\Events\JobResponse;
use Illuminate\Bus\Queueable;
use App\Models\DepartmentUser;
use App\Helpers\HttpStatusCode;
use Illuminate\Validation\Rule;
use App\Models\CustodianStudent;
use Illuminate\Support\Facades\DB;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ImportStudent implements ShouldQueue {
    
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, ModelTrait;
    
    protected $students, $userId;
    
    const EXCEL_FILE_TITLE = [
        '姓名', '性别', '学校', '生日',
        '年级', '班级', '手机号码',
        '学号', '卡号', '住校',
        '备注', '监护关系',
    ];
    
    /**
     * Create a new job instance.
     *
     * @param array $students - 学籍Excel文件
     * @param integer $userId
     */
    public function __construct(array $students, $userId) {
        
        $this->students = $students;
        $this->userId = $userId;
        
    }
    
    /**
     * @throws Exception
     * @throws \Throwable
     */
    public function handle() {

        $response = $response = [
            'userId' => $this->userId,
            'title' => '批量导入学籍',
            'statusCode' => HttpStatusCode::OK,
            'message' => __('messages.ok')
        ];
        list($updates, $inserts, $illegals) = $this->validate($this->students);
        if (empty($updates) && empty($inserts)) {
            # 数据格式不正确，中止任务
            $response['statusCode'] = HttpStatusCode::NOT_ACCEPTABLE;
            $response['message'] = __('messages.student.invalid_data_format');
            event(new JobResponse($response));
            return false;
        }
        # 验证导入数据
        $response['statusCode'] = HttpStatusCode::ACCEPTED;
        $response['message'] = sprintf(
            __('messages.student.import_request_submitted'),
            sizeof($inserts), sizeof($updates), sizeof($illegals)
        );
        # 新增数据
        $this->insert($inserts);
        # 更新数据
        $this->update($updates);
        // $response['url'] = ''; # todo: 生成非法数据excel文件及下载地址
        event(new JobResponse($response));
        
        return true;
        
    }
    
    /**
     * 检查每行数据 是否符合导入数据
     *
     * @param array $students
     * @return array
     */
    private function validate(array $students): array {
        
        unset($students[0]);
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
        $illegals = [];
        # 更新的数据
        $updates = [];
        # 需要添加的数据
        $inserts = [];
        for ($i = 1; $i <= count($students); $i++) {
            $schoolName = $students[$i]['C'];
            $gradeName = $students[$i]['E'];
            $className = $students[$i]['F'];
            $sn = $students[$i]['H'];
            $user = [
                'name'           => $students[$i]['A'],
                'gender'         => $students[$i]['B'],
                'birthday'       => $students[$i]['D'],
                'school'         => $schoolName,
                'grade'          => $gradeName,
                'class'          => $className,
                'mobile'         => $students[$i]['G'],
                'student_number' => $sn,
                'card_number'    => $students[$i]['I'],
                'oncampus'       => $students[$i]['J'],
                'remark'         => $students[$i]['K'],
                'relationship'   => $students[$i]['L'],
                'class_id'       => 0,
                'department_id'  => 0,
            ];
            $validator = Validator::make($user, $rules);
            $failed = $validator->fails();
            Log::debug($failed ? 'failed' : 'succeeded');
            $school = !$failed ? School::whereName($schoolName)->first() : null;
            if ($school) {
                Log::debug(json_encode($school));
            }
            $isSchoolValid = $school ? in_array($school->id, $this->schoolIds($this->userId)) : false;
            $grade = $school ? Grade::whereName($gradeName)->where('school_id', $school->id)->first() : null;
            $isGradeValid = $grade ? in_array($grade->id, $this->gradeIds($school->id, $this->userId)) : false;
            $class = $grade ? Squad::whereName($className)->where('grade_id', $grade->id)->first() : null;
            $isClassValid = $class ? in_array($class->id, $this->classIds($school->id, $this->userId)) : false;
            # 数据非法
            if (!(!$failed && $isSchoolValid && $isGradeValid && $isClassValid)) {
                $illegals[] = $students[$i];
                continue;
            }
            $student = Student::whereStudentNumber($sn)->where('class_id', $class->id)->first();
            $user['class_id'] = $class->id;
            $user['department_id'] = $class->department_id;
            # 学生数据已存在 更新操作
            if ($student) {
                $updates[] = $user;
            } else {
                $inserts[] = $user;
            }
        }
        
        return [$updates, $inserts, $illegals];
        
    }
    
    /**
     * 插入导入的学籍数据
     *
     * @param array $inserts
     * @throws Exception
     */
    private function insert(array $inserts) {
    
        try {
            DB::transaction(function () use ($inserts) {
                foreach ($inserts as $insert) {
                    $relationship = str_replace(['，', '：'], [',', ':'], $insert['relationship']);
                    $relationships = explode(',', $relationship);
                    # 创建用户
                    $u = User::create([
                        'username' => uniqid('student_'),
                        'group_id'   => Group::whereName('学生')->first()->id,
                        'password'   => bcrypt('student8888'),
                        'realname'   => $insert['name'],
                        'gender'     => $insert['gender'] == '男' ? '0' : '1',
                        'avatar_url' => '00001.jpg',
                        'userid'     => uniqid('student_'),
                        'isleader'   => 0,
                        'enabled'    => 1,
                    ]);
                    # 创建学生
                    $s = Student::create([
                        'user_id'        => $u['id'],
                        'class_id'       => $insert['class_id'],
                        'student_number' => $insert['student_number'],
                        'card_number'    => $insert['card_number'],
                        'oncampus'       => $insert['oncampus'] == '住读' ? '0' : '1',
                        'birthday'       => $insert['birthday'],
                        'remark'         => $insert['remark'],
                        'enabled'        => 1,
                    ]);
                    # 创建监护人关系
                    if (!empty($relationships)) {
                        foreach ($relationships as $r) {
                            $paths = explode(':', $r);
                            if (count($paths) == 4) {
                                $m = Mobile::whereMobile($paths[3])->first();
                                # 手机号码不存在时 增加监护人用户 如果存在则更新
                                if (empty($m)) {
                                    # 创建监护人用户
                                    $user = User::create([
                                        'username'   => uniqid('custodian_'),
                                        'group_id'   => Group::whereName('监护人')->first()->id,
                                        'password'   => bcrypt('custodian8888'),
                                        'realname'   => $paths[1],
                                        'gender'     => $paths[2] == '男' ? '0' : '1',
                                        'avatar_url' => '00001.jpg',
                                        'userid'     => uniqid('custodian_'),
                                        'isleader'   => 0,
                                        'enabled'    => 1,
                                    ]);
                                    # 创建监护人
                                    $c = Custodian::create(['user_id' => $user['id']]);
                                    # 创建 监护关系
                                    CustodianStudent::create([
                                        'custodian_id' => $c['id'],
                                        'student_id'   => $s['id'],
                                        'relationship' => $paths[0],
                                        'enabled'      => 1,
                                    ]);
                                    # 创建监护人用户手机号码
                                    Mobile::create([
                                        'user_id'   => $user['id'],
                                        'mobile'    => $paths[3],
                                        'isdefault' => 1,
                                        'enabled'   => 1,
                                    ]);
                                    # 创建部门成员
                                    DepartmentUser::create([
                                        'department_id' => $insert['department_id'],
                                        'user_id'       => $user['id'],
                                        'enabled'       => 1,
                                    ]);
                                    # 创建企业号成员
                                    $user->createWechatUser($user['id']);
                                } else {
                                    # 手机号码存在时 更新user 再判断监护人是否存在 监护关系是否存在
                                    $user = User::find($m->user_id);
                                    if (!empty($user)) {
                                        $user->realname = $paths[1];
                                        $user->gender = $paths[2] == '男' ? '0' : '1';
                                        $user->save();
                                    }
                                    $c = Custodian::whereUserId($m->user_id)->first();
                                    # 监护人不存在时
                                    if (empty($c)) {
                                        # 创建监护人
                                        $custodian = Custodian::create(['user_id' => $m->user_id]);
                                        # 创建 监护关系
                                        CustodianStudent::create([
                                            'custodian_id' => $custodian['id'],
                                            'student_id'   => $s['id'],
                                            'relationship' => $paths[0],
                                            'enabled'      => 1,
                                        ]);
                                    } else {
                                        # 监护人存在 监护关系不存在时
                                        $csData = CustodianStudent::whereCustodianId($c['id'])
                                            ->where('student_id', $s['id'])
                                            ->first();
                                        if (empty($csData)) {
                                            # 创建 监护关系
                                            CustodianStudent::create([
                                                'custodian_id' => $c['id'],
                                                'student_id'   => $s['id'],
                                                'relationship' => $paths[0],
                                                'enabled'      => 1,
                                            ]);
                                        }
                                    }
                                    # 更新部门成员
                                    DepartmentUser::whereUserId($m->user_id)->delete();
                                    DepartmentUser::create([
                                        'department_id' => $insert['department_id'],
                                        'user_id'       => $m->user_id,
                                        'enabled'       => 1,
                                    ]);
                                    # 更新企业号监护人成员
                                    $user->updateWechatUser($m->user_id);
                                }
                            }
                        }
        
                    }
                    # 创建学生用户手机号码
                    Mobile::create([
                        'user_id'   => $u['id'],
                        'mobile'    => $insert['mobile'],
                        'isdefault' => 1,
                        'enabled'   => 1,
                    ]);
                    # 创建部门成员
                    DepartmentUser::create([
                        'department_id' => $insert['department_id'],
                        'user_id'       => $u['id'],
                        'enabled'       => 1,
                    ]);
                    # 创建企业号成员
                    $s->user->createWechatUser($u['id']);
                }
            });
        } catch (Exception $e) {
            throw $e;
        }
        
    }
    
    /**
     * 更新导入的学籍数据
     *
     * @param array $updates
     * @return bool
     * @throws Exception
     */
    private function update(array $updates) {
    
        try {
            DB::transaction(function () use ($updates) {
                foreach ($updates as $update) {
                    $relationship = str_replace(['，', '：'], [',', ':'], $update['relationship']);
                    $relationships = explode(',', $relationship);
                    $student = Student::whereStudentNumber($update['student_number'])->first();
                    $student->class_id = $update['class_id'];
                    $student->card_number = $update['card_number'];
                    $student->oncampus = $update['card_number'];
                    $student->birthday = $update['birthday'];
                    $student->remark = $update['remark'];
                    $student->save();
                    User::find($student->user_id)->update([
                        'realname' => $update['name'],
                        'gender'   => $update['gender'] == '男' ? '0' : '1',
                    ]);
                    Mobile::whereUserId($student->user_id)->update(['isdefault' => 0, 'enabled' => 0]);
                    Mobile::create([
                        'user_id'   => $student->user_id,
                        'mobile'    => $update['mobile'],
                        'isdefault' => 1,
                        'enabled'   => 1,
                    ]);
                    # 创建监护人关系
                    if (!empty($relationships)) {
                        foreach ($relationships as $r) {
                            $paths = explode(':', $r);
                            if (count($paths) == 4) {
                                $m = Mobile::whereMobile($paths[3])->first();
                                # 手机号码不存在时 增加监护人用户 如果存在则更新
                                if (empty($m)) {
                                    # 创建监护人用户
                                    $user = User::create([
                                        'username'   => uniqid('custodian_'),
                                        'group_id'   => Group::whereName('监护人')->first()->id,
                                        'password'   => bcrypt('custodian8888'),
                                        'realname'   => $paths[1],
                                        'gender'     => $paths[2] == '男' ? '0' : '1',
                                        'avatar_url' => '00001.jpg',
                                        'userid'     => uniqid('custodian_'),
                                        'isleader'   => 0,
                                        'enabled'    => 1,
                                    ]);
                                    # 创建监护人
                                    $c = Custodian::create(['user_id' => $user['id']]);
                                    # 创建 监护关系
                                    CustodianStudent::create([
                                        'custodian_id' => $c['id'],
                                        'student_id'   => $student->id,
                                        'relationship' => $paths[0],
                                        'enabled'      => 1,
                                    ]);
                                    # 创建监护人用户手机号码
                                    Mobile::create([
                                        'user_id'   => $user['id'],
                                        'mobile'    => $paths[3],
                                        'isdefault' => 1,
                                        'enabled'   => 1,
                                    ]);
                                    # 创建部门成员
                                    DepartmentUser::create([
                                        'department_id' => $update['department_id'],
                                        'user_id'       => $user['id'],
                                        'enabled'       => 1,
                                    ]);
                                    # 创建企业号成员
                                    $user->createWechatUser($user['id']);
                                } else {
                                    # 手机号码存在 反查用户表
                                    $user = User::find($m->user_id);
                                    # 用户存在时更新数据
                                    if (!empty($user)) {
                                        $user->realname = $paths[1];
                                        $user->gender = $paths[2] == '男' ? '0' : '1';
                                        $user->save();
                                    }
                                    $c = Custodian::whereUserId($m->user_id)->first();
                                    # 监护人不存在时
                                    if (empty($c)) {
                                        # 创建监护人
                                        $custodian = Custodian::create(['user_id' => $m->user_id]);
                                        # 创建 监护关系
                                        CustodianStudent::create([
                                            'custodian_id' => $custodian['id'],
                                            'student_id'   => $student->id,
                                            'relationship' => $paths[0],
                                            'enabled'      => 1,
                                        ]);
                                    } else {
                                        # 监护人存在 监护关系不存在时
                                        $csData = CustodianStudent::whereCustodianId($c['id'])
                                            ->where('student_id', $student->id)
                                            ->first();
                                        if (empty($csData)) {
                                            # 创建 监护关系
                                            CustodianStudent::create([
                                                'custodian_id' => $csData->id,
                                                'student_id'   => $student->id,
                                                'relationship' => $paths[0],
                                                'enabled'      => 1,
                                            ]);
                                        }
                                    }
                                    # 更新部门成员
                                    DepartmentUser::whereUserId($m->user_id)->delete();
                                    DepartmentUser::create([
                                        'department_id' => $update['department_id'],
                                        'user_id'       => $m->user_id,
                                        'enabled'       => 1,
                                    ]);
                                    # 更新企业号监护人成员
                                    $user->updateWechatUser($m->user_id);
                                }
                            }
                        }
                    }
                    # 更新部门成员
                    DepartmentUser::whereUserId($student->user_id)->delete();
                    DepartmentUser::create([
                        'department_id' => $update['department_id'],
                        'user_id'       => $student->user_id,
                        'enabled'       => 1,
                    ]);
                    # 更新企业号监护人成员
                    $student->user->updateWechatUser($student->user_id);
                }
            });
        } catch (Exception $e) {
            throw $e;
        }
    
        return true;
    
    }
    
}
