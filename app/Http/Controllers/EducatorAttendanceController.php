<?php
namespace App\Http\Controllers;

use App\Models\EducatorAttendance;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Request;
use Throwable;

/**
 * 教职员工考勤
 *
 * Class EducatorAttendanceController
 * @package App\Http\Controllers
 */
class EducatorAttendanceController extends Controller {
    
    protected $ea;
    
    function __construct(EducatorAttendance $ea) {
        
        $this->middleware(['auth', 'checkrole']);
        $this->ea = $ea;
        $this->approve($ea);
        
    }
    
    /**
     * 教职员工考勤记录列表
     *
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function index() {
        
        if (Request::get('draw')) {
            return response()->json(
                $this->ea->index()
            );
        }
        
        return $this->output();
        
    }
    
    /**
     * 教职员工考勤统计
     *
     * @throws Throwable
     */
    public function stat() {
        
        if (Request::method() === 'POST') {
            return $this->ea->stat();
        }
        
        return $this->output();
        
    }
    
    /**
     * 查询教职员工考勤明细
     *
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function detail() {
        
        if (Request::method() === 'POST') {
            return response()->json(
                $this->ea->detail()
            );
        }
        
        return $this->output();
        
    }
    
    /**
     * 导出教职员工考勤明细
     *
     * @return mixed
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    public function export() {
        
        return $this->ea->export();
        
    }
    
}
