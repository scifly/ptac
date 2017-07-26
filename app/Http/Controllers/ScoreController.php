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
        //验证
        $input = $request->except('_token');
        //逻辑
        $res = Score::create($input);
        if (!$res) {
            $this->message['statusCode'] = 202;
            $this->message['message'] = 'add filed';
        } else {
            $this->message['statusCode'] = 200;
            $this->message['message'] = 'nailed it!';
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
        // find the record by id
        $score = Score::whereId($id)->first();
        $userid = $score->student->user_id;
        $studentname = User::whereId($userid)->first();
        //$username = User::whereId($userid)->pluck('realname');
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
        $score = Score::whereId($id)->first();
        return view('score.edit', [
            'js' => 'js/score/edit.js',
            'score' => $score
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
        // find the record by id
        // update the record with the request data
        $score = Score::find($id);
        $score->student_id = $request->get('student_id');
        $score->subject_id = $request->get('subject_id');
        $score->exam_id = $request->get('exam_id');
        $score->class_rank = $request->get('class_rank');
        $score->grade_rank = $request->get('grade_rank');
        $score->score = $request->get('score');
        $score->enabled = $request->get('enabled');
        $res = $score->save();
        if (!$res) {
            $this->message['statusCode'] = 202;
            $this->message['message'] = 'add filed';
        } else {
            $this->message['statusCode'] = 200;
            $this->message['message'] = 'nailed it!';
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
        $res = Score::destroy($id);
        if (!$res) {
            $this->message['statusCode'] = 202;
            $this->message['message'] = 'add filed';
        } else {
            $this->message['statusCode'] = 200;
            $this->message['message'] = 'nailed it!';

        }
        return response()->json($this->message);
    }
}
