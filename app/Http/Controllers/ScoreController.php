<?php

namespace App\Http\Controllers;

use App\Http\Requests\ScoreRequest;
use App\Models\Score;
use App\Models\User;
use Illuminate\Support\Facades\Request;

class ScoreController extends Controller {
    protected $score;
    protected $message;

    function __construct(Score $score) {
        $this->score = $score;
        $this->message = [
            'statusCode' => 200,
            'message' => ''
        ];
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
            'dialog' => true
        ]);

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
     * @return \Illuminate\Http\Response
     * @internal param \Illuminate\Http\Request $request
     */
    public function store(ScoreRequest $request) {
        $res = $this->score->create($request->except('_token'));
        if (!$res) {
            $this->message['statusCode'] = 202;
            $this->message['message'] = '添加失败';
        } else {
            $this->message['statusCode'] = 200;
            $this->message['message'] = '添加成功!';
        }
        return response()->json($this->message);
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
        $res = $this->score->findOrFail($id)->update($request->all());
        if (!$res) {
            $this->message['statusCode'] = 202;
            $this->message['message'] = '更新失败';
        } else {
            $this->message['statusCode'] = 200;
            $this->message['message'] = '更新成功';
        }
        return response()->json($this->message);
    }

    /**
     *删除指定成绩记录
     *
     * @param $id
     * @return \Illuminate\Http\Response
     * @internal param Score $score
     */
    public function destroy($id) {
        $res = $this->score->findOrFail($id)->delete();
        if (!$res) {
            $this->message['statusCode'] = 202;
            $this->message['message'] = '删除失败';
        } else {
            $this->message['statusCode'] = 200;
            $this->message['message'] = '删除成功';
        }
        return response()->json($this->message);
    }
}
