<?php
namespace App\Http\Controllers;

use App\Helpers\HttpStatusCode;
use App\Http\Requests\ScoreRequest;
use App\Models\Exam;
use App\Models\Score;
use App\Models\Squad;
use App\Models\Subject;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Request;
use Maatwebsite\Excel\Facades\Excel;
use Throwable;

/**
 * 成绩
 *
 * Class ScoreController
 * @package App\Http\Controllers
 */
class ScoreController extends Controller {
    
    protected $score;
    
    public function __construct(Score $score) {
        
        $this->middleware(['auth', 'checkrole']);
        $this->score = $score;
        
    }
    
    /**
     * 成绩列表
     *
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function index() {
        
        if (Request::get('draw')) {
            return response()->json(
                $this->score->datatable()
            );
        }
        
        return $this->output();
        
    }
    
    /**
     * 录入成绩
     *
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function create() {
        
        return $this->output();
        
    }
    
    /**
     * 保存成绩
     *
     * @param ScoreRequest $request
     * @return JsonResponse
     */
    public function store(ScoreRequest $request) {
        
        $input = $request->all();
        $subject = Subject::whereId($input['subject_id'])->first();
        abort_if(
            $input['score'] > $subject->max_score,
            HttpStatusCode::NOT_ACCEPTABLE,
            '该科目最高分为' . $subject->max_score
        );
        
        return $this->result(
            $this->score->store(
                $request->all()
            )
        );
        
    }
    
    /**
     * 修改成绩
     *
     * @param $id
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function edit($id) {
        
        $score = Score::find($id);
        abort_if(!$score, HttpStatusCode::NOT_FOUND);
        
        return $this->output([
            'score'       => $score,
            'studentName' => $score->student->user->realname,
        ]);
        
    }
    
    /**
     * 更新成绩
     *
     * @param ScoreRequest $request
     * @param $id
     * @return JsonResponse
     */
    public function update(ScoreRequest $request, $id) {
        
        $input = $request->all();
        $score = Score::find($id);
        abort_if(!$score, HttpStatusCode::NOT_FOUND, __('messages.not_found'));
        $subject = Subject::whereId($input['subject_id'])->first();
        abort_if(
            $input['score'] > $subject->max_score,
            HttpStatusCode::NOT_ACCEPTABLE,
            '该科目最高分为' . $subject->max_score
        );
        
        return $this->result(
            $score->modify($request->all(), $id)
        );
        
    }
    
    /**
     * 删除成绩
     *
     * @param $id
     * @return JsonResponse
     * @throws Exception
     */
    public function destroy($id) {
        
        $score = Score::find($id);
        abort_if(!$score, HttpStatusCode::NOT_FOUND);
        
        return $this->result(
            $score->remove($id)
        );
        
    }
    
    /**
     * 发送成绩
     *
     * @return JsonResponse
     */
    public function send() {

        abort_if(
            Request::method() !== 'POST',
            HttpStatusCode::METHOD_NOT_ALLOWED,
            __('messages.method_not_allowed')
        );
        $exam = Request::input('exam');
        $squad = Request::input('squad');
        $subject = Request::input('subject');
        $project = Request::input('project');
        if ($exam && $squad) {
            $score = new Score();
            $result = $score->scores(
                $exam,
                $squad,
                explode(',', $subject),
                explode(',', $project)
            );
        } else {
            $ids = Exam::whereId($exam)->first();
            $classes = Squad::whereIn('id', explode(',', $ids['class_ids']))
                ->get()->toArray();
            $subjects = Subject::whereIn('id', explode(',', $ids['subject_ids']))
                ->get()->toArray();
            $result = [
                'classes'  => $classes,
                'subjects' => $subjects,
            ];
    
        }
        
        return response()->json($result);
    
    
    }
    
    /**
     * 发送成绩信息
     *
     * @return JsonResponse
     */
    public function send_message() {
        
        abort_if(
            Request::method() != 'POST',
            HttpStatusCode::METHOD_NOT_ALLOWED,
            __('messages.method_not_allowed')
        );
        $data = Request::input('data');
        $score = new Score();
        
        return response()->json(
            $score->sendMessage(json_decode($data))
        );
        
    }
    
    /**
     * 导入学生考试成绩
     *
     * @throws \PHPExcel_Exception
     */
    public function import() {
        
        $input = Request::all();
        $file = Request::file('file');
        if (empty($file)) {
            $result = [
                'statusCode' => 500,
                'message'    => '您还没选择文件！',
            ];
            
            return response()->json($result);
        }
        // 文件是否上传成功
        if ($file->isValid()) {
            $result = $this->score->upload($file, $input);
            
            return response()->json($result);
        }
        
        return response()->json(['statusCode' => 500, 'message' => '上传失败！']);
        
    }
    
