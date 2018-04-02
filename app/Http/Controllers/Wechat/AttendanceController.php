<?php
namespace App\Http\Controllers\Wechat;

use App\Helpers\HttpStatusCode;
use App\Helpers\WechatTrait;
use App\Http\Controllers\Controller;
use App\Models\StudentAttendance;
use App\Models\StudentAttendanceSetting;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Request;
use Illuminate\View\View;
use Throwable;

/**
 * 微信考勤
 *
 * Class AttendanceController
 * @package App\Http\Controllers\Wechat
 */
class AttendanceController extends Controller {
    
    use WechatTrait;
    
    const APP = '考勤中心';
    const WEEK_DAYS = ['星期日', '星期一', '星期二', '星期三', '星期四', '星期五', '星期六'];
    protected $sa;
    
    function __construct(StudentAttendance $sa) {
        
        $this->sa = $sa;
        
    }
    
    /**
     * 考勤记录列表
     *
     * @return Factory|View|string
     */
    public function index() {
        
        $this->getUserid(self::APP);
        
        return $this->sa->wIndex();
        
    }
    
    /**
     * 返回指定学生的考勤记录
     *
     * @param null $studentId
     * @return Factory|JsonResponse|View
     */
    public function records($studentId = null) {
        
        return $this->sa->wRecords($studentId);
        
    }
    
    /**
     * 学生饼图数据
     *
     * @throws Throwable
     */
    public function chart() {
        
        return $this->sa->wChart();
        
    }
    
    /**
     * 返回指定班级对应的年级考勤规则
     *
     * @param $classId
     * @return \Illuminate\Http\JsonResponse
     */
    public function rules($classId) {
        
        return $this->sa->wRules($classId);
        
    }
    
    /**
     * 判断日期和规则是否匹配
     */
    public function validateRule() {
        
        $input = Request::all();
        if (isset($input['date'], $input['rule'])) {
            #获取规则的星期
            $ruleDay = StudentAttendanceSetting::find($input['rule'])->day;
            $weekDay = self::WEEK_DAYS[date("w", strtotime($input['date']))];
            $this->result['message'] = '';
            
            return $ruleDay == $weekDay
                ? response()->json($this->result)
                : abort(
                    HttpStatusCode::INTERNAL_SERVER_ERROR,
                    '请选择和规则对应的星期！'
                );
        }
        $this->result['message'] = '';
        
        return response()->json($this->result);
        
    }
    
}