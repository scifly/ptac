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
    
    /**
     * EducatorAttendanceController constructor.
     * @param EducatorAttendance $ea
     */
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
        
        return Request::get('draw')
            ? response()->json($this->ea->index())
            : $this->output();
        
    }
    
    /**
     * 教职员工考勤统计
     *
     * @throws Throwable
     */
    public function stat() {
        
        return Request::method() === 'POST'
            ? $this->ea->stat()
            : $this->output();
        
    }
    
    /**
     * 查询教职员工考勤明细
     *
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function detail() {
        
        return Request::method() === 'POST'
            ? response()->json($this->ea->detail())
            : $this->output();
        
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
