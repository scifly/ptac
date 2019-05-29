<?php
namespace App\Jobs;

use App\Apis\MassImport;
use App\Helpers\{Broadcaster, Constant, HttpStatusCode, JobTrait, ModelTrait};
use App\Models\{Department,
    DepartmentUser,
    Educator,
    EducatorClass,
    Grade,
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
use Throwable;
use Validator;

/**
 * Class ImportEducator
 * @package App\Jobs
 */
class ImportEducator implements ShouldQueue, MassImport {
    
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, ModelTrait, JobTrait;
    
    protected $data, $schoolId, $groupId, $userId, $response, $broadcaster, $members, $school;
    
    /**
     * Create a new job instance.
     *
     * @param array $data
     * @param $schoolId
     * @param $groupId
     * @param $userId
     * @throws PusherException
     */
    function __construct(array $data, $schoolId, $groupId, $userId) {
        
        $this->data = $data;
        $this->schoolId = $schoolId;
        $this->school = School::find($schoolId);
        $this->groupId = $groupId;
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
     * 验证导入数据合法性
     *
     * @param array $data
     * @return array
     */
    function validate(array $data) {
        
        $fields = [
            'name', 'gender', 'username', 'position', 'departments',
            'mobile', 'grades', 'classes', 'classes_subjects',
        ];
        $rules = array_combine($fields, [
            'required|string|between:2,20',
            ['required', Rule::in(['男', '女'])],
            'required|string',
            'nullable|string|between:1,60',
            'required|string',
            'required|regex:/^1[3456789][0-9]{9}$/',
            'nullable|string',
            'nullable|string',
            'nullable|string',
        ]);
        $isSchoolValid = in_array($this->schoolId, $this->schoolIds($this->userId));
        $schoolDepartmentIds = array_merge(
            [$this->school->department_id],
            (new Department)->subIds($this->school->department_id)
        );
        foreach ($data as &$datum) {
            $paths = explode(' . ', $datum['E']);
            $department = $paths[sizeof($paths) - 1];
            $user = array_combine($fields, [
                $datum['A'], $datum['B'], $datum['C'],
                $datum['D'], $department, $datum['F'],
                $datum['G'], $datum['H'], $datum['I'],
            ]);
            $result = Validator::make($user, $rules);
            $failed = $result->fails();
            $departments = explode(',', $user['departments']);
            $isDepartmentValid = true;
            foreach ($departments as $d) {
                if (!($department = Department::whereName($d)->whereIn('id', $schoolDepartmentIds)->first())) {
                    $isDepartmentValid = false;
                    break;
                }
            }
            if (!(!$failed && $isSchoolValid && $isDepartmentValid)) {
                $datum['J'] = $failed
                    ? json_encode($result->errors(), JSON_UNESCAPED_UNICODE)
                    : __('messages.educator.import_validation_error');
                $illegals[] = $datum;
                continue;
            }
            $user['departments'] = $departments;
            $user['school_id'] = $this->schoolId;
            if ($mobile = Mobile::where(['mobile' => $user['mobile'], 'isdefault' => 1])->first()) {
                if ($mobile->user->educator) {
                    $updates[] = $user;
                } else {
                    $datum['J'] = '手机号码已存在';
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
                foreach ($inserts as $insert) {
                    # 创建用户
                    $user = User::create(
                        array_combine(Constant::USER_FIELDS, [
                            $insert['username'], $this->groupId, bcrypt('12345678'),
                            $insert['name'], $insert['gender'] == '男' ? '1' : '0',
                            uniqid('ptac_'), $insert['position'], 1,
                        ])
                    );
                    # 创建教职员工
                    $educator = Educator::create(
                        array_combine(
                            (new Educator)->getFillable(),
                            [$user->id, $this->schoolId, 0, 1]
                        )
                    );
                    # 创建用户手机号码
                    Mobile::create(
                        array_combine(
                            (new Mobile)->getFillable(),
                            [$user->id, $insert['mobile'], 1, 1]
                        )
                    );
                    # 添加用户 & 部门绑定关系
                    $this->updateDu($user->id, $insert);
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
                foreach ($updates as $update) {
                    $mobile = Mobile::whereMobile($update['mobile'])->first();
                    # 更新用户
                    if (!($user = User::find($mobile->user_id))) continue;
                    $user->update([
                        'realname' => $update['name'],
                        'gender'   => $update['gender'] == '男' ? '0' : '1',
                    ]);
                    # 更新教职员工
                    $educator = Educator::firstOrCreate(
                        array_combine(
                            (new Educator)->getFillable(),
                            [$user->id, $this->schoolId, 0, 1]
                        )
                    );
                    # 更新用户部门绑定关系
                    $this->updateDu($user->id, $update);
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
     * 创建/更新当前导入用户的(班级/年级主任、任教科目等)绑定关系
     *
     * @param array $data
     * @param Educator $educator
     * @throws Throwable
     */
    private function binding(array $data, Educator $educator) {
        
        try {
            DB::transaction(function () use ($data, $educator) {
                list($gNames, $cNames, $cses) = array_map(
                    function ($str) use ($data) {
                        return explode(',', str_replace(['，', '：'], [',', ':'], $data[$str]));
                    }, ['grades', 'classes', 'classes_subjects']
                );
                # 更新年级主任
                foreach ($gNames as $gName) {
                    $condition = ['school_id' => $this->schoolId, 'name' => $gName];
                    if (!($grade = Grade::where($condition)->first())) continue;
                    $educatorIds = array_merge(
                        explode(',', $grade->educator_ids),
                        [$educator->id]
                    );
                    $grade->educator_ids = implode(',', array_unique($educatorIds));
                    $grade->save();
                    # 更新部门&用户绑定关系
                    $this->refreshDu($grade->department_id, $educator->user_id);
                }
                # 更新班级主任
                $gradeIds = $this->school->grades->pluck('id')->toArray();
                foreach ($cNames as $cName) {
                    if (!($class = Squad::whereName($cName)->whereIn('grade_id', $gradeIds)->first())) continue;
                    $educatorIds = array_merge(explode(',', $class->educator_ids), [$educator->id]);
                    $class->update(['educator_ids' => implode(',', array_unique($educatorIds))]);
                    $this->refreshDu($class->department_id, $educator->user_id);
                }
                # 更新班级科目绑定关系
                foreach ($cses as $cs) {
                    if (empty($cs)) continue;
                    $paths = explode(':', $cs);
                    $class = Squad::whereName($paths[0])->whereIn('grade_id', $gradeIds)->first();
                    $subject = Subject::where(['name' => $paths[1], 'school_id' => $this->schoolId])->get()->first();
                    if (!isset($class, $subject)) continue;
                    EducatorClass::firstOrCreate(
                        array_combine(
                            (new EducatorClass)->getFillable(),
                            [$educator->id, $class->id, $subject->id, 1]
                        )
                    );
                    # 更新部门&用户绑定关系
                    $this->refreshDu($class->department_id, $educator->user_id);
                }
            });
        } catch (Exception $e) {
            throw $e;
        }
        
    }
    
    /**
     * 创建/更新当前导入用户的部门绑定关系
     *
     * @param integer $userId
     * @param array $record
     */
    private function updateDu($userId, array $record) {
        
        $d = new Department;
        $du = new DepartmentUser;
        $du->where(['user_id' => $userId, 'enabled' => 1])->delete();
        $schoolDepartmentIds = array_merge(
            [$this->school->department_id],
            $d->subIds($this->school->department_id)
        );
        $departmentIds = array_intersect(
            $d->whereIn('name', $record['departments'])->pluck('id')->toArray(),
            $schoolDepartmentIds
        );
        foreach ($departmentIds as $departmentId) {
            $records[] = array_combine(
                $du->getFillable(),
                [$departmentId, $userId, 1]
            );
        }
        $du->insert($records ?? []);
        
    }
    
    /**
     * 更新部门 & 用户绑定关系
     *
     * @param $departmentId
     * @param $userId
     */
    function refreshDu($departmentId, $userId) {
        
        DepartmentUser::firstOrCreate(
            array_combine(
                (new DepartmentUser)->getFillable(),
                [$departmentId, $userId, 1]
            )
        );
        
    }
    
}
