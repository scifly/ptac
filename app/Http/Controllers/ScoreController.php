<?php

namespace App\Http\Controllers;

use App\Models\Score;
use Illuminate\Support\Facades\Request;

class ScoreController extends Controller
{
    protected $score;

    function __construct(Score $score) { $this->score = $score; }

    /**
     * 显示成绩列表
     * @return \Illuminate\Http\Response
     * @internal param null $arg
     * @internal param Request $request
     */
    public function index() {

        /*if (Request::ajax() && !$arg) {
            return response()->json($this->school->datatable());
        } elseif ($arg) {
            return view('school.index', ['js' => 'js/school/index.js']);
        } else {
            return response()->json($this->school->datatable());
        }*/

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
    public function create()
    {
        //
    }

    /**
     * 保存新创建的成绩记录
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * 显示成绩记录详情
     *
     * @param  \App\Models\Score  $score
     * @return \Illuminate\Http\Response
     */
    public function show(Score $score)
    {
        //
    }

    /**
     * 显示编辑成绩记录的表单
     *
     * @param  \App\Models\Score  $score
     * @return \Illuminate\Http\Response
     */
    public function edit(Score $score)
    {
        //
    }

    /**
     * 更新指定成绩记录
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Score  $score
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Score $score)
    {
        //
    }

    /**
     *删除指定成绩记录
     *
     * @param  \App\Models\Score  $score
     * @return \Illuminate\Http\Response
     */
    public function destroy(Score $score)
    {
        //
    }
}
