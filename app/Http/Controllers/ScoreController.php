<?php

namespace App\Http\Controllers;

use App\Http\Requests\ScoreRequest;
use App\Models\Score;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;

class ScoreController extends Controller {
    
    protected $score;
    
    function __construct(Score $score) { $this->score = $score; }
    
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
        
        if ($this->score->existed($request)) {
            return $this->fail('已经有此记录');
        }
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
        if ($this->score->existed($request, $id)) {
            return $this->fail('已经有此记录');
        }
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
    
    public function statistics($exam_id) {
        $class_ids = DB::table('exams')->where('id', $exam_id)->value('class_ids');
        $class = DB::table('classes')
            ->whereIn('id', explode(',', $class_ids))
            ->select('id', 'grade_id')
            ->get();
        //通过年级分组
        $grades = [];
        foreach ($class as $item) {
            $grades[$item->grade_id][] = $item->id;
        }
        //循环每个年级
        foreach ($grades as $class_ids_arr) {
            //查找此年级所有班级的学生的各科成绩
            $score = DB::table('scores')
                ->join('students', 'students.id', '=', 'scores.student_id')
                ->whereIn('students.class_id', $class_ids_arr)
                ->where('scores.exam_id', $exam_id)
                ->select('scores.id', 'scores.student_id', 'scores.subject_id', 'scores.score', 'students.class_id')
                ->orderBy('scores.score', 'desc')
                ->get();
            //通过科目分组
            $subject = [];
            foreach ($score as $item) {
                $subject[$item->subject_id][] = $item;
            }
            //循环每个科目
            foreach ($subject as $val) {
                foreach ($val as $k => $v) {
                    $v->grade_rank = $k + 1;
                    if ($k > 0) {
                        if ($v->score == $val[$k - 1]->score) {
                            $v->grade_rank = $val[$k - 1]->grade_rank;
                        }
                    }
                }
                //写入年级排名
                foreach ($val as $grade_rank) {
                    DB::table('scores')->where('id', $grade_rank->id)->update(['grade_rank' => $grade_rank->grade_rank]);
                }
                
                //通过班级分组
                $classes = [];
                foreach ($val as $item) {
                    $classes[$item->class_id][] = $item;
                }
                //循环每个班级
                foreach ($classes as $v) {
                    foreach ($v as $c_k => $c_v) {
                        $c_v->class_rank = $c_k + 1;
                        if ($c_k > 0) {
                            if ($c_v->score == $v[$c_k - 1]->score) {
                                $c_v->class_rank = $v[$c_k - 1]->class_rank;
                            }
                        }
                    }
                    //写入年级排名
                    foreach ($v as $class_rank) {
                        DB::table('scores')->where('id', $class_rank->id)->update(['class_rank' => $class_rank->class_rank]);
                    }
                }
            }
        }
    }
}

