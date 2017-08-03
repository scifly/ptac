<?php

namespace App\Http\Controllers;

use App\Http\Requests\ScoreTotalRequest;
use App\Models\Exam;
use App\Models\ScoreTotal;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;

class ScoreTotalController extends Controller {

    protected $score_total;

    function __construct(ScoreTotal $score_total) {
        $this->score_total = $score_total;
    }

    /**
     * 显示总成绩列表
     * @return \Illuminate\Http\Response
     * @internal param null $arg
     * @internal param Request $request
     */
    public function index() {

        if (Request::get('draw')) {
            return response()->json($this->score_total->datatable());
        }
        return view('score_total.index', [
            'js' => 'js/score_total/index.js',
            'dialog' => true,
            'datatable' => true,
        ]);
    }

    /**
     * 显示总成绩记录的表单
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        return view('score_total.create', [
            'js' => 'js/score_total/create.js',
            'form' => true,
        ]);
    }

    /**
     * 保存新的总成绩记录
     * @param ScoreTotalRequest $request
     * @return \Illuminate\Http\Response
     * @internal param \Illuminate\Http\Request|Request $request
     */
    public function store(ScoreTotalRequest $request) {
        $data = $request->all();
        dd($data);
        $record = $this->score_total->where([
            ['student_id', $data['student_id']],
            ['exam_id', $data['exam_id']]
        ])->first();
        if ((!empty($record))) {
            $this->result['statusCode'] = self::HTTP_STATUSCODE_INTERNAL_SERVER_ERROR;
            $this->result['message'] = '该学生本场考试已有记录';
        } else {
            $temp = Exam::whereId($data['exam_id'])->get(['subject_ids'])->first()->toArray();
            $exam_suject_arr = explode(',', $temp['subject_ids']);
            $data['na_subject_ids'] = array_diff($exam_suject_arr, $data['subject_ids']);
            $data['subject_ids'] = implode(',', $data['subject_ids']);
            $data['na_subject_ids'] = implode(',', $data['na_subject_ids']);
            if ($this->score_total->create($data)) {
                $this->result['statusCode'] = self::HTTP_STATUSCODE_OK;
                $this->result['message'] = self::MSG_CREATE_OK;
            } else {
                $this->result['statusCode'] = self::HTTP_STATUSCODE_INTERNAL_SERVER_ERROR;
                $this->result['message'] = '保存失败';
            }
        }
        return response()->json($this->result);
    }

    /**
     * 显示总成绩记录详情
     * @param $id
     * @return \Illuminate\Http\Response
     * @internal param Company $company
     */
    public function show($id) {
        $score_total = $this->score_total->findOrFail($id);
        $studentname = User::whereId($score_total->student->user_id)->get(['realname'])->first();
        $subjects_arr = explode(',', $score_total['subject_ids']);
        $subjects = Subject::whereIn('id', $subjects_arr)->get(['name']);
        $na_subjects_arr = explode(',', $score_total['na_subject_ids']);
        $na_subjects = Subject::whereIn('id', $na_subjects_arr)->get(['name']);

        return view('score_total.show', [
            'score_total' => $score_total,
            'studentname' => $studentname,
            'subjects' => $subjects,
            'na_subjects' => $na_subjects
        ]);
    }

    /**
     * 显示编辑总成绩记录的表单
     * @param $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {
        $scoreTotal = $this->score_total->findOrFail($id)->toArray();
        return view('score_total.edit', [
            'js' => 'js/score_total/edit.js',
            'scoreTotal' => $scoreTotal,
            'form' => true,
        ]);

    }

    /**
     * 更新指定总成绩记录
     * @param ScoreTotalRequest $request
     * @param $id
     * @return \Illuminate\Http\Response
     */
    public function update(ScoreTotalRequest $request, $id) {
        $data = $request->all();
        $record = $this->score_total->where([
            ['student_id', $data['student_id']],
            ['exam_id', $data['exam_id']]
        ])->first();
        if (!empty($record) && ($record->id != $id)) {
            $this->result['statusCode'] = self::HTTP_STATUSCODE_INTERNAL_SERVER_ERROR;
            $this->result['message'] = '该学生本场考试已有记录';
            return response()->json($this->result);
        }
        if (isset($data['subject_ids']) && isset($data['na_subject_ids'])) {
            $arr = array_intersect($data['subject_ids'], $data['na_subject_ids']);
            if (count($arr) !== 0) {
                $this->result['statusCode'] = self::HTTP_STATUSCODE_INTERNAL_SERVER_ERROR;
                $this->result['message'] = '计入总成绩科目与未计入总成绩科目有冲突';
                return response()->json($this->result);
            }
        }
        if (isset($data['subject_ids'])) {$data['subject_ids'] = implode(',', $data['subject_ids']);}
        if (isset($data['na_subject_ids'])) {$data['na_subject_ids'] = implode(',', $data['na_subject_ids']);}
        if ($this->score_total->findOrFail($id)->update($data)) {
            $this->result['statusCode'] = self::HTTP_STATUSCODE_OK;
            $this->result['message'] = self::MSG_EDIT_OK;
        } else {
            $this->result['statusCode'] = self::HTTP_STATUSCODE_INTERNAL_SERVER_ERROR;
            $this->result['message'] = '更新失败';
        }
        return response()->json($this->result);
    }

