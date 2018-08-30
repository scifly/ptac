<?php
namespace App\Http\Controllers;

use App\Helpers\HttpStatusCode;
use App\Jobs\SendMessageApi;
use App\Models\Consumption;
use App\Models\EducatorAttendance;
use App\Models\Group;
use App\Models\Message;
use App\Models\StudentAttendance;
use App\Models\User;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Throwable;

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
    
        $username = request('username');
        $credential = [
            'username' => $username,
            'password' => request('password')
        ];
        $apiUser = User::where([
            'username' => $username,
            'group_id' => Group::whereName('api')->first()->id
        ])->first();
        if ($apiUser && (Auth::id() || Auth::attempt($credential))) {
            $user = Auth::user();
            $this->result['token'] = $user->createToken('ptac')->accessToken;
        } else {
            $this->result['message'] = __('messages.forbidden');
            $this->result['statusCode'] = HttpStatusCode::UNAUTHORIZED;
        }
        
        return response()->json(
            $this->result,
            $this->result['statusCode']
        );
        
    }
    
    /**
     * 消费记录
     *
     * @return JsonResponse|string
     * @throws Throwable
     */
    public function studentConsumption() {

        return $this->consumption->store();
        
    }
    
    /**
     * 学生考勤记录
     *
     * @return JsonResponse
     * @throws Throwable
     */
    public function studentAttendance() {

        return $this->sa->store();
        
    }
    
    /**
     * 教职员工考勤记录
     *
     * @return JsonResponse|string
     * @throws Throwable
     */
    public function educatorAttendance() {
        
        return $this->ea->store();
        
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
