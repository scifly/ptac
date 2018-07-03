<?php
namespace App\Jobs;

use Exception;
use App\Models\App;
use App\Models\User;
use App\Models\Media;
use App\Models\Mobile;
use App\Models\Message;
use App\Facades\Wechat;
use App\Models\Student;
use App\Models\Semester;
use App\Models\CommType;
use App\Models\MessageType;
use Illuminate\Bus\Queueable;
use App\Models\MessageSendingLog;
use App\Models\AttendanceMachine;
use App\Models\StudentAttendance;
use Illuminate\Support\Facades\DB;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use App\Models\StudentAttendanceSetting;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

/**
 * Class ManageStudentAttendance
 * @package App\Jobs
 */
class ManageStudentAttendance implements ShouldQueue {
    
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    
    protected $data, $message;
    
    /**
     * Create a new job instance.
     *
     * @param $data
     */
    public function __construct($data) {
        $this->data = $data;
    }
    
    /**
     * @throws Exception
     * @throws \Throwable
     */
    public function handle() {
        $input = $this->data;
        try {
            DB::transaction(function () use ($input) {
                #处理计算逻辑
                $student = Student::where('card_number', $input['card_number'])->first();
                $squad = $student->squad;
                $grade = $squad->grade;
                $school = $grade->school;
                $weekArray = ['星期日', '星期一', '星期二', '星期三', '星期四', '星期五', '星期六'];
                //将时间转化成时间戳 获得星期 日期 时间
                $time = strtotime($input['punch_time']);
                $weekDay = $weekArray[date("w", $time)];
                $punch_time = date("H:i:s", $time);
                $date_time = date("Y-m-d", $time);
                $schoolSemesters = Semester::where('school_id', $school->id)->get();
                $status = 0;
                //找出对应的学期 根据打卡时间
                foreach ($schoolSemesters as $se) {
                    if ($se->start_date <= $date_time && $se->end_date >= $date_time) {
                        $semester = $se->id;
                    }
                }
                if (!isset($semester)) {
                    #没有找到打卡对应的学期
                    $semester = '';
                }
                //找出对应的考勤机id
                $attendance = AttendanceMachine::whereMachineid($input['attendId'])
                    ->where('school_id', $school->id)->first();
                //根据时间找出对应的 规则
                $rules = StudentAttendanceSetting::where('grade_id', $grade->id)
                    ->where('semester_id', $semester)
                    ->where('day', $weekDay)
                    ->get();
                
                //规则为空时，失败
                foreach ($rules as $rule) {
                    if ($rule->start <= $punch_time && $rule->end >= $punch_time) {
                        $sasId = $rule->id;
                        //对比进出状态判断是否匹配
                        if($input['inorout'] == $rule->inorout){
                            $status = 1;
                        } else {
                            $status = 0;
                        }
                    }
                }
                //如果没有满足一个规则，异常，且状态写为空
                if (!isset($sasId)) {
                    $sasId = null;
                }
                #存储到数据表
                // 先处理照片 $input['img'] 为照片路径
                if (isset($input['img'])) {
                    $m = Media::create([
                        'path'          => $input['img'],
                        'remark'        => '考勤照片',
                        'media_type_id' => 1,
                        'enabled'       => 1,
                    ]);
                    $input['media_id'] = $m->id;
                } else {
                    $input['media_id'] = 0;
                }
                $studentAttendance = StudentAttendance::create([
                    'student_id'            => $student->id,
                    'sas_id'                => $sasId,
                    'punch_time'            => $input['punch_time'],
                    'inorout'               => $input['inorout'],
                    'attendance_machine_id' => $attendance->id,
                    'media_id'              => $input['media_id'],
                    'status'                => $status,
                    'longitude'             => $input['longitude'],
                    'latitude'              => $input['latitude'],
                ]);
                #推送信息
                //根据学生信息获取监护人信息 多个
                $userId = [];
                $custodians = $student->custodians;
                foreach ($custodians as $custodian) {
                    $userId[] = $custodian->user->id;
                }
                if($sasId != null) {
                    $msgTemplate = $studentAttendance->studentAttendancesetting->msg_template;
                    // $msg = '尊敬的{name}家长, 你的孩子于{time}在校打卡, 打卡规则：{rule}, 状态：{status}';
                    $repl = [
                        '{name}' => $student->user->realname,
                        '{time}' => $studentAttendance->punch_time,
                        '{rule}' => $studentAttendance->studentAttendancesetting->name,
                        '{status}' => $studentAttendance->status == 1 ? '正常' : '异常',
                    ];
                    $msg = strtr($msgTemplate, $repl);
                } else {
                    $msg = '尊敬的' . $student->user->realname . '家长,你的孩子于' . $studentAttendance->punch_time . '在校打卡,未在规定时间打卡';
                }
                //在本地创建消息记录
                $messageSendingLog = new MessageSendingLog();
                //新增一条日志记录（指定批次）
                $sendLogData = [
                    'read_count'      => 0,
                    'received_count'  => 0,
                    'recipient_count' => count($userId),
                ];
                $mslId = $messageSendingLog->create($sendLogData)->id;
                //新增本地消息记录
                $this->message = new Message();
                $mobiles = [];
                foreach ($userId as $u) {
                    $messageData = [
                        'title'           => $date_time . '-考勤信息',
                        'comm_type_id'    => CommType::whereName('应用')->first()->id,
                        'app_id'          => App::whereName('考勤中心')->first()->id,
                        'msl_id'          => $mslId,
                        'content'         => $msg,
                        'serviceid'       => 0,
                        'message_id'      => 0,
                        'url'             => '0',
                        'media_ids'       => '0',
                        's_user_id'       => 1,
                        'r_user_id'       => $u,
                        'message_type_id' => MessageType::whereName('考勤消息')->first()->id,
                        'read'          => 0,
                        'sent'            => 1,
                    ];
                    $this->message->create($messageData);
                    $m = Mobile::whereUserId($u)->where('enabled', 1)->first();
                    $mobiles[] = $m->mobile;
                }
                //推送应用信息 失败将推送短信
                if (!$this->pushMessage($userId, $msg)) {
                // $autograph = $school->signature;
                $autograph = '【成都外国语】';
                Wechat::batchSend('LKJK004923', "654321@", implode(',', $mobiles), urlencode($msg) . $autograph);
                };
            });
        } catch (Exception $e) {
            throw $e;
        }
        
        return true;
    }
    
    /**
     * @param $userId
     * @param $msg
     * @return bool
     * @throws Exception
     */
    private function pushMessage($userId, $msg) {
        
        #应用发消息
        $corpId = 'wxe75227cead6b8aec';
        $secret = 'qv_kkW2S3zmMWIUrV3u2nydcyIoLknTvuDMq7ja4TYE';
        $token = Wechat::getAccessToken($corpId, $secret);
        $agentid = 3;
        $users = [];
        foreach ($userId as $u_id) {
            $users[] = User::find($u_id)->userid;
        }
        $topuser = implode('|', $users);
        $message = [
            'touser'  => $topuser,
            'agentid' => $agentid,
            'msgtype' => 'text',
            'text'    => ['content' => $msg],
        ];
        $res = json_decode(Wechat::sendMessage($token, $message));
        
        return $res->errcode == 0 ? true : false;
        
    }
    
}
