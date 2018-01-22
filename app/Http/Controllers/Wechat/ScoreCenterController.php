<?php
namespace App\Http\Controllers\Wechat;

use App\Http\Controllers\Controller;
use App\Http\Requests\ScoreRequest;
use App\Models\Exam;
use App\Models\Score;
use App\Models\Squad;
use App\Models\Student;
use App\Models\Subject;
use App\Models\User;
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
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|\think\response\View
     */
    public function index()
    {
        // $corpId = 'wxe75227cead6b8aec';
        // $secret = 'uorwAVlN3_EU31CDX0X1oQJk9lB0Or41juMH-cLcIEU';
        // $agentId = 1000007;
        // $userId = Session::get('userId') ? Session::get('userId') : null;
        // $code = Request::input('code');
        // if (empty($code) && empty($userId)) {
        //     $codeUrl = Wechat::getCodeUrl($corpId, $agentId, 'http://weixin.028lk.com/score_lists');
        //     return redirect($codeUrl);
        // }elseif(!empty($code) && empty($userId)){
        //     $accessToken = Wechat::getAccessToken($corpId, $secret);
        //     $userInfo = json_decode(Wechat::getUserInfo($accessToken, $code), JSON_UNESCAPED_UNICODE);
        //     $userId = $userInfo['UserId'];
        //     Session::put('userId',$userId);
        // }
        $userId = 'wangdongxi';
        // $role = '教职员工';
        $role = User::whereUserid($userId)->first()->group->name;
        $pageSize = 4;
        $start = Request::get('start') ? Request::get('start') * $pageSize : 0;
        switch ($role){
            case '监护人':
                if(Request::isMethod('post'))
                {
                    if(array_key_exists('start', Request::all()))
                    {
                        $classId = Request::get('class_id');
                        $score = $this->getClassScore($classId);
                        $scores=array_slice($score,$start,$pageSize);
                        return response()->json(['data' => $scores ]);
                    }else{
                        $classId = Request::get('class_id');
                        $score = $this->getClassScore($classId);
                        $scores=array_slice($score,$start,$pageSize);
                        return response()->json(['data' => $scores ]);
                    }
                }
                $data = $this->getStudentScore($userId);
                $score = $data['score'];
                $studentName = $data['studentName'];
                if( sizeof($score) != 0) { $scores=array_slice($score[0],$start,$pageSize); }
                return view('wechat.scores.students_score_lists',[
                    'scores' => $scores,
                    'studentName' => json_encode($studentName, JSON_UNESCAPED_UNICODE),
                    'pageSize' => $pageSize,
                ]);
                break;
            case '教职员工':
                if(Request::isMethod('post'))
                {
                    if(array_key_exists('start', Request::all()))
                    {
                        $classId = Request::get('class_id');
                        $data = $this->getEducatorScore($userId);
                        $score = $data['score'];
                        # 根据classId取出对应班级的考试
                        foreach ($score as $k=> $s)
                        {
                            foreach ($s as $key=>$v)
                            {
                                if($classId == $v['class_id']){
                                   $scores = $score[$k];
                                };
                            }
                        }
                        $scores=array_slice($scores,$start,$pageSize);
                        return response()->json(['data' => $scores ]);

                    }else{
                        $classId = Request::get('class_id');
                        $score = $this->getClassScore($classId);
                        $scores=array_slice($score,$start,$pageSize);
                        return response()->json(['data' => $scores ]);

                    }

                }
                $datas = $this->getEducatorScore($userId);
                $score =$datas['score'];
                $className = $datas['className'];
                if( sizeof($score) != 0) { $scores=array_slice($score[0],$start,$pageSize); }
                return view('wechat.scores.educator_score_lists',[
                    'scores' => $scores,
                    'className' => json_encode($className, JSON_UNESCAPED_UNICODE),
                    'pageSize' => $pageSize,
                ]);
                break;
            default:
                break;
        }
    }

    /**
     * 根据class_id获取考试的相关信息
     * @param $id
     * @return array
     */
    public function getClassScore($id){
        $score = [];
        $exams = Exam::where('class_ids','like','%' . $id . '%')
            ->get();
        foreach ($exams as $key=>$e)
        {
            $score[$key]['id'] = $e->id;
            $score[$key]['name'] = $e->name;
            $score[$key]['start_date'] = $e->start_date;
            $score[$key]['class_id'] = $id;
        }
        return $score;
    }

    /**
     * 根据监护人获取学生相关考试信息
     * @param $userId
     * @return array
     */
    public function getStudentScore( $userId)
    {
        $students = User::whereUserid($userId)->first()->custodian->students;
        $score = $data = $studentName =[];
        foreach ($students as $k=>$s)
        {
            $exams = Exam::where('class_ids','like','%' . $s->class_id . '%')
                ->get();
            foreach ($exams as $key=>$e)
            {
                $score[$k][$key]['id'] = $e->id;
                $score[$k][$key]['name'] = $e->name;
                $score[$k][$key]['start_date'] = $e->start_date;
                $score[$k][$key]['user_id'] = $s->user_id;
                $score[$k][$key]['realname'] = $s->user->realname;
                $score[$k][$key]['class_id'] = $s->class_id;
            }
            $studentName[]= [
                'title' => $s->user->realname,
                'value' => $s->class_id,
            ];
        }
        $data = [
            'score' => $score,
            'studentName' => $studentName
        ];

        return $data;
    }

    /**根据教职员工userId获取所在班级的考试
     * @param $userId
     * @return array
     */
    public function getEducatorScore($userId)
    {
        $score = $data = $className = [];
        $educatorId = User::whereUserid($userId)->first()->educator->id;
        $class = Squad::where('educator_ids','like','%' . $educatorId . '%')->get();
        foreach ($class as $k=>$c){
            $exams = Exam::where('class_ids','like','%' . $c->id . '%')
                ->get();
            foreach ($exams as $key=>$e)
            {
                $score[$k][$key]['id'] = $e->id;
                $score[$k][$key]['name'] = $e->name;
                $score[$k][$key]['classname'] = $c->name;
                $score[$k][$key]['start_date'] = $e->start_date;
                $score[$k][$key]['class_id'] = $c->id;
            }

            $className[] = [
                'title' => $c->name,
                'value' => $c->id
            ];
        }
        $data = [
            'score' => $score,
            'className' => $className,
        ];

        return $data;
    }

    /**
     * 成绩详情
     *
     * @return bool|JsonResponse
     */
    public function detail() {
        $userId = 'wangdongxi';
        $role = User::whereUserid($userId)->first()->group->name;
        switch ($role)
        {
            case '监护人':
                return view('wechat.scores.student_subject_detail');
                break;
            case '教职员工':
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
                break;
            default:
                break;
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
    
    /**
     * 微信 教师端成绩分析
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function analysis() {
        #需要判断当前访问者是否是教师
        $input = Request::all();
        $input['exam_id'] = 3;
        $input['squad_id'] = 1;
        #需要返回给视图页面的数据
        $data = $this->score->claAnalysis($input, true);
        return view('wechat.score.edu_analysis', ['data' => $data]);
    }
    
    /**
     * 微信 监护人端综合
     */
    public function cusTotal(){
        #综合返回回分数
        $input = Request::all();
        $input['exam_id'] = 3;
        $input['student_id'] = 1;
        $exam = Exam::whereId($input['exam_id'])->first();
        $student = Student::whereId($input['student_id'])->first();
        if(!$exam){
            return '暂未找到本场考试相关数据';
        }
        if(!$student){
            return '暂未该学生相关数据';
        }
        $examName = $exam->name;
        $examDate = $exam->start_date;
        $data = $this->score->totalAnalysis($input);
        
        return view('wechat.score.cus_total',[
            'data' => $data,
            'examName' => $examName,
            'examDate' => $examDate,
            ]);
    }
}

