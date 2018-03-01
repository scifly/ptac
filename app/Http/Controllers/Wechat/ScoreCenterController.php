<?php
namespace App\Http\Controllers\Wechat;

use App\Facades\Wechat;
use App\Helpers\HttpStatusCode;
use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\Score;
use App\Models\Squad;
use App\Models\Student;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Session;

/**
 * 微信端成绩
 *
 * Class ScoreController
 * @package App\Http\Controllers
 */
class ScoreCenterController extends Controller {
    
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
     *  微信端成绩
     *
     * @return string
     */
    public function index() {
        
        $corpId = 'wxe75227cead6b8aec';
        $secret = 'viHdGD1DaiDAOwbrxCZP5wT7QYNNqJpPnr3Sw5YPio4';
        $agentId = 1000008;
        $userId = Session::get('userId') ? Session::get('userId') : null;
        $code = Request::input('code');
        if (empty($code) && empty($userId)) {
            $codeUrl = Wechat::getCodeUrl($corpId, $agentId, 'http://weixin.028lk.com/wechat/score/score_lists');
            return redirect($codeUrl);
        } elseif (!empty($code) && empty($userId)) {
            $accessToken = Wechat::getAccessToken($corpId, $secret);
            $userInfo = json_decode(Wechat::getUserInfo($accessToken, $code), JSON_UNESCAPED_UNICODE);
            $userId = $userInfo['UserId'];
            Session::put('userId', $userId);
        }
        $role = User::whereUserid($userId)->first()->group->name;
        $pageSize = 4;
        $start = Request::get('start') ? Request::get('start') * $pageSize : 0;
        $scores = [];
        switch ($role) {
            case '监护人':
                if (Request::isMethod('post')) {
                    $studentId = Request::get('student_id');
                    $classId = Student::whereId($studentId)->first()->class_id;
                    if (array_key_exists('start', Request::all())) {
                        $score = $this->score->getClassScore($classId);
                        $scores = array_slice($score, $start, $pageSize);
                        
                        return response()->json(['data' => $scores, 'studentId' => $studentId]);
                    } elseif (array_key_exists('keywords', Request::all())) {
                        $keyword = Request::get('keywords');
                        $score = $this->score->getClassScore($classId, $keyword);
                        $scores = array_slice($score, $start, $pageSize);
                        
                        return response()->json(['data' => $scores, 'studentId' => $studentId]);
                    } else {
                        $score = $this->score->getClassScore($classId);
                        $scores = array_slice($score, $start, $pageSize);
                        
                        return response()->json(['data' => $scores, 'studentId' => $studentId]);
                    }
                }
                $data = $this->score->getStudentScore($userId);
                $score = $data['score'];
                $studentName = $data['studentName'];
                if (sizeof($score) != 0) {
                    $scores = array_slice($score[0], $start, $pageSize);
                }
                
                return view('wechat.score.students_score_lists', [
                    'scores'      => $scores,
                    'studentName' => json_encode($studentName, JSON_UNESCAPED_UNICODE),
                    'pageSize'    => $pageSize,
                ]);
                break;
            case '教职员工':
                if (Request::isMethod('post')) {
                    $classId = Request::get('class_id');
                    if (array_key_exists('start', Request::all())) {
                        $score = $this->score->getClassScore($classId);
                        $scores = array_slice($score, $start, $pageSize);
                        
                        return response()->json(['data' => $scores]);
                        
                    } elseif (array_key_exists('keywords', Request::all())) {
                        $keyword = Request::get('keywords');
                        $score = $this->score->getClassScore($classId, $keyword);
                        $scores = array_slice($score, $start, $pageSize);
                        
                        return response()->json(['data' => $scores]);
                    } else {
                        $score = $this->score->getClassScore($classId);
                        $scores = array_slice($score, $start, $pageSize);
                        
                        return response()->json(['data' => $scores]);
                    }
                }
                $datas = $this->score->getEducatorScore();
                if (!$datas) {
                    return '你还没有对应的班级';
                }
                $score = $datas['score'];
                $className = $datas['className'];
                if (sizeof($score) != 0) {
                    $scores = array_slice($score[0], $start, $pageSize);
                }
                
                return view('wechat.score.educator_score_lists', [
                    'scores'    => $scores,
                    'className' => json_encode($className, JSON_UNESCAPED_UNICODE),
                    'pageSize'  => $pageSize,
                ]);
            default:
                return abort(HttpStatusCode::BAD_REQUEST, '请求无效');
        }
        
    }
    
