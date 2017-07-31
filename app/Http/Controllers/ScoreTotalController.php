<?php

namespace App\Http\Controllers;

use App\Http\Requests\ScoreTotalRequest;
use App\Models\ScoreTotal;
use App\Models\Subject;
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
            'scoreTotalCreateEditJs' =>true
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
        $data['subject_ids'] = implode(',',$data['subject_ids']);
        $data['na_subject_ids'] = implode(',',$data['na_subject_ids']);
        if ($this->score_total->create($data)) {
            $this->result['statusCode'] = self::HTTP_STATUSCODE_OK;
            $this->result['message'] = self::MSG_CREATE_OK;
        } else {
            $this->result['statusCode'] = self::HTTP_STATUSCODE_INTERNAL_SERVER_ERROR;
            $this->result['message'] = '';
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
        return view('score_total.show', ['score_total' => $this->score_total->findOrFail($id)]);
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
        $data['subject_ids'] = implode(',',$data['subject_ids']);
        $data['na_subject_ids'] = implode(',',$data['na_subject_ids']);
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
            $this->result['message'] = '';
        }
        return response()->json($this->result);
    }

    /**
     * @param $id
     * @return array
     */
    public function getExamSubjects($id) {
        $exam_subject_ids = explode(',', $this->score_total->whereId($id)->first()->exam->subject_ids);
        $exam_temp_subjects = Subject::whereIn('id', $exam_subject_ids)->get(['id','name']);
        if ($exam_temp_subjects) {
            return response()->json(['statusCode' => 200, 'exam_subjects' => $exam_temp_subjects]);
        }else{
            return response()->json(['statusCode' => 500, 'message' => '查询失败!']);
        }
    }
}
