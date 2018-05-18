<?php
namespace App\Http\Controllers\Wechat;

use App\Helpers\WechatTrait;
use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\Score;
use App\Models\Squad;
use App\Models\Student;
use App\Models\Subject;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Illuminate\View\View;
use Throwable;

/**
 * 微信端成绩
 *
 * Class ScoreController
 * @package App\Http\Controllers
 */
class ScoreCenterController extends Controller {
    
    use WechatTrait;
    
    protected $score, $exam;
    
    const APP = '成绩中心';
    
    /**
     * MessageCenterController constructor.
     * @param Score $score
     * @param Exam $exam
     */
    public function __construct(Score $score, Exam $exam) {

        $this->score = $score;
        $this->exam = $exam;
        
    }
    
    /**
     * 微信端成绩
     *
     * @return string
     * @throws Throwable
     */
    public function index() {
        
        return Auth::id()
            ? $this->score->wIndex()
            : $this->signin(self::APP, Request::url());
        
    }
    
    /**
     * 考试详情
     *
     * @return array|Factory|JsonResponse|View|null|string|
     */
    public function detail() {
        
        $user = Auth::user();
        if ($user->custodian) {
            return $this->score->studentDetail();
        } elseif ($user->educator) {
            return $this->score->classDetail();
        }

        return __('messages.unauthorzied');
    
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
            'data'    => $data,
            'student' => $student,
            'exam'    => $exam,
        ]);
        
    }
    
    /**
     * 微信 教师端成绩分析
     *
     * @return string
     */
    public function analyze() {
        
        #需要判断当前访问者是否是教师
        $input['exam_id'] = Request::get('examId');
        $input['squad_id'] = Request::get('classId');
        $exam = Exam::whereId($input['exam_id'])->first();
        $squad = Squad::whereId($input['squad_id'])->first();
        if (!$exam) {
            return '暂未找到本场考试相关数据！';
        }
        if (!$squad) {
            return '暂未该班级相关数据！';
        }
        #需要返回给视图页面的数据
        $data = $this->score->classStat(true);
        if (!$data) {
            $data = [
                'className'   => $exam->start_date,
                'examName'    => $exam->name,
                'oneData'     => [],
                'rangs'       => [],
                'totalRanges' => [],
            ];
        }
        
        return view('wechat.score.edu_analysis', [
            'data'    => $data,
            'examId'  => $input['exam_id'],
            'classId' => $input['squad_id'],
        ]);
    }
    
    /**
     * 微信 监护人端综合
     */
    public function stat() {
        
        #综合返回回分数
        $input['exam_id'] = Request::get('examId');
        $input['student_id'] = Request::get('studentId');
        $exam = Exam::whereId($input['exam_id'])->first();
        $student = Student::whereId($input['student_id'])->first();
        if (!$exam) {
            $examName = '';
            $examDate = '';
        } else {
            $examName = $exam->name;
            $examDate = $exam->start_date;
        }
        if (!$student) {
            return '暂未该学生相关数据';
        }
        $data = $this->score->totalAnalysis($input);
        
        return view('wechat.score.cus_total', [
            'data'      => $data,
            'examName'  => $examName,
            'examDate'  => $examDate,
            'studentId' => $input['student_id'],
            'examId'    => $input['exam_id'],
        ]);
        
    }
    
}