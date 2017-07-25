<?php

namespace App\Http\Controllers;

use App\Models\Score;
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
        return view('score.index', ['js' => 'js/score/index.js']);

    }


    /**
     * 显示创建成绩记录的表单
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        return view('score.create', ['js' => 'js/score/create.js']);
    }

    /**
     * 保存新创建的成绩记录
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store() {
        return response()->json(['statusCode' => 200, 'Message' => 'nailed it!']);
    }

    /**
     * 显示成绩记录详情
     *
     * @param  \App\Models\Score $score
     * @return \Illuminate\Http\Response
     */
    public function show() {
        //find the record by id
        //return view('corp.show', ['corp' => $corp]);
    }

    /**
     * 显示编辑成绩记录的表单
     *
     * @param  \App\Models\Score $score
     * @return \Illuminate\Http\Response
     */
    public function edit() {
        return view('score.edit', ['js' => 'js/score/edit.js']);
    }

    /**
     * 更新指定成绩记录
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Models\Score $score
     * @return \Illuminate\Http\Response
     */
    public function update() {
        // find the record by id
        // update the record with the request data
        return response()->json([]);
    }

    /**
     *删除指定成绩记录
     *
     * @param  \App\Models\Score $score
     * @return \Illuminate\Http\Response
     */
    public function destroy() {
        return response()->json([]);
    }
}
