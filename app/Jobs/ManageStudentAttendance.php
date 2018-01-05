<?php
namespace App\Jobs;

use App\Facades\Wechat;
use App\Models\AttendanceMachine;
use App\Models\Media;
use App\Models\Message;
use App\Models\Semester;
use App\Models\Student;
use App\Models\StudentAttendance;
use App\Models\StudentAttendanceSetting;
use App\Models\User;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

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
        print_r($this->data);
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
                $semester = '';
                $timeDiff = [];
                //找出对应的学期 根据打卡时间
                foreach ($schoolSemesters as $se) {
                    if ($se->start_date <= $date_time && $se->end_date >= $date_time) {
                        $semester = $se->id;
                    }
                }
                //找出对应的考勤机id
                $attendance = AttendanceMachine::whereMachineid($input['attendId'])
                    ->where('school_id', $school->id)->first();
                //根据时间找出对应的 规则
                $rules = StudentAttendanceSetting::where('grade_id', $grade->id)
                    ->where('semester_id', $semester)
                    ->where('day', $weekDay)
                    ->where('inorout', $input['inorout'])
                    ->get();
                foreach ($rules as $rule) {
                    if ($rule->start <= $punch_time && $rule->end >= $punch_time) {
                        $sasId = $rule->id;
                        $status = 1;
                    } else {
                        $timeDiff[$rule->id] = $time - strtotime($rule->start);
                    }
                }
                //如果没有满足一个规则，异常，判断打卡时间距离那一个时间段最近
                if (!isset($sasId)) {
                    $status = 0;
                    $sasId = array_search(min($timeDiff), $timeDiff);
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
                    $input['media_id'] = '';
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
                $msgTemplate = $studentAttendance->studentAttendancesetting->msg_template;
                // $msg = '尊敬的XX家长, 你的孩子于XX在校打卡, 打卡状态：XX';
                $msg = str_replace_array('XX', [$student->user->realname, $studentAttendance->punch_time, $studentAttendance->status == 1 ? '正常' : '异常'], $msgTemplate);
                //在本地创建消息记录
               
               
                //推送应用信息 失败将推送短信
                if (!$this->pushMessage($userId, $msg)) {
                    $this->message = new Message();
                    $this->message->sendSms($userId, [], $msg);
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
