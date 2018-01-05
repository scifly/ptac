<?php
namespace App\Http\Controllers\Wechat;

use App\Helpers\ControllerTrait;
use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Squad;
use App\Models\StudentAttendance;
use App\Models\User;

class AttendanceController extends Controller{
    use ControllerTrait;

    public function index(){
        $userId = 'wangdongxi';
        $students = User::whereUserid($userId)->first()->custodian->students;

        foreach ($students  as $k=>$s){
            $s->schoolname =Squad::whereId($s->class_id)->first()->grade->school->name;
            $s->user_id = User::whereId($s->user_id)->first()->realname;
            $s->class_id = Squad::whereId($s->class_id)->first()->name;
        }

        return view('wechat.attendance_records.list',[
            'students' => $students
        ]);
        // $time = '2018-01-03';
        // $into = StudentAttendance::whereInorout(1)->where('punch_time', 'like', $time . '%')->get();
        // // echo '<pre>';
        // // print_r($into);exit;

        // return view('wechat.attendance_records.attendance_records');
    }





}