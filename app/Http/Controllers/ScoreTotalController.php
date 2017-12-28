<?php
namespace App\Http\Controllers;

use App\Models\ScoreTotal;
use App\Models\Subject;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Request;
use Throwable;

/**
 * 总成绩
 *
 * Class ScoreTotalController
 * @package App\Http\Controllers
 */
class ScoreTotalController extends Controller {
    
    protected $scoreTotal;
    protected $subject;
    
    function __construct(ScoreTotal $score_total, Subject $subject) {
    
        $this->middleware(['auth', 'checkrole']);
        
    }
    
    /**
     * 总成绩列表
     *
     * @return JsonResponse
     * @throws Throwable
     */
    public function index() {
        
        if (Request::get('draw')) {
            return response()->json(
                ScoreTotal::datatable()
            );
        }
        
        return $this->output();
        
    }
    
    /**
     * 总成绩详情
     *
     * @param $id
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function show($id) {
        
        $st = ScoreTotal::find($id);
        if (!$st) { return $this->notFound(); }
        
        return $this->output([
            'score_total' => $st,
            'studentname' => $st->student->user->realname,
            'subjects'    => Subject::subjects($st->subject_ids),
            'na_subjects' => Subject::subjects($st->na_subject_ids),
        ]);
        
    }
    
    /**
     * 总成绩统计
     *
     * @param $examId
     * @return JsonResponse
     * @throws Exception
     */
    public function statistics($examId) {
        
        return $this->result(ScoreTotal::statistics($examId));
        
    }
    
}