    /**
     * 分数统计
     *
     * @param $id
     * @return mixed
     */
    public function stat($id) {
        
        #先判断这个考试录入分数没有
        if (!Score::whereExamId($id)->first()) {
            return response()->json(['message' => '本次考试还未录入成绩！', 'statusCode' => 500]);
        }
        
        return $this->result(
            $this->score->stat($id)
        );
        
    }
    
    /**
     * 根据考试异步加载班级列表
     *
     * @param $exam_id
     * @return JsonResponse|string
     */
    public function claLists($exam_id) {
        
        $exam = Exam::whereId($exam_id)->first();
        if (!$exam) {
            $lists = [];
        } else {
            $lists = Squad::whereIn('id', explode(',', $exam->class_ids))
                ->whereEnabled(1)
                ->pluck('name', 'id')
                ->toArray();
        }
        #返回下拉列表的字符串
        $html = '';
        foreach ($lists as $key => $value) {
            $html .= '<option value="' . $key . '">' . $value . '</option>';
        }
        
        return $this->result($lists, $html);
        
    }
    
    /**
     * 成绩分析
     *
     * @throws Throwable
     */
    public function analysis() {
        return $this->output();
    }
    
    /**
     * 成绩分析表格数据填充
     * @throws Throwable
     */
    public function analydata() {
        
        $input = Request::all();
        $view = Score::analysis($input);
        
        return $this->result($view, $view, '未录入或未统计成绩！');
        
    }
    
    /**
     * 根据考试id获取 对应的学生和科目
     * @param $examId
     * @return JsonResponse
     */
    public function listdatas($examId) {
        
        $exam = Exam::whereId($examId)->first();
        $squadIds = explode(',', $exam->class_ids);
        $subjectIds = explode(',', $exam->subject_ids);
        #找出这个考试对应的学生
        $students = [];
        foreach ($squadIds as $squadId) {
            $squ = Squad::whereId($squadId)->first();
            foreach ($squ->students as $student) {
                $students[$student->id] = $student->student_number . '-' . $student->user->realname;
            }
        }
        #找出这个考试对应的科目
        $subjects = [];
        foreach ($subjectIds as $subjectId) {
            $sub = Subject::whereId($subjectId)->first();
            $subjects[$sub->id] = $sub->name;
        }
        #返回下拉列表的字符串
        $studentHtml = '';
        foreach ($students as $key => $value) {
            $studentHtml .= '<option value="' . $key . '">' . $value . '</option>';
        }
        $subjectHtml = '';
        foreach ($subjects as $key => $value) {
            $subjectHtml .= '<option value="' . $key . '">' . $value . '</option>';
        }
        
        return response()->json([
            'statusCode' => HttpStatusCode::OK,
            'students'   => $studentHtml,
            'subjects'   => $subjectHtml,
        ]);
        
    }
    
    /**
     * 导出成绩模板
     *
     * @return void
     */
    public function exports() {
        $examId = Request::get('examId');
        $exam = Exam::whereId($examId)->first();
        $subjectIds = explode(',', $exam->subject_ids);
        $subjects = Subject::whereIn('id', $subjectIds)->get();
        $title = ['班级', '学号', '姓名'];
        foreach ($subjects as $s) {
            $title[] = $s->name;
        }
        $data[] = $title;
        $classId = Request::get('classId');
        $class = Squad::find($classId);
        $students = $class->students;
        foreach ($students as $s) {
            $exams = [
                $class->name,
                $s->student_number,
                $s->user->realname,
            ];
            $data[] = $exams;
            unset($exams);
        }
        /** @noinspection PhpMethodParametersCountMismatchInspection */
        /** @noinspection PhpUndefinedMethodInspection */
        Excel::create(iconv('UTF-8', 'GBK', 'scores'), function ($excel) use ($data) {
            /** @noinspection PhpUndefinedMethodInspection */
            $excel->sheet('score', function ($sheet) use ($data) {
                /** @noinspection PhpUndefinedMethodInspection */
                $sheet->rows($data);
                /** @noinspection PhpUndefinedMethodInspection */
                $sheet->setWidth([
                    'A' => 15,
                    'B' => 15,
                    'C' => 15,
                    'D' => 10,
                    'E' => 10,
                    'F' => 10,
                ]);
                
            });
        }, 'UTF-8')->export('xls');
        
    }
    
    /**
     * 根据班级异步加载学生列表
     *
     * @param $suquad_id
     * @return JsonResponse|string
     */
    public function clastudents($suquad_id) {
        
        $squad = Squad::whereId($suquad_id)->first();
        $students = [];
        if ($squad) {
            $stus = $squad->students;
            foreach ($stus as $item) {
                $students[$item->id] = $item->student_number . '-' . $item->user->realname;
            }
        }
        #返回下拉列表的字符串
        $html = '';
        foreach ($students as $key => $value) {
            $html .= '<option value="' . $key . '">' . $value . '</option>';
        }
        
        return $this->result($students, $html);
        
    }
    
}

