<?php
namespace App\Http\Controllers;

use App\Http\Requests\ScoreRequest;
use App\Models\{Exam, Score};
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Request;
use Throwable;

/**
 * 成绩
 *
 * Class ScoreController
 * @package App\Http\Controllers
 */
class ScoreController extends Controller {
    
    protected $score, $exam;
    
    /**
     * ScoreController constructor.
     * @param Score $score
     * @param Exam $exam
     */
    public function __construct(Score $score, Exam $exam) {
        
        $this->middleware(['auth', 'checkrole']);
        $this->exam = $exam;
        $this->approve($this->score = $score);
        
    }
    
    /**
     * 成绩列表
     *
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function index() {
        
        return Request::get('draw')
            ? response()->json($this->score->index())
            : $this->output();
        
    }
    
    /**
     * 录入成绩
     *
     * @param null $examId
     * @return array|bool|JsonResponse
     * @throws Throwable
     */
    public function create($examId = null) {
        
        return $examId
            ? $this->score->ssList($examId)
            : $this->output();
        
    }
    
    /**
     * 保存成绩
     *
     * @param ScoreRequest $request
     * @return JsonResponse
     */
    public function store(ScoreRequest $request) {
        
        return $this->result(
            $this->score->store(
                $request->all()
            )
        );
        
    }
    
    /**
     * 编辑成绩
     *
     * @param $id
     * @param null $examId
     * @return array|bool|JsonResponse
     * @throws Throwable
     */
    public function edit($id, $examId = null) {
        
        return $examId
            ? $this->score->ssList($examId)
            : $this->output([
                'score' => $this->score->find($id),
            ]);
        
    }
    
    /**
     * 更新成绩
     *
     * @param ScoreRequest $request
     * @param $id
     * @return JsonResponse
     * @throws Throwable
     */
    public function update(ScoreRequest $request, $id = null) {
        
        return $this->result(
            $this->score->modify(
                $request->all(), $id
            )
        );
        
    }
    
    /**
     * 删除成绩
     *
     * @param $id
     * @return JsonResponse
     * @throws Throwable
     */
    public function destroy($id = null) {
        
        return $this->result(
            $this->score->remove($id)
        );
        
    }
    
    /**
     * 发送成绩
     *
     * @return JsonResponse
     * @throws Throwable
     */
    public function send() {
        
        return Request::has('examId')
            ? $this->score->preview()
            : $this->result(
                $this->score->send(json_decode(Request::input('data'))),
                __('messages.score.send_request_submitted')
            );
        
    }
    
    /**
     * 排名统计
     *
     * @param $examId
     * @return mixed
     * @throws Throwable
     */
    public function rank($examId) {
        
        return $this->result(
            $this->score->rank($examId)
        );
        
    }
    
    /**
     * 统计分析
     *
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function stat() {
        
        return Request::method() == 'POST'
            ? (
            Request::has('type')
                ? $this->score->lists()
                : $this->score->stat()
            )
            : $this->output();
        
    }
    
    /**
     * 导入成绩
     *
     * @param null $examId
     * @return JsonResponse
     * @throws Throwable
     */
    public function import($examId = null) {
        
        if ($examId) {
            if (Request::method() == 'POST') {
                $this->score->template($examId);
                $response = $this->exam->classList($examId, 'import');
            } else {
                $this->score->template($examId, Request::input('classId'));
                $response = response()->json();
            }
        } else {
            $response = $this->result(
                $this->score->import(),
                __('messages.import_started'),
                __('messages.file_upload_failed')
            );
        }
        
        return $response;
        
    }
    
    /**
     * 导出成绩
     *
     * @param null $examId
     * @return mixed
     * @throws Exception
     */
    public function export($examId = null) {
        
        return $examId
            ? $this->exam->classList($examId, 'export')
            : $this->score->export();
        
    }
    
}