    /**
     * 学生考试详情页
     */
    public function subjectDetail() {
        
        $subjectIds = $subjects = $studentIds = $allStudentIds = $classIds = $data = $allScores = $total = [];
        $examId = Request::get('examId');
        $studentId = Request::get('studentId');
        // $studentId= 2;
        # 获取该学生班级所有学生
        $exam = Exam::whereId($examId)->first();
        # 获取该次考试该学生所在的年级id
        $gradeId = Student::whereId($studentId)->first()->squad->grade->id;
        $classes = Squad::whereGradeId($gradeId)->get();
        foreach ($classes as $c) {
            $classIds[] = $c->id;
        }
        # 获取该次考试所有科目id
        if (sizeof($exam) != 0) {
            $subjectIds = explode(',', $exam->subject_ids);
        } else {
            $subject = Subject::all();
            foreach ($subject as $s) {
                $subjectIds[] = $s->id;
            }
        }
        # 获取该班级所有学生
        $students = Student::whereId($studentId)->first()->squad->students;
        foreach ($students as $s) {
            $studentIds[] = $s->id;
        }
        # 获取该年级所有学生
        $allStudents = Student::whereIn('class_id', $classIds)->get();
        foreach ($allStudents as $a) {
            $allStudentIds[] = $a->id;
        }
        foreach ($subjectIds as $k => $s) {
            $subjects[] = [
                'title' => Subject::whereId($s)->first()->name,
                'value' => $s,
            ];
        }
        if (Request::isMethod('post')) {
            $subjectId = Request::get('subject_id');
            $scores = $this->score->getScores($examId, $subjectId, $studentId);
            $allScores = $this->score->getAllScores($subjectId, $studentId);
            $scores['start_date'] = $exam['start_date'];
            foreach ($allScores as $k => $a) {
                $total['name'][] = $a->exam->name;
                $total['score'][] = $a->score;
                $total['avg'][] = $this->score->getClassAvg($a->exam_id, $subjectId, $studentIds)['avg'];
            }
            $scores['start_date'] = $exam['start_date'];
            $classData = $this->score->getClassAvg($examId, $subjectId, $studentIds);
            $gradesData = $this->score->getClassAvg($examId, $subjectId, $allStudentIds);
            $data = [
                # 统计该学生本次考试该科目班上的平均成绩
                'avg'       => number_format($classData['avg'], 2),
                'nums'      => $classData['nums'],
                # 统计该学生本次考试该科目年级的平均成绩
                'gradeavg'  => number_format($gradesData['avg'], 2),
                'gradeNums' => $gradesData['nums'],
            ];
            
            return response()->json(['scores' => $scores, 'data' => $data, 'total' => $total]);
        }
        $scores = $this->score->getScores($examId, $subjectIds[0], $studentId);
        if (!empty($scores)) {
            $scores['start_date'] = $exam['start_date'];
        }
        if (empty($scores)) {
            $scores = [];
        }
        $allScores = $this->score->getAllScores($subjectIds[0], $studentId);
        $total['name'] = $total['score'] = $total['avg'] = [];
        foreach ($allScores as $k => $a) {
            $total['name'][] = $a->exam->name;
            $total['score'][] = $a->score;
            $total['avg'][] = $this->score->getClassAvg($a->exam_id, $subjectIds[0], $studentIds)['avg'];
        }
        $classData = $this->score->getClassAvg($examId, $subjectIds[0], $studentIds);
        $gradesData = $this->score->getClassAvg($examId, $subjectIds[0], $allStudentIds);
        $data = [
            # 统计该学生本次考试该科目班上的平均成绩
            'avg'       => number_format($classData['avg'], 2),
            'nums'      => $classData['nums'],
            # 统计该学生本次考试该科目年级的平均成绩
            'gradeavg'  => number_format($gradesData['avg'], 2),
            'gradeNums' => $gradesData['nums'],
        ];
        
        return view('wechat.score.student_subject_detail', [
            'scores'    => $scores,
            'data'      => $data,
            'subjects'  => json_encode($subjects, JSON_UNESCAPED_UNICODE),
            'total'     => json_encode($total, JSON_UNESCAPED_UNICODE),
            'examId'    => $examId,
            'studentId' => $studentId,
        ]);
        
    }
    
    /**
     * 成绩详情
     *
     * @return bool|JsonResponse
     */
    public function detail() {
        
        $classId = Request::input('classId');
        $examId = Request::input('examId');
        $student = Request::input('student');
//        $classId = 1;
//        $examId = 1;
        if ($classId && $examId) {
            $data = $this->score->getExamClass($examId, $classId, $student);

//            print_r($data);die;
            return view('wechat.score.detail', [
                'data'    => $data,
                'classId' => $classId,
                'examId'  => $examId,
            ]);
        }
        
        return abort(HttpStatusCode::BAD_REQUEST, '请求无效');
        
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
    public function analysis() {
        
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
        $data = $this->score->claAnalysis($input, true);
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
    public function cusTotal() {
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

