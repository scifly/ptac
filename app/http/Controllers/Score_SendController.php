<?php
namespace App\Http\Controllers;

//用到学校数据模型
use App\Models\Exam;
use App\Models\Grade;
use App\Models\School;
use App\Models\Score;
use App\Models\ScoreTotal;
use App\Models\Squad;
use App\Models\Student;
use App\Models\Subject;

/**
 * 成绩发送
 *
 * Class Score_SendController
 * @package App\Http\Controllers
 */
class Score_SendController extends Controller {

    protected $exam, $score, $user, $student,
        $class, $school, $grade, $subject, $scoreTotal;

    /**
     * Score_SendController constructor.
     * 初始化注入关联模型
     * @param Exam $exam
     * @param School $school
     * @param Grade $grade
     * @param Squad $class
     * @param Student $student
     * @param Score $score
     * @param Subject $subject
     * @param ScoreTotal $scoreTotal
     */
    function __construct(
        Exam $exam,
        School $school,
        Grade $grade,
        Squad $class,
        Student $student,
        Score $score,
        Subject $subject,
        ScoreTotal $scoreTotal
    ) {

        $this->exam = $exam;
        $this->school = $school;
        $this->grade = $grade;
        $this->class = $class;
        $this->student = $student;
        $this->score = $score;
        $this->subject = $subject;
        $this->scoreTotal = $scoreTotal;
    }

    /**
     * 发送成绩首页,加载学校列表
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index() {
        //先通过角色判断管理员、政教、年级主任等 多角色获取
        //如果是普通老师获取关联的考次，班主任获取管理班级所考次，科任老师获取任教科目考次
        return view("score_send.index", ['js' => 'js/score_send/index.js', 'schools' => $this->school->all(['name', 'id']), 'form' => 0, 'datatable' => 1]);
    }

    /**
     * @param null $id
     * @return string
     */
    public function getGrade($id = null) {
        //根据学校ID获取年级信息
        return json_encode($this->grade->all(['id', 'name', 'school_id'])->where("school_id", $id));

    }

    /**
     * @param null $id
     * @return string
     */
    public function getClass($id = null) {
        //根据年级ID获取班级信息
        return json_encode($this->class->all(['id', 'name', 'grade_id'])->where("grade_id", $id));

    }

    /**
     * @param null $id
     * @return string
     */
    public function getExam($id = null) {

        //根据班级ID获取考次
        return json_encode($this->exam->examsByClassId($id));
    }

    /**
     * @param null $id
     * @return string
     */
    public function getSubject($id = null) {
        //获取考次关联的科目
        return json_encode($this->exam->subjectsByExamId($id));
    }

