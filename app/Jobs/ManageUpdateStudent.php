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

class ManageUpdateStudent implements ShouldQueue {
    
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
        Log::debug('update');
    
        $rows = $this->data;
        try {
            
            $exception = DB::transaction(function () use ($rows) {
                foreach ($rows as $datum) {
                    $relationship = str_replace(['，', '：'], [',', ':'], $datum['relationship']);
                    $relationships = explode(',', $relationship);
                    $u = new User();
                    $s = new Student();
                    $studentData = $s->where('student_number', $datum['student_number'])->first();
                    $studentData->class_id = $datum['class_id'];
                    $studentData->card_number = $datum['card_number'];
                    $studentData->oncampus = $datum['card_number'];
                    $studentData->birthday = $datum['birthday'];
                    $studentData->remark = $datum['remark'];
                    $studentData->save();
                    $studentUser = [
                        'realname' => $datum['name'],
                        'gender'   => $datum['gender'] == '男' ? '0' : '1',
                    ];
                    $u->where('id', $studentData->user_id)->update($studentUser);
                    Mobile::where('user_id', $studentData->user_id)->update(['isdefault' => 0, 'enabled' => 0]);
                    $mobile = [
                        'user_id'   => $studentData->user_id,
                        'mobile'    => $datum['mobile'],
                        'isdefault' => 1,
                        'enabled'   => 1,
                    ];
                    Mobile::create($mobile);
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
                                        'student_id'   => $studentData->id,
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
                                } else {
                                    # 手机号码存在 反查用户表
                                    $user = User::whereId($m->user_id)->first();
                                    # 用户存在时更新数据
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
                                            'student_id'   => $studentData->id,
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
                                                'custodian_id' => $csData->id,
                                                'student_id'   => $studentData->id,
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
                    $userModel = new User();
                    $userModel->updateWechatUser($studentData->user_id);
                    unset($userModel);
                }
                
            });
            return is_null($exception) ? true : $exception;
        } catch (Exception $e) {
            return false;
        }
        
    }
}
