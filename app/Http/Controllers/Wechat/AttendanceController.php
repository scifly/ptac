<?php
namespace App\Http\Controllers\Wechat;

use App\Http\Controllers\Controller;
use App\Models\Squad;
use App\Models\Student;
use App\Models\StudentAttendance;
use App\Models\User;
use Illuminate\Support\Facades\Request;

class AttendanceController extends Controller {

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|\think\response\View
     */
    public function index() {
        $userId = 'wangdongxi';
        # 当月第一天
        $beginTime=date('Y-m-01', strtotime(date("Y-m-d"))).' 00:00:00';
        # 当月最后一天
        $endTime = date('Y-m-d', strtotime("$beginTime +1 month -1 day"));
        $endTime = $endTime.' 23:59:59';
        $students = User::whereUserid($userId)->first()->custodian->students;
        $ids = $adays =$ndays=[];
        foreach ($students as $k => $s) {
            $data = $this->getDays($s->id,$beginTime,$endTime);
            $s->abnormal = count($data['adays']);
            $s->normal = count($data['ndays']);
            $s->schoolname = Squad::whereId($s->class_id)->first()->grade->school->name;
            $s->studentname = User::whereId($s->user_id)->first()->realname;
            $s->class_id = Squad::whereId($s->class_id)->first()->name;
        }

        return view('wechat.attendance_records.list', [
            'students' => $students,
        ]);
    }

    public function records($id = null)
    {
        if(Request::isMethod('post')){
            $date = Request::get('date');
            $intoAttendance = StudentAttendance::whereStudentId(Request::get('id'))
                ->whereDate('punch_time',$date)
                ->where('inorout',1)
                ->orderBy('punch_time','ASC')
                ->get();
            $outAttendance = StudentAttendance::whereStudentId(Request::get('id'))
                ->whereDate('punch_time',$date)
                ->where('inorout',0)
                ->orderBy('punch_time','desc')
                ->get();
            return response()->json(['time'=> $date, 'into'=> $intoAttendance,'out'=>$outAttendance]);
        }
        # 当月第一天
        $beginTime=date('Y-m-01', strtotime(date("Y-m-d"))).' 00:00:00';
        # 当月最后一天
        $endTime = date('Y-m-d', strtotime("$beginTime +1 month -1 day"));
        $endTime = $endTime.' 23:59:59';
        # 当天时间
        // $time = date('Y-m-d',time());
        $time = '2018-01-08';
        $into = $out = [];
        $into = StudentAttendance::whereDate('punch_time',$time)
            ->where('student_id',$id)
            ->where('inorout',1)
            ->orderBy('punch_time','ASC')
            ->get();
        $out = StudentAttendance::whereDate('punch_time',$time)
            ->where('student_id',$id)
            ->where('inorout',0)
            ->orderBy('punch_time','desc')
            ->get();
        $data = $this->getDays($id,$beginTime,$endTime);

        return view('wechat.attendance_records.attendance_records',[
            'id' => $id,
            'data' => $data,
            'days' => json_encode($data, JSON_UNESCAPED_UNICODE),
            'time' => $time,
            'into' => $into,
            'out' => $out
        ]);
    }

    /**
     * 获取考勤异常和正常的天数
     * @param $id
     * @param $beginTime
     * @param $endTime
     * @return array
     */
    public function getDays( $id, $beginTime, $endTime){
        $ids = $adays =$ndays = $data = [];
        # 查询考勤异常的数据
        $abnormal = StudentAttendance::whereStudentId($id)
            ->where('punch_time','>',$beginTime)
            ->where('punch_time','<',$endTime)
            ->where('status','0')
            ->get();
        foreach ($abnormal as $a){
            # 查询考勤异常当天的所有数据
            $sAttendance = StudentAttendance::whereDate('punch_time',substr($a->punch_time,0,10))
                ->where('punch_time','>',$beginTime)
                ->where('punch_time','<',$endTime)
                ->get();
            foreach ($sAttendance as $sa){
                $ids[]= $sa->id;
                $adays[] = substr($sa->punch_time,0,10);
            }
            $adays = array_unique($adays);
        }
        # 查询考勤正常天数的数据
        $normal = StudentAttendance::whereNotIn('id',$ids)
            ->where('punch_time','>',$beginTime)
            ->where('punch_time','<',$endTime)
            ->get();
        foreach ($normal as $n){
            $ndays[] = substr($n->punch_time,0,10);
        }
        $ndays = array_unique($ndays);

        $data = ['adays'=> $adays,'ndays' => $ndays ];
        return $data;
    }
}