<?php
namespace App\Jobs;

use App\Events\ContactImportTrigger;
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
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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
    
    /**
     * @return bool
     * @throws Exception
     * @throws \Throwable
     */
    public function handle() {

        $rows = $this->data;
        try {
            DB::transaction(function () use ($rows) {
                foreach ($rows as $row) {
                    $m = Mobile::whereMobile($row['mobile'])->first();
                    # 手机号码不存在时 增加监护人用户 如果存在则更新
                    if (empty($m)) {
                        # 创建教职工用户
                        $user = User::create([
                            'username'   => uniqid('educator_'),
                            'group_id'   => Group::whereName('教职员工')->first()->id,
                            'password'   => bcrypt('12345678'),
                            'realname'   => $row['name'],
                            'gender'     => $row['gender'] == '男' ? '0' : '1',
                            'avatar_url' => '00001.jpg',
                            'userid'     => uniqid('educator_'),
                            'isleader'   => 0,
                            'wechatid'   => '',
                            'enabled'    => 1,
                        ]);
                        # 创建教职员工记录
                        $educator = Educator::create([
                            'user_id'   => $user['id'],
                            'school_id' => $row['school_id'],
                            'sms_quote' => 0,
                            'enabled'   => 1,
                        ]);
                        $educatorId = $educator['id'];
                        # 创建用户手机号码
                        Mobile::create([
                            'user_id'   => $user['id'],
                            'mobile'    => $row['mobile'],
                            'isdefault' => 1,
                            'enabled'   => 1,
                        ]);
                        # 创建部门成员
                        foreach ($row['departments'] as $d) {
                            $i = Department::whereName($d)->first();
                            DepartmentUser::create([
                                'department_id' => $i->id,
                                'user_id'       => $user['id'],
                                'enabled'       => 1,
                            ]);
                        }
                        # 创建企业号成员
                        $user->createWechatUser($user['id']);
                    } else {
                        # 手机号码存在时 更新user
                        $u = User::find($m->user_id);
                        if (!empty($u)) {
                            $u->realname = $row['name'];
                            $u->gender = $row['gender'] == '男' ? '0' : '1';
                            $u->save();
                        }
                        $u->updateWechatUser($m->user_id);
                        $e = Educator::whereUserId($m->user_id)->first();
                        # 教职工存在
                        if (!empty($e)) {
                            $e->school_id = $row['school_id'];
                            $e->save();
                            $educatorId = $e->id;
                        } else {
                            # 创建教职员工记录
                            $eduData = Educator::create([
                                'user_id'   => $m->user_id,
                                'school_id' => $row['school_id'],
                                'sms_quote' => 0,
                                'enabled'   => 1,
                            ]);
                            $educatorId = $eduData['id'];
                        }
                        unset($em);
                        # 更新部门成员
                        DepartmentUser::whereUserId($m->user_id)->delete();
                        foreach ($row['departments'] as $d) {
                            $i = Department::whereName($d)->first();
                            DepartmentUser::create([
                                'department_id' => $i->id,
                                'user_id'       => $m->user_id,
                                'enabled'       => 1,
                            ]);
                        }
                        # 更新企业号成员
                        $u->updateWechatUser($m->user_id);
                    }
                    # 年级数据 存在时更新年级主任数据
                    $gradeInput = str_replace(['，', '：'], [',', ':'], $row['grades']);
                    $grades = explode(',', $gradeInput);
                    foreach ($grades as $g) {
                        $grade = Grade::whereSchoolId($row['school_id'])
                            ->where('name', $g)->first();
                        if (!empty($grade)) {
                            $grade->educator_ids .= $educatorId . ',';
                            $grade->save();
                        }
                    }
                    # 班级数据 存在时更新班级主任数据
                    $classInput = str_replace(['，', '：'], [',', ':'], $row['classes']);
                    $classes = explode(',', $classInput);
                    foreach ($classes as $c) {
                        $squads = explode(':', $c);
                        $grade = Grade::whereSchoolId($row['school_id'])
                            ->where('name', $squads[0])->first();
                        if (!empty($grade)) {
                            $squad = Squad::whereName($squads[1])
                                ->where('grade_id', $grade->id)
                                ->first();
                            if (!empty($squad)) {
                                if ($squad->grade->school_id == $row['school_id']) {
                                    $squad->educator_ids .= $educatorId . ',';
                                    $squad->save();
                                }
                            }
                        }
                    }
                    # 教职员工班级任课
                    if ($row['subjects'] && $row['educators_classes']) {
                        $subjectsInput = str_replace(['，', '：'], [',', ':'], $row['subjects']);
                        # 科目数组
                        $subjects = explode(',', $subjectsInput);
                        $ecInput = str_replace(['，', '：'], [',', ':'], $row['educators_classes']);
                        # 班级数组
                        $ecs = explode(',', $ecInput);
                        foreach ($subjects as $sub) {
                            foreach ($ecs as $ec) {
                                $subject = Subject::whereSchoolId($row['school_id'])
                                    ->where('name', $sub)->first();
                                $classItems = Squad::whereName($ec)->get();
                                if (!empty($subject)) {
                                    $subjectGradeIds = explode('|', $subject->grade_ids);
                                    foreach ($classItems as $item) {
                                        # 班级的所属年级 是否在 科目对应的年级中
                                        if (in_array($item->grade_id, $subjectGradeIds)) {
                                            EducatorClass::create([
                                                'educator_id' => $educatorId,
                                                'class_id'    => $item->id,
                                                'subject_id'  => $subject->id,
                                                'enabled'     => 1,
                                            ]);
                                        }
                                    }
                                }
                            }
                        }
                    }
                    unset($educatorId);
                }
            });
            $data['user'] = Auth::user();
            $data['type'] = 'educator';
            event(new ContactImportTrigger($data));
        } catch (Exception $e) {
            throw $e;
        }
        
        return true;
        
    }
    
}
