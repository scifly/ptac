<?php
namespace App\Http\Controllers\Wechat;

use App\Http\Controllers\Controller;
use App\Http\Requests\ScoreRequest;
use App\Models\Exam;
use App\Models\Score;
use App\Models\Squad;
use App\Models\Student;
use App\Models\Subject;
use Excel;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Request;
use Maatwebsite\Excel\Classes\PHPExcel;
use Maatwebsite\Excel\Files\ExcelFile;
use Throwable;

/**
 * 微信端成绩
 *
 * Class ScoreController
 * @package App\Http\Controllers
 */
class ScoreController extends Controller {


    protected $score, $exam;

    /**
     * MessageCenterController constructor.
     * @param Score $score
     * @param Exam $exam
     */
    public function __construct(Score $score, Exam $exam) {
        // $this->middleware();
        $this->score = $score;
        $this->exam = $exam;

    }
    /**
     * 成绩详情
     *
     * @param $id
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function detail() {

        $classId = Request::input('class_id');
        $examId = Request::input('exam_id');
        $classId = 1;
        $examId = 1;
        if ($classId && $examId) {
            $data = $this->score->getExamClass($examId, $classId);
//            return response()->json($this->score->getExamClass($examId, $classId));
            return view('wechat.score.detail', [
                'data' => $data,
            ]);

        }

    }

    /**
     * 图表成绩详情
     *
     * @return bool|JsonResponse
     */
    public function show() {

        $studentId = Request::input('student');
        $examId = Request::input('exam');

        $subjectId = Request::input('subject');
        if (Request::method() == 'POST') {
            if ($examId && $subjectId) {
                return response()->json($this->score->getGraphData($studentId, $examId, $subjectId));
            }

        }
        $exam = Exam::whereId($examId)->first();
        $student = Student::whereId($studentId)->first();
        $data = Subject::whereIn('id', explode(',', $exam->subject_ids))->get();
        return view('wechat.score.show', [
            'data' => $data,
            'student' => $student,
            'exam' => $exam,
        ]);

    }
    
}

