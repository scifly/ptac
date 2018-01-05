<?php
namespace App\Http\Controllers\Wechat;

use App\Http\Controllers\Controller;
use App\Models\Squad;
use App\Models\Student;
use App\Models\User;

class AttendanceController extends Controller {

    public function index() {
        $userId = 'wangdongxi';
        $students = User::whereUserid($userId)->first()->custodian->students;
        foreach ($students as $k => $s) {
            $s->schoolname = Squad::whereId($s->class_id)->first()->grade->school->name;
            $s->user_id = User::whereId($s->user_id)->first()->realname;
            $s->class_id = Squad::whereId($s->class_id)->first()->name;
        }

        return view('wechat.attendance_records.list', [
            'students' => $students,
        ]);
        // $time = '2018-01-03';
        // $into = StudentAttendance::whereInorout(1)->where('punch_time', 'like', $time . '%')->get();

        //
    }

    public function records($id)
    {
        $student = Student::whereId($id)->first();
        return view('wechat.attendance_records.attendance_records');
    }
}