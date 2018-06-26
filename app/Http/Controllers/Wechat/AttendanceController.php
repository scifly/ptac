<?php
namespace App\Http\Controllers\Wechat;

use App\Http\Controllers\Controller;
use App\Models\StudentAttendance;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Throwable;

/**
 * 微信考勤
 *
 * Class AttendanceController
 * @package App\Http\Controllers\Wechat
 */
class AttendanceController extends Controller {
    
    protected $sa;
    
    function __construct(StudentAttendance $sa) {
        
        $this->middleware('wechat');
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
     * 返回指定学生的考勤记录
     *
     * @param null $studentId
     * @return Factory|JsonResponse|View
     */
    public function detail($studentId = null) {
        
        return $this->sa->wDetail($studentId);
        
    }
    
    /**
     * 学生饼图数据
     *
     * @throws Throwable
     */
    public function chart() {
        
        return $this->sa->wChart();
        
    }
    
}