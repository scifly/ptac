<?php

namespace App\Http\Controllers;

use App\Http\Requests\ScoreTotalRequest;
use App\Models\Exam;
use App\Models\ScoreTotal;
use App\Models\Subject;
use App\Models\User;
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
//
//   /**
//     * 显示总成绩记录的表单
//     *
//     * @return \Illuminate\Http\Response
//     */
//    public function create() {
//        return view('score_total.create', [
//            'js' => 'js/score_total/create.js',
//            'form' => true,
//        ]);
//    }
//
//    /**
//     * 保存新的总成绩记录
//     * @param ScoreTotalRequest $request
//     * @return \Illuminate\Http\Response
//     * @internal param \Illuminate\Http\Request|Request $request
//     */
//    public function store(ScoreTotalRequest $request) {
//        $data = $request->all();
//        $record = $this->score_total->where('student_id', $data['student_id'])
//            ->where('exam_id', $data['exam_id'])
//            ->first();
//        if ((!empty($record))) {
//            $this->result['statusCode'] = self::HTTP_STATUSCODE_INTERNAL_SERVER_ERROR;
//            $this->result['message'] = '该学生本场考试已有记录';
//        } else {
//            $temp = Exam::whereId($data['exam_id'])->get(['subject_ids'])->first()->toArray();
//            $exam_suject_arr = explode(',', $temp['subject_ids']);
//            $data['na_subject_ids'] = array_diff($exam_suject_arr, $data['subject_ids']);
//            $data['subject_ids'] = implode(',', $data['subject_ids']);
//            $data['na_subject_ids'] = implode(',', $data['na_subject_ids']);
//            if ($this->score_total->create($data)) {
//                $this->result['statusCode'] = self::HTTP_STATUSCODE_OK;
//                $this->result['message'] = self::MSG_CREATE_OK;
//            } else {
//                $this->result['statusCode'] = self::HTTP_STATUSCODE_INTERNAL_SERVER_ERROR;
//                $this->result['message'] = '保存失败';
//            }
//        }
//        return response()->json($this->result);
//    }

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

//    /**
//     * 显示编辑总成绩记录的表单
//     * @param $id
//     * @return \Illuminate\Http\Response
//     */
//    public function edit($id) {
//        $scoreTotal = $this->score_total->findOrFail($id)->toArray();
//        return view('score_total.edit', [
//            'js' => 'js/score_total/edit.js',
//            'scoreTotal' => $scoreTotal,
//            'form' => true,
//        ]);
//
//    }
//
//    /**
//     * 更新指定总成绩记录
//     * @param ScoreTotalRequest $request
//     * @param $id
//     * @return \Illuminate\Http\Response
//     */
//    public function update(ScoreTotalRequest $request, $id) {
//        $data = $request->all();
//        $record = $this->score_total->where('student_id', $data['student_id'])
//            ->where('exam_id', $data['exam_id'])
//            ->first();
//        if (!empty($record) && ($record->id != $id)) {
//            $this->result['statusCode'] = self::HTTP_STATUSCODE_INTERNAL_SERVER_ERROR;
//            $this->result['message'] = '该学生本场考试已有记录';
//            return response()->json($this->result);
//        }
//        if (isset($data['subject_ids']) && isset($data['na_subject_ids'])) {
//            $arr = array_intersect($data['subject_ids'], $data['na_subject_ids']);
//            if (count($arr) !== 0) {
//                $this->result['statusCode'] = self::HTTP_STATUSCODE_INTERNAL_SERVER_ERROR;
//                $this->result['message'] = '计入总成绩科目与未计入总成绩科目有冲突';
//                return response()->json($this->result);
//            }
//        }
//        if (isset($data['subject_ids'])) {
//            $data['subject_ids'] = implode(',', $data['subject_ids']);
//        }
//        if (isset($data['na_subject_ids'])) {
//            $data['na_subject_ids'] = implode(',', $data['na_subject_ids']);
//        }
//        if ($this->score_total->findOrFail($id)->update($data)) {
//            $this->result['statusCode'] = self::HTTP_STATUSCODE_OK;
//            $this->result['message'] = self::MSG_EDIT_OK;
//        } else {
//            $this->result['statusCode'] = self::HTTP_STATUSCODE_INTERNAL_SERVER_ERROR;
//            $this->result['message'] = '更新失败';
//        }
//        return response()->json($this->result);
//    }
//
//    /**
//     * 删除指定总成绩记录
//     * @param $id
//     * @return \Illuminate\Http\Response
//     */
//    public function destroy($id) {
//        if ($this->score_total->findOrFail($id)->delete()) {
//            $this->result['statusCode'] = self::HTTP_STATUSCODE_OK;
//            $this->result['message'] = self::MSG_DEL_OK;
//        } else {
//            $this->result['statusCode'] = self::HTTP_STATUSCODE_INTERNAL_SERVER_ERROR;
//            $this->result['message'] = '删除失败';
//        }
//        return response()->json($this->result);
//    }
//
//    /**
//     * @param $id
//     * @return array
//     */
//    public function getExamSubjects($id) {
//
//        $temp = Exam::whereId($id)->get(['subject_ids'])->first();
//        $exam_subject_ids = explode(',', $temp['subject_ids']);
//        $exam_temp_subjects = Subject::whereIn('id', $exam_subject_ids)->get(['id', 'name'])->toArray();
//        if ($exam_temp_subjects) {
//            return response()->json(['statusCode' => 200, 'exam_subjects' => $exam_temp_subjects]);
//        } else {
//            return response()->json(['statusCode' => 500, 'message' => '查询失败!']);
//        }
//    }
}