    /**
     * @param $examId
     * @param $classId
     * @param $subjectIds
     * @param $itemId
     * @return string
     */
    public function preview($examId, $classId, $subjectIds, $itemId) {
        #拆分所选科目
        $subject_Ids = explode(',', $subjectIds);
        #拆分所选项目
        $item_Ids = explode(',', $itemId);
        #获取年级ID
        $grade_id = $this->class->where('id', $classId)
            ->first(['grade_id'])->grade_id;
        #获取年级的考试信息
        $grade_scores =
            $this->student
                ->join('classes', 'students.class_id', 'classes.id')
                ->where('grade_id', $grade_id)
                ->join('scores', 'students.id', 'scores.student_id')
                ->join('exams', 'exams.id', 'scores.exam_id')
                #获取当前考试
                ->where('exams.id', $examId)
                ->join('subjects', 'scores.subject_id', 'subjects.id')
                ->join('users', 'students.user_id', 'users.id')
                ->get(['scores.score'
                    , 'scores.subject_id', 'subjects.max_score', 'subjects.pass_score'
                    , 'scores.class_rank', 'scores.grade_rank', 'students.id']);
        #获取班级的考试信息
        $class_scores =
            $this->student->where('class_id', $classId)
                ->join('classes', 'students.class_id', 'classes.id')
                ->join('scores', 'students.id', 'scores.student_id')
                ->join('exams', 'exams.id', 'scores.exam_id')
                #获取当前考试
                ->where('exams.id', $examId)
                ->join('subjects', 'scores.subject_id', 'subjects.id')
                ->join('users', 'students.user_id', 'users.id')
                ->get(['users.realname', 'scores.score', 'exams.name as examname', 'subjects.name'
                    , 'scores.subject_id', 'subjects.max_score', 'subjects.pass_score'
                    , 'scores.class_rank', 'scores.grade_rank', 'students.id']);
        #年级总分信息
        $grade_total_scores =
            $this->student->join('classes', 'students.class_id', 'classes.id')
                ->where('grade_id', $grade_id)
                ->leftJoin('score_totals', 'students.id', 'score_totals.student_id')
                ->get(['students.id'
                    , 'score_totals.class_rank as totals_class_rank'
                    , 'score_totals.grade_rank as totals_grade_rank',
                    'score_totals.score as totals_score']);
        #班级总分信息
        $class_total_scores =
            $this->student->where('class_id', $classId)
                ->leftJoin('score_totals', 'students.id', 'score_totals.student_id')
                ->get(['students.id'
                    , 'score_totals.class_rank as totals_class_rank'
                    , 'score_totals.grade_rank as totals_grade_rank',
                    'score_totals.score as totals_score']);
        #获取学生信息
        $students = $class_scores->groupBy('id');
        #拼接信息
        $strs = [];
        /** @var Student $student */
        foreach ($students as $student) {
            $str = '';
            #获取考试名称及姓名
            $str .= $student[0]->realname . '在' . '[' . $student[0]->examname . ']中，考试成绩如下:';
            #循环科目取分数及科目名称
            foreach ($subject_Ids as $sid) {
                #防止总分循环
                if ($sid == 0) continue;
                #科目名称
                $str .= $student->where('subject_id', $sid)->first()->name . ':';
                #科目分数
                $str .= $student->where('subject_id', $sid)->first()->score;
                #判断选择项目
                #0班排名,1年排名,2班平均,3年平均
                #,4班最高,5年最高,6班最低
                #7年最低,8科目总分,9及格分
                if (in_array('0', $item_Ids)) {
                    $str .= '班排:' . $student->where('subject_id', $sid)->first()->class_rank;
                }
                if (in_array('1', $item_Ids)) {
                    $str .= '年排:' . $student->where('subject_id', $sid)->first()->grade_rank;
                }
                if (in_array('2', $item_Ids)) {
                    $str .= '班平均:' . $class_scores->where('subject_id', $sid)->avg('score');
                }
                if (in_array('3', $item_Ids)) {
                    $str .= '年平均:' . $grade_scores->where('subject_id', $sid)->avg('score');
                }
                if (in_array('4', $item_Ids)) {
                    $str .= '班最高:' . $class_scores->where('subject_id', $sid)->max('score');
                }
                if (in_array('5', $item_Ids)) {
                    $str .= '年最高:' . $grade_scores->where('subject_id', $sid)->max('score');
                }
                if (in_array('6', $item_Ids)) {
                    $str .= '班最低:' . $class_scores->where('subject_id', $sid)->min('score');
                }
                if (in_array('7', $item_Ids)) {
                    $str .= '年最低:' . $grade_scores->where('subject_id', $sid)->min('score');
                }
                if (in_array('8', $item_Ids)) {
                    $str .= '科目总分:' . $student->where('subject_id', $sid)->first()->max_score;
                }
                if (in_array('9', $item_Ids)) {
                    $str .= '及格分:' . $student->where('subject_id', $sid)->first()->pass_score;
                }
            }
            #判断总分
            if (in_array('0', $subject_Ids)) {
                $str .= '总分:' . $class_total_scores
                        ->where('id', $student[0]->id)
                        ->first()->totals_score;
                if (in_array('0', $item_Ids)) {
                    $str .= '班排:' . $class_total_scores
                            ->where('id', $student[0]->id)
                            ->first()->totals_class_rank;
                }
                if (in_array('1', $item_Ids)) {
                    $str .= '年排:' . $class_total_scores
                            ->where('id', $student[0]->id)
                            ->first()->totals_grade_rank;
                }
                if (in_array('2', $item_Ids)) {
                    $str .= '班平均:' . $class_total_scores->avg('totals_score');
                }
                if (in_array('3', $item_Ids)) {
                    $str .= '年平均:' . $grade_total_scores->avg('totals_score');
                }
                if (in_array('4', $item_Ids)) {
                    $str .= '班最高:' . $class_total_scores->max('totals_score');
                }
                if (in_array('5', $item_Ids)) {
                    $str .= '年最高:' . $grade_total_scores->max('totals_score');
                }
                if (in_array('6', $item_Ids)) {
                    $str .= '班最低:' . $class_total_scores->min('totals_score');
                }
                if (in_array('7', $item_Ids)) {
                    $str .= '年最低:' . $grade_total_scores->min('totals_score');
                }
//                if(in_array('8',$item_Ids)){
//                    $str .='科目总分:' . $student->where('subject_id',$sid)->first()->max_score;
//                }
//                if(in_array('9',$item_Ids)){
//                    $str .='及格分:' . $student->where('subject_id',$sid)->first()->pass_score;
//                }
            }
            $strs[] = ["id" => $student[0]->id, 'name' => $student[0]->realname, "msg" => $str];
        }
        
        return '';
        
    }

}
