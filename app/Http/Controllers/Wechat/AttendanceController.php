<?php
namespace App\Http\Controllers\Wechat;

use App\Helpers\ControllerTrait;
use App\Http\Controllers\Controller;

class AttendanceController extends Controller{

    use ControllerTrait;

    public function index(){

        return view('wechat.attendance_records.attendance_records');
    }

}