    /**
     * 删除指定总成绩记录
     * @param $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        if ($this->score_total->findOrFail($id)->delete()) {
            $this->result['statusCode'] = self::HTTP_STATUSCODE_OK;
            $this->result['message'] = self::MSG_DEL_OK;
        } else {
            $this->result['statusCode'] = self::HTTP_STATUSCODE_INTERNAL_SERVER_ERROR;
            $this->result['message'] = '删除失败';
        }
        return response()->json($this->result);
    }

    /**
     * @param $id
     * @return array
     */
    public function getExamSubjects($id) {

        $temp = Exam::whereId($id)->get(['subject_ids'])->first();
        $exam_subject_ids = explode(',', $temp['subject_ids']);
        $exam_temp_subjects = Subject::whereIn('id', $exam_subject_ids)->get(['id', 'name'])->toArray();
        if ($exam_temp_subjects) {
            return response()->json(['statusCode' => 200, 'exam_subjects' => $exam_temp_subjects]);
        } else {
            return response()->json(['statusCode' => 500, 'message' => '查询失败!']);
        }
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
            $data = [];
            //查找此年级参与考试班级的所有学生
            $students = DB::table('students')
                ->whereIn('class_id',$class_ids_arr)
                ->pluck('class_id', 'id');
            //循环学生
            foreach ($students as $student => $class_id){
                $scores = DB::table('scores')
                    ->where(['student_id' => $student, 'exam_id' => $exam_id])
                    ->pluck('score','id');
                $score = 0;
                $na_subject_ids = '';
                $subject_ids = '';
                foreach ($scores as $id => $v){
                    if($v == 0){
                        $na_subject_ids .= ',' . $id;
                    }else{
                        $subject_ids .= ',' . $id;
                    }
                    $score += $v;
                }
                $insert = [
                    'student_id' => $student,
                    'class_id' => $class_id,
                    'exam_id' => intval($exam_id),
                    'score' => $score,
                    'subject_ids' => empty($subject_ids) ? '' : substr($subject_ids,1),
                    'na_subject_ids' => empty($na_subject_ids) ? '' : substr($na_subject_ids,1)
                ];
                $data []=$insert;
            }
            foreach ($data as $key => $row)
            {
                $score_sore[$key]  = $row['score'];
            }
            array_multisort($score_sore, SORT_DESC, $data);


            $grade_ranks = [];
            //计算年级排名
            foreach ($data as $k => $v){
                $v['grade_rank'] = $k+1;
                if($k>0){
                    if($v['score'] == $data[0]['score']){
                        $v['grade_rank'] = $grade_ranks[0]['grade_rank'];
                    }
                }
                $grade_ranks []= $v;
            }
            //通过班级分组
            $classes = [];
            foreach($grade_ranks as $item){
                $classes[$item['class_id']][] = $item;
            }
            //循环每个班级
            foreach ($classes as $v){
                //计算班级排名
                $inserts = [];
                foreach ($v as $c_k => $c_v){
                    $c_v['class_rank'] = $c_k+1;
                    if($c_k>1){
                        if($c_v['score'] == $v[$c_k-1]['score']){
                            $c_v['class_rank'] = $inserts[$c_k-1]['class_rank'];
                        }
                    }
                    unset($c_v['class_id']);
                    $inserts []= $c_v;
                }
                $this->score_total->insert([[
                    'student_id' => 1,
                    'exam_id' => 1,
                    'score' => 123,
                    'subject_ids' => 1,
                    'na_subject_ids' => 1,
                    'class_rank' => 1,
                    'grade_rank' => 1
                ],[
                    'student_id' => 1,
                    'exam_id' => 1,
                    'score' => 123,
                    'subject_ids' => 1,
                    'na_subject_ids' => 1,
                    'class_rank' => 1,
                    'grade_rank' => 1
                ]]);
            }
        }
    }
}
