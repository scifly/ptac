<?php
namespace App\Jobs;

use App\Events\eventTrigger;
use App\Models\Department;
use App\Models\DepartmentUser;
use App\Models\Educator;
use App\Models\EducatorClass;
use App\Models\Grade;
use App\Models\Group;
use App\Models\Mobile;
use App\Models\Squad;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Mockery\Exception;

class ManageImportEducator implements ShouldQueue {
    
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $data;
    
    /**
     * Create a new job instance.
     *
     * @param $data
     */
    public function __construct($data) {
        $this->data = $data;
    }
    
    public function handle() {
        Log::debug('import');
        $rows = $this->data;
        try {
            
            $exception = DB::transaction(function () use ($rows) {
                foreach ($rows as $datum) {
                    $m = Mobile::where('mobile', $datum['mobile'])->first();
                    # 手机号码不存在时 增加监护人用户 如果存在则更新
                    if (empty($m)) {
                        # 创建教职工用户
                        $user = [
                            'username'   => uniqid('educator_'),
                            'group_id'   => Group::whereName('教职员工')->first()->id,
                            'password'   => bcrypt('12345678'),
                            'realname'   => $datum['name'],
                            'gender'     => $datum['gender'] == '男' ? '0' : '1',
                            'avatar_url' => '00001.jpg',
                            'userid'     => uniqid('educator_'),
                            'isleader'   => 0,
                            'wechatid'   => '',
                            'enabled'    => 1,
                        ];
                        $userData = User::create($user);
                        # 创建教职员工记录
                        $educator = [
                            'user_id'   => $userData['id'],
                            'school_id' => $datum['school_id'],
                            'sms_quote' => 0,
                            'enabled'   => 1,
                        ];
                        $educatorData = Educator::create($educator);
                        $educator_id = $educatorData['id'];
                        # 创建用户手机号码
                        $mobile = [
                            'user_id'   => $userData['id'],
                            'mobile'    => $datum['mobile'],
                            'isdefault' => 1,
                            'enabled'   => 1,
                        ];
                        Mobile::create($mobile);
                        # 创建部门成员
                        foreach ($datum['departments'] as $d) {
                            $i = Department::whereName($d)->first();
                            $departmentUser = [
                                'department_id' => $i->id,
                                'user_id'       => $userData['id'],
                                'enabled'       => 1,
                            ];
                            DepartmentUser::create($departmentUser);
                        }
                        # 创建企业号成员
                        $userModel = new User();
                        $userModel->createWechatUser($userData['id']);
                        unset($userModel);
                        
                    } else {
                        $um = new User();
                        # 手机号码存在时 更新user
                        $u = User::whereId($m->user_id)->first();
                        if (!empty($u)) {
                            $u->realname = $datum['name'];
                            $u->gender = $datum['gender'] == '男' ? '0' : '1';
                            $um->save();
                        }
                        $um->updateWechatUser($m->user_id);
                        unset($um);
                        $e = Educator::whereId($m->user_id)->first();
                        # 教职工存在
                        if (!empty($e)) {
                            $e->school_id = $datum['school_id'];
                            $e->save();
                            $educator_id = $e->id;
                        } else {
                            # 创建教职员工记录
                            $edu = [
                                'user_id'   => $m->user_id,
                                'school_id' => $datum['school_id'],
                                'sms_quote' => 0,
                                'enabled'   => 1,
                            ];
                            $eduData = Educator::create($edu);
                            $educator_id = $eduData['id'];
                        }
                        unset($em);
                        # 更新部门成员
                        DepartmentUser::where('user_id', $m->user_id)->delete();
                        foreach ($datum['departments'] as $d) {
                            $i = Department::whereName($d)->first();
                            $departmentUser = [
                                'department_id' => $i->id,
                                'user_id'       => $m->user_id,
                                'enabled'       => 1,
                            ];
                            DepartmentUser::create($departmentUser);
                        }
                        # 更新企业号成员
                        $userUpdateModel = new User();
                        $userUpdateModel->updateWechatUser($m->user_id);
                        unset($userUpdateModel);
                    }
                    # 年级数据 存在时更新年级主任数据
                    $gradeInput = str_replace(['，', '：'], [',', ':'], $datum['grades']);
                    $grades = explode(',', $gradeInput);
                    foreach ($grades as $g) {
                        $grade = Grade::where('school_id', $datum['school_id'])
                            ->where('name', $g)->first();
                        if (!empty($grade)) {
                            $grade->educator_ids .= $educator_id . ',';
                            $grade->save();
                        }
                    }
                    # 班级数据 存在时更新班级主任数据
                    $classInput = str_replace(['，', '：'], [',', ':'], $datum['classes']);
                    $classes = explode(',', $classInput);
                    foreach ($classes as $c) {
                        $squadItem = explode(':', $c);
                        $gradeItem = Grade::where('school_id', $datum['school_id'])
                            ->where('name', $squadItem[0])->first();
                        if (!empty($gradeItem)) {
                            Log::debug($squadItem[0]);
                            $squads = Squad::where('name', $squadItem[1])
                                ->where('grade_id', $gradeItem->id)
                                ->first();
                            if (!empty($squads)) {
                                if ($squads->grade->school_id == $datum['school_id']) {
                                    $squads->educator_ids .= $educator_id . ',';
                                    $squads->save();
                                }
                            }
                        }
                    }
                    # 教职员工班级任课
                    if ($datum['subjects'] && $datum['educators_classes']) {
                        $subjectsInput = str_replace(['，', '：'], [',', ':'], $datum['subjects']);
                        # 科目数组
                        $subjects = explode(',', $subjectsInput);
                        $ecInput = str_replace(['，', '：'], [',', ':'], $datum['educators_classes']);
                        # 班级数组
                        $ecs = explode(',', $ecInput);
                        foreach ($subjects as $sub) {
                            foreach ($ecs as $ec) {
                                $subject = Subject::where('school_id', $datum['school_id'])
                                    ->where('name', $sub)->first();
                                $classItems = Squad::where('name', $ec)->get();
                                if (!empty($subject)) {
                                    
                                    $subjectGradeIds = explode('|', $subject->grade_ids);
                                    foreach ($classItems as $item) {
                                        # 班级的所属年级 是否在 科目对应的年级中
                                        if (in_array($item->grade_id, $subjectGradeIds)) {
                                            $educators_classes = [
                                                'educator_id' => $educator_id,
                                                'class_id'    => $item->id,
                                                'subject_id'  => $subject->id,
                                                'enabled'     => 1,
                                            ];
                                            EducatorClass::create($educators_classes);
                                        }
                                    }
                                }
                            }
                        }
                    }
                    unset($educator_id);
                }
                
            });
            $data['user'] = Auth::user();
            $data['type'] = 'educator';

            event(new eventTrigger($data));

            return is_null($exception) ? true : $exception;
        } catch (Exception $e) {
            return false;
        }
        
    }
}
