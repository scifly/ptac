<?php
namespace App\Jobs;

use App\Models\Custodian;
use App\Models\CustodianStudent;
use App\Models\DepartmentUser;
use App\Models\Group;
use App\Models\Mobile;
use App\Models\Student;
use App\Models\User;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class ManageImportStudent implements ShouldQueue {
    
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
                    $relationship = str_replace(['，', '：'], [',', ':'], $row['relationship']);
                    $relationships = explode(',', $relationship);
                    $student = Student::whereStudentNumber($row['student_number'])->first();
                    if (!$student) {
                        # 创建用户
                        $u = User::create([
                            'username'   => uniqid('student_'),
                            'group_id'   => Group::whereName('学生')->first()->id,
                            'password'   => bcrypt('student8888'),
                            'realname'   => $row['name'],
                            'gender'     => $row['gender'] == '男' ? '0' : '1',
                            'avatar_url' => '00001.jpg',
                            'userid'     => uniqid('custodian_'),
                            'isleader'   => 0,
                            'wechatid'   => '',
                            'enabled'    => 1,
                        ]);
                        # 创建学生
                        $s = Student::create([
                            'user_id'        => $u['id'],
                            'class_id'       => $row['class_id'],
                            'student_number' => $row['student_number'],
                            'card_number'    => $row['card_number'],
                            'oncampus'       => $row['oncampus'] == '住读' ? '0' : '1',
                            'birthday'       => $row['birthday'],
                            'remark'         => $row['remark'],
                            'enabled'        => 1,
                        ]);
                        # 创建监护人关系
                        if (!empty($relationships)) {
                            foreach ($relationships as $r) {
                                $item = explode(':', $r);
                                if (count($item) == 4) {
                                    $m = Mobile::whereMobile($item[3])->first();
                                    # 手机号码不存在时 增加监护人用户 如果存在则更新
                                    if (empty($m)) {
                                        # 创建监护人用户
                                        $user = User::create([
                                            'username'   => uniqid('custodian_'),
                                            'group_id'   => Group::whereName('监护人')->first()->id,
                                            'password'   => bcrypt('custodian8888'),
                                            'realname'   => $item[1],
                                            'gender'     => $item[2] == '男' ? '0' : '1',
                                            'avatar_url' => '00001.jpg',
                                            'userid'     => uniqid('custodian_'),
                                            'isleader'   => 0,
                                            'wechatid'   => '',
                                            'enabled'    => 1,
                                        ]);
                                        # 创建监护人
                                        $c = Custodian::create(['user_id' => $user['id']]);
                                        # 创建 监护关系
                                        CustodianStudent::create([
                                            'custodian_id' => $c['id'],
                                            'student_id'   => $s['id'],
                                            'relationship' => $item[0],
                                            'enabled'      => 1,
                                        ]);
                                        # 创建监护人用户手机号码
                                        Mobile::create([
                                            'user_id'   => $user['id'],
                                            'mobile'    => $item[3],
                                            'isdefault' => 1,
                                            'enabled'   => 1,
                                        ]);
                                        # 创建部门成员
                                        DepartmentUser::create([
                                            'department_id' => $row['department_id'],
                                            'user_id'       => $user['id'],
                                            'enabled'       => 1,
                                        ]);
                                        # 创建企业号成员
                                        User::createWechatUser($user['id']);
                                    } else {
                                        # 手机号码存在时 更新user 再判断监护人是否存在 监护关系是否存在
                                        $user = User::find($m->user_id);
                                        if (!empty($user)) {
                                            $user->realname = $item[1];
                                            $user->gender = $item[2] == '男' ? '0' : '1';
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
                                                'relationship' => $item[0],
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
                                                    'relationship' => $item[0],
                                                    'enabled'      => 1,
                                                ]);
                                            }
                                        }
                                        # 更新部门成员
                                        DepartmentUser::whereUserId($m->user_id)->delete();
                                        DepartmentUser::create([
                                            'department_id' => $row['department_id'],
                                            'user_id'       => $m->user_id,
                                            'enabled'       => 1,
                                        ]);
                                        # 更新企业号监护人成员
                                        User::updateWechatUser($m->user_id);
                                    }
                                }
                            }
                            
                        }
                        # 创建学生用户手机号码
                        Mobile::create([
                            'user_id'   => $u['id'],
                            'mobile'    => $row['mobile'],
                            'isdefault' => 1,
                            'enabled'   => 1,
                        ]);
                        # 创建部门成员
                        DepartmentUser::create([
                            'department_id' => $row['department_id'],
                            'user_id'       => $u['id'],
                            'enabled'       => 1,
                        ]);
                        # 创建企业号成员
                        User::createWechatUser($u['id']);
                    }
                }
                
            });
        } catch (Exception $e) {
            throw $e;
        }
        
        return true;
        
    }
    
}
