<?php
namespace App\Http\Controllers\Wechat;

use App\Helpers\ControllerTrait;
use App\Helpers\ModelTrait;
use App\Http\Controllers\Controller;

class AttendanceController extends Controller{

    use ModelTrait;

    public function index(){
        
        return '测试';
        
    }

}