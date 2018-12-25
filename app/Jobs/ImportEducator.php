<?php
namespace App\Jobs;

use App\Apis\MassImport;
use App\Helpers\{Broadcaster, Constant, HttpStatusCode, JobTrait, ModelTrait};
use App\Models\{Department,
    DepartmentUser,
    Educator,
    EducatorClass,
    Grade,
    Group,
    Mobile,
    School,
    Squad,
    Subject,
    User};
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
 * Class ImportEducator
 * @package App\Jobs
 */
class ImportEducator implements ShouldQueue, MassImport {
    
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, ModelTrait, JobTrait;
    
    protected $data, $userId, $response, $broadcaster, $members;
    
    /**
     * Create a new job instance.
     *
     * @param array $data
     * @param $userId
     * @throws \Pusher\PusherException
     */
    function __construct(array $data, $userId) {
        
        $this->data = $data;
        $this->userId = $userId;
        $this->response = array_combine(Constant::BROADCAST_FIELDS, [
            $userId, __('messages.educator.title'),
            HttpStatusCode::OK, __('messages.educator.import_completed'),
        ]);
        $this->broadcaster = new Broadcaster();
        
    }
    
    /**
     * @return bool
     * @throws Exception
     * @throws \Throwable
     */
    function handle() {
        
        $imported = $this->import($this, $this->response);
        !$imported ?: (new User)->sync(
            $this->members, $this->userId,
            User::find($this->members[0][0])->educator->school->corp_id
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
     * 验证导入数据合法性
     *
     * @param array $data
     * @return array
     */
    function validate(array $data) {
        
        $fields = [
            'name', 'gender', 'username', 'position', 'departments',
            'school', 'mobile', 'grades', 'classes', 'classes_subjects',
        ];
        $rules = array_combine($fields, [
            'required|string|between:2,20',
            ['required', Rule::in(['男', '女'])],
            'required|string',
            'nullable|string|between:1,60',
            'required|string',
            'required|string',
            'required|regex:/^1[3456789][0-9]{9}$/',
            'nullable|string',
            'nullable|string',
            'nullable|string',
        ]);
        foreach ($data as &$datum) {
            $paths = explode(' . ', $datum['E']);
            $department = $paths[sizeof($paths) - 1];
            $user = array_combine($fields, [
                $datum['A'], $datum['B'], $datum['C'],
                $datum['D'], $department, $datum['F'],
                $datum['G'], $datum['H'], $datum['I'],
                $datum['J'],
            ]);
            $result = Validator::make($user, $rules);
            $failed = $result->fails();
            $school = !$failed ? School::whereName($user['school'])->first() : null;
            $schoolDepartmentId = $school ? $school->department_id : 0;
            $isSchoolValid = $school ? in_array($school->id, $this->schoolIds($this->userId)) : false;
            $departments = explode(',', $user['departments']);
            $schoolDepartmentIds = array_merge(
                [$schoolDepartmentId],
                (new Department)->subIds($schoolDepartmentId)
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
                $datum['K'] = $failed
                    ? json_encode($result->errors(), JSON_UNESCAPED_UNICODE)
                    : __('messages.educator.import_validation_error');
                $illegals[] = $datum;
                continue;
            }
            $user['departments'] = $departments;
            $user['school_id'] = $school->id;
            $mobile = Mobile::where(['mobile' => $user['mobile'], 'isdefault' => 1])->first();
            if ($mobile) {
                if ($mobile->user->educator) {
                    $updates[] = $user;
                } else {
                    $datum['K'] = '手机号码已存在';
                    $illegals[] = $datum;
                }
            } else {
                $inserts[] = $user;
            }
        }
        
        return [$inserts ?? [], $updates ?? [], $illegals ?? []];
        
    }
    
    /**
     * 插入需导入的数据
     *
     * @param array $inserts
     * @return bool
     * @throws Throwable
     */
    function insert(array $inserts) {
        
        try {
            DB::transaction(function () use ($inserts) {
                $school = School::whereName($inserts[0]['school'])->first();
                throw_if(!$school, new NotFoundHttpException(__('messages.school_not_found')));
                $schoolId = $school->id;
                $group = Group::whereName('教职员工')->where('school_id', $schoolId)->first();
                throw_if(!$group, new NotFoundHttpException(__('messages.group.not_found')));
                foreach ($inserts as $insert) {
                    # 创建用户
                    $user = User::create(
                        array_combine(Constant::USER_FIELDS, [
                            $insert['username'], $group->id, bcrypt('12345678'), $insert['name'],
                            $insert['gender'] == '男' ? '1' : '0', uniqid('ptac_'),
                            $insert['position'], 0, 1,
                        ])
                    );
                    # 创建教职员工
                    $educator = Educator::create(
                        array_combine(Constant::EDUCATOR_FIELDS, [
                            $user->id, $schoolId, 0, 1,
                        ])
                    );
                    # 创建用户手机号码
                    Mobile::create(
                        array_combine(Constant::MOBILE_FIELDS, [
                            $user->id, $insert['mobile'], 1, 1,
                        ])
                    );
                    # 添加用户 & 部门绑定关系
                    $this->updateDu($school, $user, $insert);
                    # 更新(班级/年级主任、任教科目等)绑定关系
                    $this->binding($insert, $educator);
                    # 需要新增的企业微信会员
                    $this->members[] = [$user->id, 'educator', 'create'];
                }
            });
        } catch (Exception $e) {
            $this->eHandler($e, $this->response);
            throw $e;
        }
        
        return true;
        
    }
    
    /**
     * 更新已导入的数据
     *
     * @param array $updates
     * @return bool
     * @throws Throwable
     */
    function update(array $updates) {
        
        try {
            DB::transaction(function () use ($updates) {
                $school = School::whereName($updates[0]['school'])->first();
                throw_if(!$school, new NotFoundHttpException(__('messages.school_not_found')));
                $schoolId = $school->id;
                $group = Group::whereName('教职员工')->where('school_id', $schoolId)->first();
                throw_if(!$group, new NotFoundHttpException(__('messages.group.not_found')));
                foreach ($updates as $update) {
                    $mobile = Mobile::whereMobile($update['mobile'])->first();
                    # 更新用户
                    $user = User::find($mobile->user_id);
                    if (!$user) continue;
                    $user->update([
                        'realname' => $update['name'],
                        'gender'   => $update['gender'] == '男' ? '0' : '1',
                    ]);
                    # 更新教职员工
                    $educator = Educator::firstOrCreate(
                        array_combine(Constant::EDUCATOR_FIELDS, [
                            $user->id, $schoolId, 0, 1,
                        ])
                    );
                    # 更新用户部门绑定关系
                    $this->updateDu($school, $user, $update);
                    # 更新(班级/年级主任、任教科目等)绑定关系
                    $this->binding($update, $educator);
                    # 更新卡德人员
                    $this->members[] = [$user->id, 'educator', 'update'];
                }
            });
        } catch (Exception $e) {
            $this->eHandler($e, $this->response);
            throw $e;
        }
        
        return true;
        
    }
    
    /**
     * 创建/更新当前导入用户的部门绑定关系
     *
     * @param School $school
     * @param User $user
     * @param array $record
     * @throws Exception
     */
    private function updateDu(School $school, User $user, array $record) {
        
        $department = new Department;
        $department->where(['user_id' => $user->id, 'enabled' => 1])->delete();
        $schoolDepartmentId = $school->department_id;
        $schoolDepartmentIds = array_merge(
            [$schoolDepartmentId],
            $department->subIds($schoolDepartmentId)
        );
        $departmentIds = array_intersect(
            $department->whereIn('name', $record['departments'])->pluck('id')->toArray(),
            $schoolDepartmentIds
        );
        foreach ($departmentIds as $departmentId) {
            $records[] = array_combine(Constant::DU_FIELDS, [
                $departmentId, $user->id, 1,
            ]);
        }
        DepartmentUser::insert($records ?? []);
        
    }
    
    /**
     * 创建/更新当前导入用户的(班级/年级主任、任教科目等)绑定关系
     *
     * @param array $data
     * @param Educator $educator
     * @throws Throwable
     */
    private function binding(array $data, Educator $educator) {
        
        try {
            DB::transaction(function () use ($data, $educator) {
                $school = School::find($educator->school_id);
                # 更新年级主任
                $gradeNames = explode(',', str_replace(['，', '：'], [',', ':'], $data['grades']));
                foreach ($gradeNames as $gradeName) {
                    $grade = Grade::whereSchoolId($school->id)->where('name', $gradeName)->first();
                    if (!$grade) continue;
                    $educatorIds = array_merge(
                        explode(',', $grade->educator_ids),
                        [$educator->id]
                    );
                    $grade->educator_ids = implode(',', array_unique($educatorIds));
                    $grade->save();
                    # 更新部门&用户绑定关系
                    DepartmentUser::firstOrCreate(
                        array_combine(Constant::DU_FIELDS, [$educator->user_id, $grade->department_id])
                    );
                }
                # 更新班级主任
                $classeNames = explode(',', str_replace(['，', '：'], [',', ':'], $data['classes']));
                $gradeIds = $school->grades->pluck('id')->toArray();
                foreach ($classeNames as $classeName) {
                    $class = Squad::whereName($classeName)->whereIn('grade_id', $gradeIds)->first();
                    if (!$class) continue;
                    $educatorIds = array_merge(explode(',', $class->educator_ids), [$educator->id]);
                    $class->update(['educator_ids' => implode(',', array_unique($educatorIds))]);
                    # 更新部门 & 用户绑定关系
                    DepartmentUser::firstOrCreate(
                        array_combine(Constant::DU_FIELDS, [$educator->user_id, $class->department_id])
                    );
                }
                # 更新班级科目绑定关系
                $classSubjects = explode(',', str_replace(['，', '：'], [',', ':'], $data['classes_subjects']));
                foreach ($classSubjects as $classSubject) {
                    if (empty($classSubject)) continue;
                    $paths = explode(':', $classSubject);
                    $class = Squad::whereName($paths[0])->whereIn('grade_id', $gradeIds)->first();
                    $subject = Subject::whereName($paths[1])->where('school_id', $school->id)->first();
                    if (!isset($class, $subject)) continue;
                    EducatorClass::firstOrCreate(
                        array_combine(Constant::EC_FIELDS, [$educator->id, $class->id, $subject->id, 1])
                    );
                    # 更新部门&用户绑定关系
                    DepartmentUser::firstOrCreate(
                        array_combine(Constant::DU_FIELDS, [$educator->user_id, $class->department_id])
                    );
                }
            });
        } catch (Exception $e) {
            throw $e;
        }
        
    }
    
}
