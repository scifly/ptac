<?php
namespace App\Http\Controllers;

use App\Http\Requests\ScoreRangeRequest;
use App\Models\ScoreRange;
use App\Models\Subject;
use Illuminate\Http\Request as HttpRequest;
use Illuminate\Support\Facades\Request;

/**
 * 成绩统计项
 *
 * Class ScoreRangeController
 * @package App\Http\Controllers
 */
class ScoreRangeController extends Controller {

    protected $scoreRange;
    protected $subject;

    function __construct(ScoreRange $scoreRange, Subject $subject) {

        $this->scoreRange = $scoreRange;
        $this->subject = $subject;

    }

    /**
     * 成绩统计项列表
     *
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public function index() {

        if (Request::get('draw')) {
            return response()->json($this->scoreRange->datatable());
        }

        return $this->output(__METHOD__);

    }

    /**
     * 创建成绩统计项
     *
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public function create() {

        return $this->output(__METHOD__, [
            'subjects' => $this->subject->subjects(1),
        ]);

    }

    /**
     * 保存成绩统计项
     *
     * @param ScoreRangeRequest $request
     * @return \Illuminate\Http\Response
     * @internal param \Illuminate\Http\Request|Request $request
     */
    public function store(ScoreRangeRequest $request) {

        //添加新数据
        return $this->scoreRange->store($request->all())
            ? $this->succeed() : $this->fail();

    }

    /**
     * 成绩统计项详情
     *
     * @param $id
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public function show($id) {

        $scoreRange = $this->scoreRange->find($id);
        if (!$scoreRange) {
            return $this->notFound();
        }
        $subjectsArr = explode(',', $scoreRange['subject_ids']);
        $str = '';
        foreach ($subjectsArr as $val) {
            $str .= ',' . $this->subject->find($val)->name;
        }
        $scoreRange['subject_ids'] = substr($str, 1);

        return $this->output(__METHOD__, ['scoreRange' => $scoreRange]);

    }

    /**
     * 编辑成绩统计项
     *
     * @param $id
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public function edit($id) {

        $scoreRange = $this->scoreRange->find($id);
        if (!$scoreRange) {
            return $this->notFound();
        }

        return $this->output(__METHOD__, [
            'scoreRange'       => $scoreRange,
            'subjects'         => $this->subject->subjects(1),
            'selectedSubjects' => $this->subject->selectedSubjects($scoreRange->subject_ids),
        ]);

    }

    /**
     * 更新成绩统计项
     *
     * @param ScoreRangeRequest $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(ScoreRangeRequest $request, $id) {

        $scoreRange = $this->scoreRange->find($id);
        if (!$scoreRange) {
            return $this->notFound();
        }
        $score_range = $request->all();
        $score_range['subject_ids'] = implode(',', $score_range['subject_ids']);

        return $scoreRange->update($score_range) ? $this->succeed() : $this->fail();

    }

    /**
     * 删除成绩统计项
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id) {

        $scoreRange = $this->scoreRange->find($id);
        if (!$scoreRange) {
            return $this->notFound();
        }

        return $scoreRange->delete() ? $this->succeed() : $this->fail();

    }

    /**
     * 成绩统计项详情
     *
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public function showStatistics() {

        return $this->output(__METHOD__);

    }

    /**
     * 按统计项进行统计
     *
     * @param HttpRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function statistics(HttpRequest $request) {

        return $this->scoreRange->statistics($request->all());

    }

}
