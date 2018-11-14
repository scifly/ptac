<?php
namespace App\Http\Controllers\Wechat;

use App\Http\Controllers\Controller;
use App\Models\StudentAttendance;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Throwable;

/**
 * 考勤管理
 *
 * Class AttendanceController
 * @package App\Http\Controllers\Wechat
 */
class AttendanceController extends Controller {
    
    static $category = 1; # 微信端控制器
    
    protected $sa;
    
    /**
     * AttendanceController constructor.
     * @param StudentAttendance $sa
     */
    function __construct(StudentAttendance $sa) {
        
        $this->middleware(['corp.auth', 'corp.role']);
        $this->sa = $sa;
        
    }
    
    /**
     * 考勤记录列表
     *
     * @return Factory|View|string
     */
    public function index() {
        
        return $this->sa->wIndex();
        
    }
    
    /**
     * 获取指定学生的考勤记录
     *
     * @param null $studentId
     * @return Factory|JsonResponse|View
     */
    public function detail($studentId = null) {
        
        return $this->sa->wDetail($studentId);
        
    }
    
    /**
     * 获取学生饼图数据
     *
     * @throws Throwable
     */
    public function chart() {
        
        return $this->sa->wChart();
        
    }
    
}