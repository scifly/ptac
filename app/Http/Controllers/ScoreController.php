<?php
namespace App\Http\Controllers;

use App\Http\Requests\ScoreRequest;
use App\Models\Exam;
use App\Models\Score;
use App\Models\Squad;
use App\Models\Subject;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Request;
use Throwable;

/**
 * 成绩
 *
 * Class ScoreController
 * @package App\Http\Controllers
 */
class ScoreController extends Controller {
    
    function __construct() {
        
        $this->middleware(['auth', 'checkrole']);
        
    }
    
    /**
     * 成绩列表
     *
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function index() {
        
        if (Request::get('draw')) {
            return response()->json(Score::datatable());
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
        
        return $this->result(Score::create($request->all()));
        
    }
    
    /**
     * 成绩详情
     *
     * @param $id
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function show($id) {
        
        $score = Score::find($id);
        if (!$score) { return $this->notFound(); }
        
        return $this->output([
            'score'       => $score,
            'studentName' => $score->student->user->realname,
        ]);
        
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
        if (!$score) { return $this->notFound(); }
        
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
        
        $score = Score::find($id);
        if (!$score) { return $this->notFound(); }
        
        return $this->result($score->update($request->all()));
        
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
        if (!$score) { return $this->notFound(); }
        
        return $this->result($score->delete());
        
    }
    
    /**
     * 成绩发送
     *
     * @return JsonResponse
     */
    public function send() {
//        $score = new Score();
//        $exam = 1;
//        $squad = 1;
//        $subject = [1,2];
//        $project = ['class_rank', 'grade_average', 'class_average'];
//
//        $result = $score->scores($exam, $squad, $subject, $project);
//        return response()->json($result);die;
        if (Request::method() === 'POST') {
            $exam = Request::input('exam');
            $squad = Request::input('squad');
            $subject = Request::input('subject');
            $project = Request::input('project');
            if ($exam && $squad ) {
                $score = new Score();
                $result = $score->scores($exam, $squad, explode(',', $subject), explode(',', $project));
                return response()->json($result);
            }else{
                $ids = Exam::whereId($exam)->first();
                
                $classes = Squad::whereIn('id', explode(',', $ids['class_ids']))
                    ->get()
                    ->toArray();
                $subjects = Subject::whereIn('id', explode(',', $ids['subject_ids']))
                    ->get()
                    ->toArray();
                $result = [
                    'classes' => $classes,
                    'subjects' => $subjects,
                ];
                return response()->json($result);
            }
            
            
        }
    }
    
    /**
     * 发送成绩信息
     *
     */
    public function send_message() {
        if (Request::method() === 'POST') {
            $data = Request::input('data');
            $score = new Score();
            return response()->json($score->sendMessage(json_decode($data)));
            
            
        }
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
            $result = Score::upload($file, $input);
        
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
    public function statistics($id){
        #先判断这个考试录入分数没有
        if(!Score::whereExamId($id)->first()){
            return response()->json(['message' => '本次考试还未录入成绩！', 'statusCode' => 500]);
        }
       return Score::statistics($id) ? $this->succeed() : $this->fail();
       
    }
    
    /**
     * 根据考试异步加载班级列表
     *
     * @param $exam_id
     * @return JsonResponse|string
     */
    public function claLists($exam_id){
        $exam = Exam::whereId($exam_id)->first();
        $lists = Squad::whereIn('id', explode(',', $exam->class_ids))
            ->whereEnabled(1)
            ->pluck('name', 'id')
            ->toArray();
        #返回下拉列表的字符串
        $html = '';
        foreach ($lists as $key => $value) {
            $html .= '<option value="' . $key . '">' . $value . '</option>';
        }
        return $lists ? $this->succeed($html) : $this->fail();
    }
    
    /**
     * 成绩分析
     *
     * @throws Throwable
     */
    public function analysis(){
        return $this->output();
    }
  
  
    // /**
    //  * 成绩发送
    //  *
    //  * @return JsonResponse
    //  */
    // public function send() {
    //
    //     if (Request::method() === 'POST') {
    //         $exam = Request::input('exam');
    //         if($exam) {
    //             $ids = Exam::whereId($exam)->first();
    //
    //             $classes = Squad::where('id', explode(',', $ids->class_ids))
    //                 ->pluck('name', 'id')
    //                 ->toArray();
    //             return response()->json($classes);
    //         }
    //     }
    // }
    //
    //
    // /**
    //  * 统计成绩排名
    //  *
    //  * @param $examId
    //  * @return JsonResponse
    //  */
    // public function statistics($examId) {
    //
    //     return $this->result(Score::statistics($examId));
    // }
    //
    // /**
    //  * Excel模板生成
    //  * @param $examId
    //  */
    // public function export($examId) {
    //
    //     $exam = Exam::find($examId);
    //     $subject = Exam::subjects($exam->subject_ids);
    //     $heading = ['学号', '姓名'];
    //     foreach ($subject as $value) {
    //         $heading[] = $value;
    //     }
    //     $cellData = Student::studentsNum($exam->class_ids);
    //     array_unshift($cellData, $heading);
    //     Excel::create('score', function ($excel) use ($cellData, $examId) {
    //         $excel->sheet('score', function ($sheet) use ($cellData) {
    //             $sheet->rows($cellData);
    //         });
    //         $excel->setTitle($examId);
    //     })->store('xls')->export('xls');
    //
    // }
    //
    // /**
    //  * 成绩导入
    //  */
    // public function import() {
    //     $filePath = 'storage/exports/score.xls';
    //     $insert = [];
    //     Excel::load($filePath, function ($reader) use (&$insert) {
    //         $exam_id = $reader->getTitle();
    //         $subjects = Subject::ids(array_slice(array_keys($reader->toArray()[0]), 2));
    //         $reader->each(function ($sheet) use ($exam_id, $subjects, &$insert) {
    //             $studentNum = '';
    //             foreach ($sheet as $key => $row) {
    //                 switch ($key) {
    //                     case '学号':
    //                         $studentNum = Student::whereStudentNumber($row)->value('id');
    //                         break;
    //                     case '姓名':
    //                         break;
    //                     default:
    //                         if (!is_null($row) && isset($subjects[$key])) {
    //                             $insert [] = [
    //                                 'student_id' => $studentNum,
    //                                 'subject_id' => $subjects[$key],
    //                                 'exam_id'    => $exam_id,
    //                                 'score'      => $row,
    //                                 'enabled'    => 1,
    //                             ];
    //                         }
    //                 }
    //             }
    //         });
    //     });
    //
    //     return Score::insert($insert) ? $this->succeed() : $this->fail();
    // }
}

