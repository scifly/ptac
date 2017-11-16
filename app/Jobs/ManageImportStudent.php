<?php
namespace App\Jobs;

use App\Models\Custodian;
use App\Models\CustodianStudent;
use App\Models\Group;
use App\Models\Mobile;
use App\Models\Student;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Mockery\Exception;

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
    
    public function handle() {
        Log::debug('import');
        $rows = $this->data;
        try {
            
            $exception = DB::transaction(function () use ($rows) {
                foreach ($rows as $datum) {
                    $relationship = str_replace(['，', '：'], [',', ':'], $datum['relationship']);
                    $relationships = explode(',', $relationship);
                    $s = new Student();
                    $studentData = $s->where('student_number', $datum['student_number'])->first();
                    if (empty($studentData)) {
                        # 创建用户
                        $user = [
                            'username'   => uniqid('custodian_'),
                            'group_id'   => Group::whereName('学生')->first()->id,
                            'password'   => bcrypt('custodian8888'),
                            'realname'   => $datum['name'],
                            'gender'     => $datum['gender'] == '男' ? '0' : '1',
                            'avatar_url' => '00001.jpg',
                            'userid'     => uniqid('custodian_'),
                            'isleader'   => 0,
                            'wechatid'   => '',
                            'enabled'    => 1,
                        ];
                        $u = User::create($user);
                        # 创建学生
                        $student = [
                            'user_id'        => $u['id'],
                            'class_id'       => $datum['class_id'],
                            'student_number' => $datum['student_number'],
                            'card_number'    => $datum['card_number'],
                            'oncampus'       => $datum['oncampus'] == '住读' ? '0' : '1',
                            'birthday'       => $datum['birthday'],
                            'remark'         => $datum['remark'],
                            'enabled'        => 1,
                        ];
                        $s = Student::create($student);
                        // print_r(count($relationships));die;
                        # 创建监护人关系
                        if (!empty($relationships)) {
                            foreach ($relationships as $r) {
                                $item = explode(':', $r);
                                if (count($item) == 4) {
                                    $m = Mobile::where('mobile', $item[3])->first();
                                    # 手机号码不存在时 增加监护人用户 如果存在则更新
                                    if (empty($m)) {
                                        # 创建监护人用户
                                        $user = [
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
                                        ];
                                        // $u = new User();
                                        $userData = User::create($user);
                                        # 创建监护人
                                        $custodian = [
                                            'user_id' => $userData['id'],
                                        ];
                                        // $c = new Custodian();
                                        $c = Custodian::create($custodian);
                                        # 创建 监护关系
                                        $custodianStudent = [
                                            'custodian_id' => $c['id'],
                                            'student_id'   => $s['id'],
                                            'relationship' => $item[0],
                                            'enabled'      => 1,
                                        ];
                                        // $cs = new CustodianStudent();
                                        CustodianStudent::create($custodianStudent);
                                        # 创建监护人用户手机号码
                                        $mobile = [
                                            'user_id'   => $userData['id'],
                                            'mobile'    => $item[3],
                                            'isdefault' => 1,
                                            'enabled'   => 1,
                                        ];
                                        Mobile::create($mobile);
                                        $userModel = new User();
                                        $userModel->createWechatUser($userData['id']);
                                        unset($userModel);
        
                                    }else{
                                        # 手机号码存在时 更新user 再判断监护人是否存在 监护关系是否存在
                                        $user = User::whereId($m->user_id)->first();
                                        if (!empty($user)) {
                                            $user->realname = $item[1];
                                            $user->gender = $item[2] == '男' ? '0' : '1';
                                            $user->save();
                                        }
                                        $c = Custodian::where('user_id', $m->user_id)->first();
                                        # 监护人不存在时
                                        if (empty($c)) {
                                            # 创建监护人
                                            $custodian = [
                                                'user_id' => $m->user_id,
                                            ];
                                            $custodianData = Custodian::create($custodian);
                                            
                                            # 创建 监护关系
                                            $custodianStudent = [
                                                'custodian_id' => $custodianData['id'],
                                                'student_id'   => $s['id'],
                                                'relationship' => $item[0],
                                                'enabled'      => 1,
                                            ];
                                            CustodianStudent::create($custodianStudent);
                                        }else{
                                            # 监护人存在 监护关系不存在时
                                            $csData = CustodianStudent::where('custodian_id',$c['id'])->where('student_id', $s['id'])->first();
                                            if (empty($csData)) {
                                                # 创建 监护关系
                                                $custodianStudent = [
                                                    'custodian_id' => $c['id'],
                                                    'student_id'   => $s['id'],
                                                    'relationship' => $item[0],
                                                    'enabled'      => 1,
                                                ];
                                                CustodianStudent::create($custodianStudent);
                                            }
                                        }
                                        $userModel = new User();
                                        $userModel->updateWechatUser($m->user_id);
                                        unset($userModel);
                                    }
                                }
                            }
                            
                        }
                        # 创建学生用户手机号码
                        $mobile = [
                            'user_id'   => $u['id'],
                            'mobile'    => $datum['mobile'],
                            'isdefault' => 1,
                            'enabled'   => 1,
                        ];
                        Mobile::create($mobile);
                        Log::debug($u['id']);
                        $user = new User();
                        $user->createWechatUser($u['id']);
                        unset($user);
                    }
                }
    
            });
            return is_null($exception) ? true : $exception;
        } catch (Exception $e) {
            return false;
        }
        
    }
}
