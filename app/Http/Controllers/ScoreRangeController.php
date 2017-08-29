<?php

namespace App\Http\Controllers;

use App\Http\Requests\ScoreRangeRequest;
use App\Models\ScoreRange;
use App\Models\Subject;
use Illuminate\Support\Facades\Request;
use Illuminate\Http\Request as HttpRequest;

class ScoreRangeController extends Controller
{
    protected $scoreRange;
    protected $subject;

    function __construct(ScoreRange $scoreRange, Subject $subject) {
        $this->scoreRange = $scoreRange;
        $this->subject = $subject;
    }

    /**
     * 显示成绩统计项列表
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (Request::get('draw')) {
            return response()->json($this->scoreRange->datatable());
        }
        return $this->output(__METHOD__);
    }

    /**
     * 显示创建成绩统计项的表单
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return $this->output(__METHOD__);
    }

    /**
     * 保存新创建的成绩统计项
     *
     * @param ScoreRangeRequest $request
     * @return \Illuminate\Http\Response
     * @internal param \Illuminate\Http\Request|Request $request
     */
    public function store(ScoreRangeRequest $request)
    {
        //添加新数据
        $score_range = $request->all();
        $score_range['subject_ids'] = implode(',',$score_range['subject_ids']);
        if ($this->scoreRange->existed($request)) {
            return $this->fail('已经有此记录');
        }
        return $this->scoreRange->create($score_range) ? $this->succeed() : $this->fail();
    }

    /**
     * 显示指定的成绩统计项
     *
     * @param $id
     * @return \Illuminate\Http\Response
     * @internal param ScoreRange $scoreRange
     */
    public function show($id)
    {
        $scoreRange = $this->scoreRange->find($id);
        if (!$scoreRange) { return $this->notFound(); }

        $subjectsArr = explode(',', $scoreRange['subject_ids']);
        $str = '';
        foreach ($subjectsArr as $val){
            $str .= ',' . $this->subject->find($val)->name;
        }
        $scoreRange['subject_ids'] = substr($str,1);

        return $this->output(__METHOD__, ['scoreRange' => $scoreRange]);
    }

    /**
     * 显示编辑指定成绩统计项的表单
     *
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit($id)
    {
        $scoreRange = $this->scoreRange->find($id);
        if (!$scoreRange) { return $this->notFound(); }
        return $this->output(__METHOD__, ['scoreRange' => $scoreRange]);
    }


    /**
     * 更新指定的成绩统计项
     *
     * @param ScoreRangeRequest $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(ScoreRangeRequest $request, $id)
    {
        $scoreRange = $this->scoreRange->find($id);

        if (!$scoreRange) { return $this->notFound(); }
        if ($this->$scoreRange->existed($request, $id)) {
            return $this->fail('已经有此记录');
        }

        $score_range = $request->all();
        $score_range['subject_ids'] = implode(',',$score_range['subject_ids']);

        return $app->update($score_range) ? $this->succeed() : $this->fail();
    }

    /**
     * 删除指定的成绩统计项
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $scoreRange =$this->scoreRange->find($id);
        if (!$scoreRange) { return $this->notFound(); }
        return $scoreRange->delete() ? $this->succeed() : $this->fail();
    }

    /**
     * 显示成绩统计页面
     *
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public function showStatistics(){

        return $this->output(__METHOD__);
    }


    /**
     * 展示统计成绩段
     *
     * @param HttpRequest $request
     * @return \Illuminate\Http\JsonResponse
     */

    public function statistics(HttpRequest $request) {

        return $this->scoreRange->statistics($request->all());
    }

}
