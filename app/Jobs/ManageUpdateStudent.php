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
        Log::debug('123');
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
                                    $u = User::create($user);
                                    # 创建监护人
                                    $custodian = [
                                        'user_id' => $u['id'],
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
                                    $cs = CustodianStudent::create($custodianStudent);
                                    # 创建监护人用户手机号码
                                    $mobile = [
                                        'user_id'   => $u['id'],
                                        'mobile'    => $item[3],
                                        'isdefault' => 1,
                                        'enabled'   => 1,
                                    ];
                                    $m = Mobile::create($mobile);
                                    $user = new User();
                                    $user->createWechatUser($u['id']);
                                    unset($user);
                                } else {
                                    $user = User::whereId($m->user_id)->first();
                                    if (!empty($user)) {
                                        $user->realname = $item[1];
                                        $user->gender = $item[2] == '男' ? '0' : '1';
                                        $user->save();
                                    }
                                }
                            }
                            
                        }
                    }
                    
                }
                
            });
            return is_null($exception) ? true : $exception;
        } catch (Exception $e) {
            return false;
        }
        
    }
}
