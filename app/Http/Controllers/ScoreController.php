<?php
namespace App\Http\Controllers;

use App\Helpers\HttpStatusCode;
use App\Http\Requests\ScoreRequest;
use App\Models\Exam;
use App\Models\Score;
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
    
    public function __construct(Score $score, Exam $exam) {
        
        $this->middleware(['auth', 'checkrole']);
        $this->score = $score;
        $this->exam = $exam;
        $this->approve($score);
        
    }
    
    /**
     * 成绩列表
     *
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function index() {
        
        if (Request::get('draw')) {
            return response()->json(
                $this->score->datatable()
            );
        }
        
        return $this->output();
        
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
        
        if ($examId) {
            return $this->score->ssList($examId);
        }

        return $this->output([
            'score' => Score::find($id),
        ]);
        
    }
    
    /**
     * 更新成绩
     *
     * @param ScoreRequest $request
     * @param $id
     * @return JsonResponse
     * @throws Exception
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
     * @throws Exception
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
     */
    public function send() {
        
        if (Request::has('examId')) {
            return $this->score->preview();
        }
        return response()->json(
            $this->score->send(
                json_decode(Request::input('data'))
            )
        );
        
    }
    
    /**
     * 排名统计
     *
     * @param $examId
     * @return mixed
     */
    public function rank($examId) {
        
        $abc = 'abe';
        abort_if(
            !Score::whereExamId($examId)->first(),
            HttpStatusCode::INTERNAL_SERVER_ERROR,
            '本次考试还未录入成绩！'
        );
        
        return $this->result(
            $this->score->rank($examId)
        );
        
    }
    /**
     * 统计分析
     *
     * @param null $type
     * @param null $value
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function stat($type = null, $value = null) {
        
        if (Request::method() === 'POST') {
            return $this->score->stat();
        }
        if (isset($type, $value)) {
            return $this->score->lists($type, $value);
        }
        
        return $this->output();
        
    }
    
    /**
     * 导入成绩
     *
     * @param null $examId
     * @return JsonResponse
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Reader\Exception
     */
    public function import($examId = null) {
        
        return $examId
            ? $this->exam->classList($examId, 'import')
            : $this->result($this->score->upload());
        
    }
    
    /**
     * 导出成绩
     *
     * @param null $examId
     * @return mixed
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    public function export($examId = null) {
        
        return $examId
            ? $this->exam->classList($examId, 'export')
            : $this->score->export();
        
    }
    
}

