<?php
namespace App\Http\Controllers;

use App\Http\Requests\ScoreRequest;
use App\Models\Exam;
use App\Models\Score;
use App\Models\Student;
use App\Models\Subject;
use Excel;
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
     * 统计成绩排名
     *
     * @param $examId
     * @return JsonResponse
     */
    public function statistics($examId) {
        
        return $this->result(Score::statistics($examId));
    }
    
    /**
     * Excel模板生成
     * @param $examId
     */
    public function export($examId) {
        
        $exam = $this->exam->find($examId);
        $subject = $this->exam->subjects($exam->subject_ids);
        $heading = ['学号', '姓名'];
        foreach ($subject as $value) {
            $heading[] = $value;
        }
        $cellData = $this->student->studentsNum($exam->class_ids);
        array_unshift($cellData, $heading);
        Excel::create('score', function ($excel) use ($cellData, $examId) {
            $excel->sheet('score', function ($sheet) use ($cellData) {
                $sheet->rows($cellData);
            });
            $excel->setTitle($examId);
        })->store('xls')->export('xls');
        
    }
    
    /**
     * 成绩导入
     */
    public function import() {
        $filePath = 'storage/exports/score.xls';
        $insert = [];
        Excel::load($filePath, function ($reader) use (&$insert) {
            $exam_id = $reader->getTitle();
            $subjects = $this->subject->ids(array_slice(array_keys($reader->toArray()[0]), 2));
            $reader->each(function ($sheet) use ($exam_id, $subjects, &$insert) {
                $studentNum = '';
                foreach ($sheet as $key => $row) {
                    switch ($key) {
                        case '学号':
                            $studentNum = $this->student->whereStudentNumber($row)->value('id');
                            break;
                        case '姓名':
                            break;
                        default:
                            if (!is_null($row) && isset($subjects[$key])) {
                                $insert [] = [
                                    'student_id' => $studentNum,
                                    'subject_id' => $subjects[$key],
                                    'exam_id'    => $exam_id,
                                    'score'      => $row,
                                    'enabled'    => 1,
                                ];
                            }
                    }
                }
            });
        });
        
        return $this->score->insert($insert) ? $this->succeed() : $this->fail();
    }
}

