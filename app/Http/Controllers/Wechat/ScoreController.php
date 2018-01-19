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
        $role = User::whereUserid($userId)->first()->group->name;
        switch ($role){
            case '监护人':
                $students = User::whereUserid($userId)->first()->custodian->students;
                $score = $studentName =[];
                foreach ($students as $k=>$s)
                {
                    $exams = Exam::where('class_ids','like','%' . $s->class_id . '%')
                        ->get();
                    foreach ($exams as $e)
                    {
                        $score[$k]['name'] = $e->name;
                        $score[$k]['strat_date'] = $e->start_date;
                    }
                    $score[$k]['user_id'] = $s->user_id;
                    $score[$k]['realname'] = $s->user->realname;
                    $score[$k]['class_id'] = $s->class_id;
                    $studentName[$s->user_id] = $s->user->realname;
                    ksort($studentName);

                }
                return view('wechat.scores.students_score_lists',[
                    'scores' => $score,
                    'studentName' => $studentName,
                ]);
                break;
            case '教职员工':
                break;
            default:
                break;
        }
    }


    /**
     * 成绩详情
     *
     * @return bool|JsonResponse
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

