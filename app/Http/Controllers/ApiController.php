<?php
namespace App\Http\Controllers;

use App\Helpers\HttpStatusCode;
use App\Http\Requests\ConsumptionRequest;
use App\Http\Requests\StudentAttendanceRequest;
use App\Models\Consumption;
use App\Models\EducatorAttendance;
use App\Models\StudentAttendance;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Throwable;

/**
 * Class ApiController
 * @package App\Http\Controllers
 */
class ApiController extends Controller {
    
    protected $consumption, $sa, $ea;
    
    /**
     * ApiController constructor.
     * @param Consumption $consumption
     * @param StudentAttendance $sa
     * @param EducatorAttendance $ea
     */
    function __construct(
        Consumption $consumption,
        StudentAttendance $sa,
        EducatorAttendance $ea
    ) {
        
        $this->consumption = $consumption;
        $this->sa = $sa;
        $this->ea = $ea;
        
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
        
        return $this->consumption->store($request->all())
            ? response()->json()
            : response()->json('error', 400);
        
    }
    
    /**
     * 学生考勤记录
     *
     * @param StudentAttendanceRequest $request
     * @return JsonResponse
     * @throws Throwable
     */
    public function studentAttendance(StudentAttendanceRequest $request) {

        return $this->sa->store($request->all())
            ? response()->json()
            : response()->json('error', 400);
        
    }
    
    /**
     * 教职员工考勤记录
     *
     * @param EducatorAttendance $request
     * @return JsonResponse|string
     */
    public function educatorAttendance(EducatorAttendance $request) {
        
        return $this->result(
            $this->ea->store($request->all())
        );
        
    }
    
}
