<?php
namespace App\Jobs;

use App\Helpers\JobTrait;
use App\Helpers\ModelTrait;
use Exception;
use App\Models\User;
use App\Models\Group;
use App\Models\Grade;
use App\Models\Squad;
use App\Models\School;
use App\Models\Mobile;
use App\Models\Subject;
use App\Models\Educator;
use App\Models\Department;
use App\Events\JobResponse;
use Illuminate\Bus\Queueable;
use App\Models\EducatorClass;
use App\Models\DepartmentUser;
use App\Helpers\HttpStatusCode;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Validator;

/**
 * Class ImportEducator
 * @package App\Jobs
 */
class ImportEducator implements ShouldQueue {
    
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, ModelTrait, JobTrait;
    
    protected $data, $userId;
    
    /**
     * Create a new job instance.
     *
     * @param array $data
     * @param $userId
     */
    function __construct(array $data, $userId) {
        
        $this->data = $data;
        $this->userId = $userId;
        
    }
    
    /**
     * @return bool
     * @throws Exception
     * @throws \Throwable
     */
    function handle() {
        
        $response = [
            'userId' => $this->userId,
            'title' => __('messages.educator.title'),
            'statusCode' => HttpStatusCode::OK,
            'message' => __('messages.educator.educator_imported')
        ];
        $rows = $this->data;
        try {
            DB::transaction(function () use ($rows, $response) {
                $school = School::whereName($rows[0]['school'])->first();
                throw_if(!$school, new NotFoundHttpException(__('messages.school_not_found')));
                $schoolId = $school->id;
                $schoolDepartmentId = $school->department_id;
                $schoolDepartmentIds = array_merge(
                    [$schoolDepartmentId],
                    (new Department)->subDepartmentIds($schoolDepartmentId)
                );
                $group = Group::whereName('教职员工')->where('school_id', $schoolId)->first();
                throw_if(!$group, new NotFoundHttpException(__('messages.group.not_found')));
                foreach ($rows as $row) {
                    $mobile = Mobile::whereMobile($row['mobile'])->first();
                    if (!$mobile) {
                        # 创建用户
                        $user = User::create([
                            'username'   => uniqid('educator_'),
                            'group_id'   => $group->id,
                            'password'   => bcrypt('12345678'),
                            'realname'   => $row['name'],
                            'gender'     => $row['gender'] == '男' ? '0' : '1',
                            'userid'     => uniqid('educator_'),
                            'isleader'   => 0,
                            'enabled'    => 1,
                            'synced'     => 0,
                            'subscribed' => 0
                        ]);
                        # 创建教职员工
                        $educator = Educator::create([
                            'user_id'   => $user->id,
                            'school_id' => $schoolId,
                            'sms_quote' => 0,
                            'enabled'   => 1,
                        ]);
                        # 创建用户手机号码
                        Mobile::create([
                            'user_id'   => $user->id,
                            'mobile'    => $row['mobile'],
                            'isdefault' => 1,
                            'enabled'   => 1,
                        ]);
                        # 添加用户&部门绑定关系
                        foreach ($row['departments'] as $departmentName) {
                            $department = Department::whereName($departmentName)
                                ->whereIn('id', $schoolDepartmentIds)->first();
                            DepartmentUser::create([
                                'department_id' => $department->id,
                                'user_id'       => $user->id,
                                'enabled'       => 1,
                            ]);
                        }
                        # 创建企业号成员
                        $user->createWechatUser($user->id);
                    } else {
                        # 更新用户
                        $user = User::find($mobile->user_id);
                        if (!$user) { continue; }
                        $user->realname = $row['name'];
                        $user->gender = $row['gender'] == '男' ? '0' : '1';
                        $user->save();
                        # 更新教职员工
                        $educator = $user->educator ?? Educator::create([
                                'user_id'   => $user->id,
                                'school_id' => $schoolId,
                                'sms_quote' => 0,
                                'enabled'   => 1,
                            ]);
                        # 更新用户部门绑定关系
                        DepartmentUser::whereUserId($user->id)->delete();
                        foreach ($row['departments'] as $departmentName) {
                            $department = Department::whereName($departmentName)
                                ->whereIn('id', $schoolDepartmentIds)->first();
                            DepartmentUser::create([
                                'department_id' => $department->id,
                                'user_id'       => $user->id,
                                'enabled'       => 1,
                            ]);
                        }
                        # 更新企业号成员
                        $user->updateWechatUser($user->id);
                    }
                    # 更新年级主任
                    $gradeNames = explode(',', str_replace(['，', '：'], [',', ':'], $row['grades']));
                    foreach ($gradeNames as $gradeName) {
                        $grade = Grade::whereSchoolId($schoolId)->where('name', $gradeName)->first();
                        if (!$grade) { continue; }
                        $educatorIds = array_merge(
                            explode(',', $grade->educator_ids),
                            [$educator->id]
                        );
                        $grade->educator_ids = implode(',', array_unique($educatorIds));
                        $grade->save();
                        # 更新部门&用户绑定关系
                        $this->updateDu($user, $grade->department_id);
                    }
                    # 更新班级主任
                    $classeNames = explode(',', str_replace(['，', '：'], [',', ':'], $row['classes']));
                    $gradeIds = $school->grades->pluck('id')->toArray();
                    foreach ($classeNames as $classeName) {
                        $class = Squad::whereName($classeName)->whereIn('grade_id', $gradeIds)->first();
                        if (!$class) { continue; }
                        $educatorIds = array_merge(
                            explode(',', $class->educator_ids),
                            [$educator->id]
                        );
                        $class->educator_ids = implode(',', array_unique($educatorIds));;
                        $class->save();
                        # 更新部门&用户绑定关系
                        $this->updateDu($user, $class->department_id);
                    }
                    # 更新班级科目绑定关系
                    $classSubjects = explode(',', str_replace(['，', '：'], [',', ':'], $row['classes_subjects']));
                    foreach ($classSubjects as $classSubject) {
                        if (empty($classSubject)) { continue; }
                        $paths = explode(':', $classSubject);
                        $class = Squad::whereName($paths[0])->whereIn('grade_id', $gradeIds)->first();
                        $subject = Subject::whereName($paths[1])->where('school_id', $schoolId)->first();
                        if (!$class || !$subject) { continue; }
                        $educatorClass = EducatorClass::whereEducatorId($educator->id)
                            ->where('class_id', $class->id)
                            ->where('subject_id', $subject->id)
                            ->first();
                        if (!$educatorClass) {
                            EducatorClass::create([
                                'educator_id' => $educator->id,
                                'class_id'    => $class->id,
                                'subject_id'  => $subject->id,
                                'enabled'     => 1,
                            ]);
                        }
                        # 更新部门&用户绑定关系
                        $this->updateDu($user, $class->department_id);
                    }
                }
            });
        } catch (Exception $e) {
            Log::error(
                get_class($e) .
                '(code: ' . $e->getCode() . '): ' .
                $e->getMessage() . ' at ' .
                $e->getFile() . ' on line ' .
                $e->getLine()
            );
            $response['statusCode'] = $e->getCode();
            $response['message'] = $e->getMessage();
        }
        event(new JobResponse($response));
        
        return true;
        
    }
    
