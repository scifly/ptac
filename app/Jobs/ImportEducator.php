<?php
namespace App\Jobs;

use App\Apis\MassImport;
use App\Helpers\{Broadcaster, Constant, HttpStatusCode, JobTrait, ModelTrait};
use App\Models\{ClassEducator, Department, DepartmentUser, Educator, Grade, School, Squad, Subject, User};
use Exception;
use Illuminate\{Bus\Queueable,
    Contracts\Queue\ShouldQueue,
    Database\Eloquent\Model,
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
    
    protected $data, $response, $groupId, $userId, $broadcaster;
    protected $schoolId, $school, $schoolDeptIds;
    protected $members, $directors, $dus;
    
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
        $this->schoolDeptIds = array_merge(
            [$this->school->department_id],
            (new Department)->subIds($this->school->department_id)
        );
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
        
        try {
            DB::transaction(function () {
                # 导入教职员工(创建/更新)
                $this->import($this);
                # 更新年级/班级主任
                foreach ($this->directors as $director) {
                    /**
                     * @var Educator $educator
                     * @var Model $model
                     */
                    [$model, $educator] = $director;
                    $educatorIds = array_unique(
                        array_merge(
                            explode(',', $model->{'educator_ids'}),
                            [$educator->id]
                        )
                    );
                    $model->update(['educator_ids' => join(',', $educatorIds)]);
                    $this->dus[] = [$model->{'department_id'}, $educator->user_id];
                }
                # 更新部门用户绑定关系
                foreach ($this->dus as $du) {
                    DepartmentUser::firstOrCreate(
                        array_combine(
                            (new DepartmentUser)->getFillable(),
                            array_merge($du, [1])
                        )
                    );
                }
                # 同步企业微信通讯录
                (new User)->sync($this->members, $this->userId);
            });
        } catch (Exception $e) {
            $this->eHandler($e, $this->response);
            throw $e;
        }
        $this->broadcaster->broadcast($this->response);
        
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
            $record = array_combine($fields, [
                $datum['A'], $datum['B'], $datum['C'],
                $datum['D'], $department, $datum['F'],
                $datum['G'], $datum['H'], $datum['I'],
            ]);
            $result = Validator::make($record, $rules);
            $failed = $result->fails();
            $departments = explode(',', $record['departments']);
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
            $record['departments'] = $departments;
            $record['school_id'] = $this->schoolId;
            if ($user = User::whereMobile($record['mobile'])->first()) {
                if ($user->educator) {
                    $updates[] = $record;
                } else {
                    $datum['J'] = '手机号码已存在';
                    $illegals[] = $datum;
                }
            } else {
                $inserts[] = $record;
            }
        }
        
        return [$inserts ?? [], $updates ?? [], $illegals ?? []];
        
    }
    
    /**
     * 插入需导入的数据
     *
     * @param array $records
     * @throws Throwable
     */
    function insert(array $records) {
        
        try {
            DB::transaction(function () use ($records) {
                foreach ($records as $record) {
                    $userId = User::insertGetId(
                        array_combine(Constant::USER_FIELDS, [
                            $record['username'], $this->groupId,
                            bcrypt('12345678'), $record['name'],
                            $record['gender'] == '男' ? '1' : '0',
                            $record['mobile'], Constant::ENABLED,
                            json_encode([
                                'userid' => uniqid('ptac_'),
                                'position' => $record['position'],
                            ], JSON_UNESCAPED_UNICODE),
                        ])
                    );
                    # 教职员工
                    $educator = Educator::create(
                        array_combine(
                            (new Educator)->getFillable(),
                            [$userId, $this->schoolId, 0, 0, 1]
                        )
                    );
                    $this->binding($educator, $record, 'create');
                }
            });
        } catch (Exception $e) {
            throw $e;
        }
        
    }
    
    /**
     * 更新已导入的数据
     *
     * @param array $records
     * @throws Throwable
     */
    function update(array $records) {
        
        try {
            DB::transaction(function () use ($records) {
                foreach ($records as $record) {
                    # 用户
                    if (!$user = User::whereMobile($record['mobile'])) continue;
                    $user->update([
                        'realname' => $record['name'],
                        'gender'   => $record['gender'] == '男' ? '0' : '1',
                    ]);
                    # 教职员工
                    $educator = Educator::firstOrCreate(
                        array_combine(
                            ['user_id', 'school_id', 'enabled'],
                            [$user->id, $this->schoolId, 1]
                        )
                    );
                    $this->binding($educator, $record, 'update');
                }
            });
        } catch (Exception $e) {
            throw $e;
        }
        
    }
    
    /**
     * 更新绑定关系、添加同步数据
     *
     * @param Educator $educator
     * @param array $record
     * @param $type
     * @throws Throwable
     */
    private function binding(Educator $educator, array $record, $type) {
    
        try {
           DB::transaction(function () use ($educator, $record, $type) {
               # 用户部门绑定关系
               (new DepartmentUser)->where([
                   'user_id' => $educator->user_id, 'enabled' => 1
               ])->delete();
               $deptIds = array_intersect(
                   (new Department)->whereIn('name', $record['departments'])
                       ->pluck('id')->toArray(),
                   $this->schoolDeptIds
               );
               foreach ($deptIds as $deptId) {
                   $this->dus[] = [$deptId, $educator->user_id];
               }
               # 班级/年级主任、任教科目等绑定关系
               [$gNames, $cNames, $cses] = array_map(
                   function ($field) use ($record) {
                       return explode(',', str_replace(['，', '：'], [',', ':'], $record[$field]));
                   }, ['grades', 'classes', 'classes_subjects']
               );
               # 年级主任
               foreach ($gNames as $gName) {
                   $condition = ['school_id' => $this->schoolId, 'name' => $gName];
                   if (!$grade = Grade::where($condition)->first()) continue;
                   $this->directors[] = [$grade, $educator];
               }
               # 班级主任
               $gradeIds = $this->school->grades->pluck('id')->toArray();
               foreach ($cNames as $cName) {
                   if (!$class = Squad::whereName($cName)->whereIn('grade_id', $gradeIds)->first()) continue;
                   $this->directors[] = [$class, $educator];
               }
               # 班级科目绑定关系
               foreach ($cses as $cs) {
                   if (empty($cs)) continue;
                   $paths = explode(':', $cs);
                   $class = Squad::whereName($paths[0])->whereIn('grade_id', $gradeIds)->first();
                   $subject = Subject::where(['name' => $paths[1], 'school_id' => $this->schoolId])->get()->first();
                   if (!isset($class, $subject)) continue;
                   ClassEducator::firstOrCreate(
                       array_combine(
                           (new ClassEducator)->getFillable(),
                           [$educator->id, $class->id, $subject->id, 1]
                       )
                   );
                   # 更新部门&用户绑定关系
                   $this->dus[] = [$class->department_id, $educator->user_id];
               }
               # 添加企业微信同步数据
               $this->members[] = [$educator->user_id, 'educator', $type];
           });
        } catch (Exception $e) {
            throw $e;
        }
        
    }
    
}
