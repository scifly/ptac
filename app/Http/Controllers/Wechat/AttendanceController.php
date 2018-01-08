<?php
namespace App\Http\Controllers\Wechat;

use App\Http\Controllers\Controller;
use App\Models\Squad;
use App\Models\Student;
use App\Models\StudentAttendance;
use App\Models\User;

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


        $normal = StudentAttendance::whereStudentId(1)
            ->where('punch_time','>',$beginTime)
            ->where('punch_time','<',$endTime)
            ->whereNotIn('punch_time',[])
            ->where('status','1')
            ->get();
        foreach ($students as $k => $s) {

            # 查询考勤异常的数据
            $abnormal = StudentAttendance::whereStudentId($s->id)
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
                    $adays = array_unique($adays);
                }
            }
            # 查询考勤正常天数的数据
            $normal = StudentAttendance::whereNotIn('id',$ids)
                ->where('punch_time','>',$beginTime)
                ->where('punch_time','<',$endTime)
                ->get();
            foreach ($normal as $n){
                $ndays[] = substr($n->punch_time,0,10);
                $ndays = array_unique($ndays);
            }
            $s->abnormal = count($adays);
            $s->normal = count($ndays);
            $s->schoolname = Squad::whereId($s->class_id)->first()->grade->school->name;
            $s->studentname = User::whereId($s->user_id)->first()->realname;
            $s->class_id = Squad::whereId($s->class_id)->first()->name;
        }

        return view('wechat.attendance_records.list', [
            'students' => $students,
        ]);
    }

    public function records($id)
    {


        // $student = Student::whereId($id)->first();
        return view('wechat.attendance_records.attendance_records');
    }

    public function getDays($id){

    }
}