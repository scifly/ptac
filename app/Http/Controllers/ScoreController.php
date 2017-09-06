<?php

namespace App\Http\Controllers;

use App\Http\Requests\ScoreRequest;
use App\Models\Exam;
use App\Models\Score;
use App\Models\Student;
use Excel;
use Illuminate\Support\Facades\Request;

class ScoreController extends Controller {
    
    protected $score;
    protected $exam;
    protected $student;

    function __construct(Score $score, Exam $exam, Student $student) {
        $this->score = $score;
        $this->exam = $exam;
        $this->student = $student;
    }
    
    /**
     * 显示成绩列表
     *
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public function index() {
        
        if (Request::get('draw')) {
            return response()->json($this->score->datatable());
        }
        return $this->output(__METHOD__);
        
    }
    
    /**
     * 显示创建成绩记录的表单
     *
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public function create() {
        
        return $this->output(__METHOD__);
        
    }
    
    /**
     * 保存新创建的成绩记录
     *
     * @param ScoreRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(ScoreRequest $request) {
        
        return $this->score->create($request->all()) ? $this->succeed() : $this->fail();
        
    }
    
    /**
     * 显示指定的成绩记录详情
     *
     * @param $id
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public function show($id) {
        
        $score = $this->score->find($id);
        if (!$score) { return $this->notFound(); }
        return $this->output(__METHOD__, [
            'score' => $score,
            'studentName' => $score->student->user->realname
        ]);
        
    }
    
    /**
     * 显示编辑指定成绩记录的表单
     *
     * @param $id
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public function edit($id) {
        
        $score = $this->score->find($id);
        if (!$score) { return $this->notFound(); }
        return $this->output(__METHOD__, [
            'score' => $score,
            'studentName' => $score->student->user->realname
        ]);
        
    }
    
    /**
     * 更新指定的成绩记录
     *
     * @param ScoreRequest $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(ScoreRequest $request, $id) {
        
        $score = $this->score->find($id);
        if (!$score) { return $this->notFound(); }
        return $score->update($request->all()) ? $this->succeed() : $this->fail();
        
    }
    
    /**
     * 删除指定的成绩记录
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id) {
        
        $score = $this->score->find($id);
        if (!$score) { return $this->notFound(); }
        return $score->delete() ? $this->succeed() : $this->fail();
    
    }

    /**
     * 统计成绩排名
     *
     * @param $examId
     * @return \Illuminate\Http\JsonResponse
     */
    public function statistics($examId) {
        return $this->score->statistics($examId) ? $this->succeed() : $this->fail();
    }


    /**
     * Excel模板生成
     * @param $examId
     */
    public function export($examId){
        $exam = $this->exam->find($examId);
        $subject = $this->exam->subjects($exam->subject_ids);
        $heading = ['学号','姓名'];
        foreach ($subject as $value){
            $heading[] = $value;
        }
        $cellData = $this->student->studentsNum($exam->class_ids);
        array_unshift($cellData,$heading);

        Excel::create('score',function($excel) use ($cellData){
            $excel->sheet('score', function($sheet) use ($cellData){
                $sheet->rows($cellData);
            });
        })->export('xls');
    }


    /**
     * 成绩导入
     */
    public function import(){
        $filePath = 'storage/exports/score.xls';
        $data = Excel::load($filePath)->get();
        foreach ($data as $val){
            var_dump($val->学号);
        }

    }
}

