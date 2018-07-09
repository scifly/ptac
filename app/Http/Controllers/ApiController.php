<?php
namespace App\Http\Controllers;

use App\Helpers\HttpStatusCode;
use App\Http\Requests\ConsumptionRequest;
use App\Http\Requests\StudentAttendanceRequest;
use App\Jobs\SendMessageApi;
use App\Models\Consumption;
use App\Models\EducatorAttendance;
use App\Models\Message;
use App\Models\StudentAttendance;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

/**
 * Class ApiController
 * @package App\Http\Controllers
 */
class ApiController extends Controller {
    
    protected $consumption, $sa, $ea, $message;
    
    /**
     * ApiController constructor.
     * @param Consumption $consumption
     * @param StudentAttendance $sa
     * @param EducatorAttendance $ea
     * @param Message $message
     */
    function __construct(
        Consumption $consumption,
        StudentAttendance $sa,
        EducatorAttendance $ea,
        Message $message
    ) {
        
        $this->consumption = $consumption;
        $this->sa = $sa;
        $this->ea = $ea;
        $this->message = $message;
        
    }
    
    /**
     * 登录
     *
     * @return JsonResponse
     */
    public function login() {
        
        if (Auth::id() || Auth::attempt([
                'username' => request('username'),
                'password' => request('password'),
            ])) {
            $user = Auth::user();
            $this->result['token'] = $user->createToken('ptac')->accessToken;
            $statusCode = HttpStatusCode::OK;
        } else {
            $this->result['message'] = __('messages.forbidden');
            $this->result['statusCode'] = $statusCode = HttpStatusCode::UNAUTHORIZED;
        }
        
        return response()->json(
            $this->result,
            $statusCode
        );
        
    }
    
    /**
     * 消费记录
     *
     * @param ConsumptionRequest $request
     * @return JsonResponse|string
     */
    public function studentConsumption(ConsumptionRequest $request) {

        return $this->consumption->store(
            $request->all()
        );
        
    }
    
    /**
     * 学生考勤记录
     *
     * @param StudentAttendanceRequest $request
     * @return JsonResponse
     */
    public function studentAttendance(StudentAttendanceRequest $request) {

        return $this->sa->store(
            $request->all()
        );
        
    }
    
    /**
     * 教职员工考勤记录
     *
     * @param EducatorAttendance $request
     * @return JsonResponse|string
     */
    public function educatorAttendance(EducatorAttendance $request) {
        
        return $this->ea->store(
            $request->all()
        );
        
    }
    
    /**
     * 发送消息（短信或者微信）
     *
     * @return string
     * @throws Exception
     */
    public function sendMsg() {
    
        SendMessageApi::dispatch(
            Request::input('mobiles'),
            Request::input('school_id'),
            Request::input('content')
        );
        
        return response()->json([
            'statusCode' => HttpStatusCode::OK,
            'message' => '已成功提交消息发送请求'
        ]);
    
    }
    
}