    /**
     * 验证导入数据合法性
     *
     * @param array $data
     * @return array
     */
    function validate(array $data) {
    
        $rules = [
            'name'             => 'required|string|between:2,20',
            'gender'           => ['required', Rule::in(['男', '女'])],
            'birthday'         => 'required|date',
            'school'           => 'required|string|between:4,20',
            'mobile'           => 'required', new Mobile(),
            'grades'           => 'nullable|string',
            'classes'          => 'nullable|string',
            'classes_subjects' => 'nullable|string',
            'departments'      => 'required|string',
        ];
        // Validator::make($data,$rules);
        # 非法数据
        $illegals = [];
        # 需要更新的数据
        $updates = [];
        # 需要添加的数据
        $inserts = [];
        foreach ($data as &$datum) {
            $user = [
                'name'             => $datum['A'],
                'gender'           => $datum['B'],
                'birthday'         => $datum['C'],
                'school'           => $datum['D'],
                'mobile'           => $datum['E'],
                'grades'           => $datum['F'],
                'classes'          => $datum['G'],
                'classes_subjects' => $datum['H'],
                'departments'      => $datum['I'],
            ];
            $result = Validator::make($user, $rules);
            $failed = $result->fails();
            $school = !$failed ? School::whereName($user['school'])->first() : null;
            $isSchoolValid = $school ? in_array($school->id, $this->schoolIds($this->userId)) : false;
            $departments = explode(',', $user['departments']);
            $schoolDepartmentIds = array_merge(
                [$school->department_id],
                (new Department)->subDepartmentIds($school->department_id)
            );
            $isDepartmentValid = true;
            foreach ($departments as $d) {
                $department = Department::whereName($d)
                    ->whereIn('id', $schoolDepartmentIds)
                    ->first();
                if (!$department) {
                    $isDepartmentValid = false;
                    break;
                }
            }
            if (!(!$failed && $isSchoolValid && $isDepartmentValid)) {
                $datum['J'] = $failed
                    ? json_encode($result->errors())
                    : __('messages.educator.import_validation_error');
                $illegals[] = $datum;
                continue;
            }
            $user['departments'] = $departments;
            $user['school_id'] = $school->id;
            if (Mobile::whereMobile($user['mobile'])->where('isdefault', 1)->first()) {
                $updates[] = $user;
            } else {
                $inserts[] = $user;
            }
        }
    
        return [$inserts, $updates, $illegals];
        
    }
    
