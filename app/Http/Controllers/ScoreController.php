<?php

namespace App\Http\Controllers;

use App\Http\Requests\ScoreRequest;
use App\Models\Score;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;

class ScoreController extends Controller {
    protected $score;

    function __construct(Score $score) {
        $this->score = $score;
    }

    /**
     * 显示成绩列表
     * @return \Illuminate\Http\Response
     * @internal param null $arg
     * @internal param Request $request
     */
    public function index() {

        if (Request::get('draw')) {
            return response()->json($this->score->datatable());
        }
        return view('score.index', [
            'js' => 'js/score/index.js',
            'dialog' => true,
            'datatable' => true,
        ]);

    }

    /**
     * 显示创建成绩记录的表单
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        return view('score.create', [
            'js' => 'js/score/create.js',
            'form' => true
        ]);
    }

    /**
     * 保存新创建的成绩记录
     * @param ScoreRequest $request
     * @return \Illuminate\Http\Response
     * @internal param \Illuminate\Http\Request $request
     */
    public function store(ScoreRequest $request) {
        $data = $request->all();
        $record = $this->score->where([
            ['student_id', $data['student_id']],
            ['subject_id', $data['subject_id']],
            ['exam_id', $data['exam_id']]
        ])->first();
        if (!empty($record)) {
            $this->result['statusCode'] = self::HTTP_STATUSCODE_INTERNAL_SERVER_ERROR;
            $this->result['message'] = '该学生本场考试科目已有记录';
        } else {
            if ($this->score->create($data)) {
                $this->result['statusCode'] = self::HTTP_STATUSCODE_OK;
                $this->result['message'] = self::MSG_CREATE_OK;
            } else {
                $this->result['statusCode'] = self::HTTP_STATUSCODE_INTERNAL_SERVER_ERROR;
                $this->result['message'] = '';
            }
        }
        return response()->json($this->result);
    }

    /**
     * 显示成绩记录详情
     *
     * @param $id
     * @return \Illuminate\Http\Response
     * @internal param Score $score
     */
    public function show($id) {
        $score = $this->score->findOrFail($id);
        $studentname = User::whereId($score->student->user_id)->get(['realname'])->first();
        return view('score.show', ['score' => $score, 'studentname' => $studentname]);
    }

    /**
     * 显示编辑成绩记录的表单
     *
     * @param $id
     * @return \Illuminate\Http\Response
     * @internal param Score $score
     */
    public function edit($id) {
        return view('score.edit', [
            'js' => 'js/score/edit.js',
            'score' => $this->score->findOrFail($id),
            'form' => true
        ]);
    }

    /**
     * 更新指定成绩记录
     *
     * @param ScoreRequest|\Illuminate\Http\Request $request
     * @param $id
     * @return \Illuminate\Http\Response
     * @internal param Score $score
     */
    public function update(ScoreRequest $request, $id) {
        $data = $request->all();
        $record = $this->score->where([
            ['student_id', $data['student_id']],
            ['subject_id', $data['subject_id']],
            ['exam_id', $data['exam_id']]
        ])->first();
        if (!empty($record) && ($record->id != $id)) {
            $this->result['statusCode'] = self::HTTP_STATUSCODE_INTERNAL_SERVER_ERROR;
            $this->result['message'] = '该学生本场科目考试已有记录';
        } else {
            if ($this->score->findOrFail($id)->update($request->all())) {
                $this->result['statusCode'] = self::HTTP_STATUSCODE_OK;
                $this->result['message'] = self::MSG_EDIT_OK;
            } else {
                $this->result['statusCode'] = self::HTTP_STATUSCODE_INTERNAL_SERVER_ERROR;
                $this->result['message'] = '';
            }
        }
        return response()->json($this->result);
    }

    /**
     *删除指定成绩记录
     *
     * @param $id
     * @return \Illuminate\Http\Response
     * @internal param Score $score
     */
    public function destroy($id) {
        if ($this->score->findOrFail($id)->delete()) {
            $this->result['statusCode'] = self::HTTP_STATUSCODE_OK;
            $this->result['message'] = self::MSG_DEL_OK;
        } else {
            $this->result['statusCode'] = self::HTTP_STATUSCODE_INTERNAL_SERVER_ERROR;
            $this->result['message'] = '';
        }
        return response()->json($this->result);
    }

    public function statistics($exam_id){
        $class_ids = DB::table('exams')->where('id', $exam_id)->value('class_ids');
        $class = DB::table('classes')
            ->whereIn('id', explode(',', $class_ids))
            ->select('id', 'grade_id')
            ->get();
        //通过年级分组
        $grades = [];
        foreach($class as $item){
            $grades[$item->grade_id][] = $item->id;
        }
        //循环每个年级
        foreach ($grades as $class_ids_arr){
            //查找此年级所有班级的学生的各科成绩
            $score = DB::table('scores')
                ->join('students', 'students.id', '=', 'scores.student_id')
                ->whereIn('students.class_id',$class_ids_arr)
                ->where('scores.exam_id', $exam_id)
                ->select('scores.id', 'scores.student_id', 'scores.subject_id', 'scores.score', 'students.class_id')
                ->orderBy('scores.score', 'desc')
                ->get();
            //通过科目分组
            $subject = [];
            foreach($score as $item){
                $subject[$item->subject_id][] = $item;
            }
            //循环每个科目
            foreach ($subject as $val){
                $ranks = [];
                foreach ($val as $k => $v){
                    $v->grade_rank = $k+1;
                    if($k>1){
                        if($v->score == $ranks[$k-1]->score){
                            $v->grade_rank = $ranks[$k-1]->grade_rank;
                        }
                    }
                    $ranks []= $v;
                }
                //写入年级排名
                foreach ($ranks as $grade_rank){
                    DB::table('scores')->where('id', $grade_rank->id)->update(['grade_rank' => $grade_rank->grade_rank]);
                }

                //通过班级分组
                $classes = [];
                foreach($val as $item){
                    $classes[$item->class_id][] = $item;
                }
                //循环每个班级
                foreach ($classes as $v){
                    $c_ranks = [];
                    foreach ($v as $c_k => $c_v){
                        $c_v->class_rank = $c_k+1;
                        if($c_k>1){
                            if($c_v->score == $c_ranks[$c_k-1]->score){
                                $c_v->class_rank = $c_ranks[$c_k-1]->class_rank;
                            }
                        }
                        $c_ranks []= $c_v;
                    }
                    //写入年级排名
                    foreach ($c_ranks as $class_rank){
                        DB::table('scores')->where('id', $class_rank->id)->update(['class_rank' => $class_rank->class_rank]);
                    }
                }
            }
        }
    }
}

