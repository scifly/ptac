<?php
namespace App\Jobs;

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
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ImportEducator implements ShouldQueue {
    
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    
    protected $data, $userId;
    
    /**
     * Create a new job instance.
     *
     * @param array $data
     * @param $userId
     */
    public function __construct(array $data, $userId) {
        
        $this->data = $data;
        $this->userId = $userId;
        
    }
    
    /**
     * @return bool
     * @throws Exception
     * @throws \Throwable
     */
    public function handle() {
        
        $response = [
            'userId' => $this->userId,
            'title' => '批量导入教职员工',
            'statusCode' => HttpStatusCode::OK,
            'message' => __('messages.educator.educator_imported')
        ];
        $rows = $this->data;
        try {
            DB::transaction(function () use ($rows, $response) {
                $schoolId = $rows[0]['school_id'];
                $school = School::find($schoolId);
                if (!$school) {
                    throw new NotFoundHttpException(__('messages.school_not_found'));
                }
                $schoolDepartmentId = $school->department_id;
                $schoolDepartmentIds = array_merge(
                    [$schoolDepartmentId],
                    (new Department())->subDepartmentIds($schoolDepartmentId)
                );
                $groupId = Group::whereName('教职员工')->where('school_id', $schoolId)->first()->id;
                foreach ($rows as $row) {
                    $mobile = Mobile::whereMobile($row['mobile'])->first();
                    if (!$mobile) {
                        # 创建用户
                        $user = User::create([
                            'username'   => uniqid('educator_'),
                            'group_id'   => $groupId,
                            'password'   => bcrypt('12345678'),
                            'realname'   => $row['name'],
                            'gender'     => $row['gender'] == '男' ? '0' : '1',
                            'avatar_url' => '00001.jpg',
                            'userid'     => uniqid('educator_'),
                            'isleader'   => 0,
                            'enabled'    => 1,
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
                        $paths = explode(':', $classSubject);
                        $class = Squad::whereName($paths[0])->where('school_id', $schoolId)->first();
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
            $response['statusCode'] = $e->getCode();
            $response['message'] = $e->getMessage();
        }
        event(new JobResponse($response));
        
        return true;
        
    }
    
    /**
     * 更新部门&用户绑定关系
     *
     * @param User $user
     * @param $departmentId
     */
    private function updateDu(User $user, $departmentId) {
    
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