    /**
     * 插入需导入的数据
     *
     * @param array $inserts
     * @return bool
     * @throws Exception
     */
    function insert(array $inserts) {
        
        try {
            DB::transaction(function () use ($inserts) {
                $school = School::whereName($inserts[0]['school'])->first();
                throw_if(!$school, new NotFoundHttpException(__('messages.school_not_found')));
                $schoolId = $school->id;
                $schoolDepartmentId = $school->department_id;
                $schoolDepartmentIds = array_merge(
                    [$schoolDepartmentId],
                    (new Department())->subDepartmentIds($schoolDepartmentId)
                );
                $group = Group::whereName('教职员工')->where('school_id', $schoolId)->first();
                throw_if(!$group, new NotFoundHttpException(__('messages.group.not_found')));
                foreach ($inserts as $insert) {
                    # 创建用户
                    $user = User::create([
                        'username'   => uniqid('educator_'),
                        'group_id'   => $group->id,
                        'password'   => bcrypt('12345678'),
                        'realname'   => $insert['name'],
                        'gender'     => $insert['gender'] == '男' ? '0' : '1',
                        'userid'     => uniqid('educator_'),
                        'isleader'   => 0,
                        'enabled'    => 1,
                        'synced'     => 0,
                        'subscribed' => 0
                    ]);
                    # 创建教职员工
                    $educator = Educator::create([
                        'user_id'   => $user->id,
                        'school_id' => $schoolId,
                        'sms_quote' => 0,
                        'enabled'   => 1,
                    ]);
                    # 创建用户手机号码
                    Mobile::create([
                        'user_id'   => $user->id,
                        'mobile'    => $insert['mobile'],
                        'isdefault' => 1,
                        'enabled'   => 1,
                    ]);
                    # 添加用户&部门绑定关系
                    foreach ($insert['departments'] as $departmentName) {
                        $department = Department::whereName($departmentName)
                            ->whereIn('id', $schoolDepartmentIds)->first();
                        DepartmentUser::create([
                            'department_id' => $department->id,
                            'user_id'       => $user->id,
                            'enabled'       => 1,
                        ]);
                    }
                    # 更新绑定关系
                    $this->binding($insert, $educator);
                    # 创建企业号成员
                    $user->createWechatUser($user->id);
                }
            });
        } catch (Exception $e) {
            event(new JobResponse([
                'userId'     => $this->userId,
                'title'      => __('messages.educator.title'),
                'statusCode' => HttpStatusCode::INTERNAL_SERVER_ERROR,
                'message'    => $e->getMessage(),
            ]));
            throw $e;
        }
        
        return true;
        
    }
    
