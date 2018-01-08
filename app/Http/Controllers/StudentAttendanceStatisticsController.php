<?php
namespace App\Http\Controllers;

use App\Http\Requests\StudentAttendanceRequest;
use App\Models\AttendanceMachine;
use App\Models\Media;
use App\Models\School;
use App\Models\Student;
use App\Models\StudentAttendance;
use Illuminate\Support\Facades\Request;

/**
 * 考勤记录统计
 *
 * Class StudentAttendanceController
 * @package App\Http\Controllers
 */
class StudentAttendanceStatisticsController extends Controller {
    
    protected $studentAttendance, $student, $media;
    
    function __construct(StudentAttendance $studentAttendance, Student $student, Media $media) {
        
        // $this->middleware(['auth']);
        $this->studentAttendance = $studentAttendance;
        $this->student = $student;
        $this->media = $media;
        
    }
    
    /**
     * 考勤统计
     *
     * @return bool|\Illuminate\Http\JsonResponse
     * @throws \Throwable
     */
    public function index() {
        

        
        return $this->output();
        
    }

}
