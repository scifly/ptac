<?php

namespace App\Http\Controllers;

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
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\View\View
     */
    public function index() {

        if (Request::get('draw')) {
            return response()->json($this->score_total->datatable());
        }
        return $this->output(__METHOD__);

    }

    /**
     * 显示总成绩记录详情
     *
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show($id) {
        $score_total = $this->score_total->find($id);
        if (!$score_total) { return $this->notFound(); }
        $subjects = Subject::whereIn('id', explode(',', $score_total['subject_ids']))->get(['name']);
        $na_subjects = Subject::whereIn('id', explode(',', $score_total['na_subject_ids']))->get(['name']);

        return $this->output(__METHOD__, [
            'score_total' => $score_total,
            'studentname' => $score_total->student->user->realname,
            'subjects' => $subjects,
            'na_subjects' => $na_subjects
        ]);

    }

    /**
     * 总成绩统计
     *
     * @param $exam_id
     */
    public function statistics($exam_id){
        return $this->score_total->statistics($exam_id) ? $this->succeed() : $this->fail();
    }

}