    function update(array $updates) {
        
        try {
        
        } catch (Exception $e) {
            event(new JobResponse([
                'userId'     => $this->userId,
                'title'      => __('messages.educator.title'),
                'statusCode' => HttpStatusCode::INTERNAL_SERVER_ERROR,
                'message'    => $e->getMessage(),
            ]));
            throw $e;
        }
        
        return true;
        
    }
    
    /**
     * 更新教职员工的年级主任、班级主任以及班级科目等绑定关系
     *
     * @param array $data
     * @param Educator $educator
     */
    function binding(array $data, Educator $educator) {
    
        $school = School::find($educator->school_id);
        # 更新年级主任
        $gradeNames = explode(',', str_replace(['，', '：'], [',', ':'], $data['grades']));
        foreach ($gradeNames as $gradeName) {
            $grade = Grade::whereSchoolId($school->id)->where('name', $gradeName)->first();
            if (!$grade) { continue; }
            $educatorIds = array_merge(
                explode(',', $grade->educator_ids),
                [$educator->id]
            );
            $grade->educator_ids = implode(',', array_unique($educatorIds));
            $grade->save();
            # 更新部门&用户绑定关系
            $this->updateDu($educator->user, $grade->department_id);
        }
        # 更新班级主任
        $classeNames = explode(',', str_replace(['，', '：'], [',', ':'], $data['classes']));
        $gradeIds = $school->grades->pluck('id')->toArray();
        foreach ($classeNames as $classeName) {
            $class = Squad::whereName($classeName)->whereIn('grade_id', $gradeIds)->first();
            if (!$class) { continue; }
            $educatorIds = array_merge(
                explode(',', $class->educator_ids),
                [$educator->id]
            );
            $class->educator_ids = implode(',', array_unique($educatorIds));;
            $class->save();
            # 更新部门&用户绑定关系
            $this->updateDu($educator->user, $class->department_id);
        }
        # 更新班级科目绑定关系
        $classSubjects = explode(',', str_replace(['，', '：'], [',', ':'], $data['classes_subjects']));
        foreach ($classSubjects as $classSubject) {
            if (empty($classSubject)) { continue; }
            $paths = explode(':', $classSubject);
            $class = Squad::whereName($paths[0])->whereIn('grade_id', $gradeIds)->first();
            $subject = Subject::whereName($paths[1])->where('school_id', $school->id)->first();
            if (!$class || !$subject) { continue; }
            $educatorClass = EducatorClass::whereEducatorId($educator->id)
                ->where('class_id', $class->id)
                ->where('subject_id', $subject->id)
                ->first();
            if (!$educatorClass) {
                EducatorClass::create([
                    'educator_id' => $educator->id,
                    'class_id'    => $class->id,
                    'subject_id'  => $subject->id,
                    'enabled'     => 1,
                ]);
            }
            # 更新部门&用户绑定关系
            $this->updateDu($educator->user, $class->department_id);
        }
        
    }
    
    /**
     * 更新部门&用户绑定关系
     *
     * @param User $user
     * @param $departmentId
     */
    function updateDu(User $user, $departmentId) {
    
        $du = DepartmentUser::whereUserId($user->id)
            ->where('department_id', $departmentId)
            ->first();
        if (!$du) {
            DepartmentUser::create([
                'user_id' => $user->id,
                'department_id' => $departmentId,
                'enabled' => 1
            ]);
            
            $user->updateWechatUser($user->id);
        }
        
    }
    
}
