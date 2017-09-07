<?php

namespace App\Http\Controllers;

use App\Http\Requests\ScoreRequest;
use App\Models\Score;
use Illuminate\Support\Facades\Request;

/**
 * 成绩
 *
 * Class ScoreController
 * @package App\Http\Controllers
 */
class ScoreController extends Controller {
    
    protected $score;
    
    function __construct(Score $score) { $this->score = $score; }
    
    /**
     * 显示成绩列表
     *
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public function index() {
        
        if (Request::get('draw')) {
            return response()->json($this->score->datatable());
        }
        return $this->output(__METHOD__);
        
    }
    
    /**
     * 录入成绩
     *
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public function create() {
        
        return $this->output(__METHOD__);
        
    }
    
    /**
     * 保存成绩
     *
     * @param ScoreRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(ScoreRequest $request) {
        
        return $this->score->create($request->all()) ? $this->succeed() : $this->fail();
        
    }
    
    /**
     * 成绩详情
     *
     * @param $id
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public function show($id) {
        
        $score = $this->score->find($id);
        if (!$score) { return $this->notFound(); }
        return $this->output(__METHOD__, [
            'score' => $score,
            'studentName' => $score->student->user->realname
        ]);
        
    }
    
    /**
     * 修改成绩
     *
     * @param $id
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public function edit($id) {
        
        $score = $this->score->find($id);
        if (!$score) { return $this->notFound(); }
        return $this->output(__METHOD__, [
            'score' => $score,
            'studentName' => $score->student->user->realname
        ]);
        
    }
    
    /**
     * 更新成绩
     *
     * @param ScoreRequest $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(ScoreRequest $request, $id) {
        
        $score = $this->score->find($id);
        if (!$score) { return $this->notFound(); }
        return $score->update($request->all()) ? $this->succeed() : $this->fail();
        
    }
    
    /**
     * 删除成绩
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id) {
        
        $score = $this->score->find($id);
        if (!$score) { return $this->notFound(); }
        return $score->delete() ? $this->succeed() : $this->fail();
    
    }

    /**
     * 统计成绩排名
     *
     * @param $examId
     * @return \Illuminate\Http\JsonResponse
     */
    public function statistics($examId) {
        
        return $this->score->statistics($examId) ? $this->succeed() : $this->fail();
        
    }
    
}

