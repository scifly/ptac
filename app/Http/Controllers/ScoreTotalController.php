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
    
        $this->middleware(['auth']);
        $this->scoreTotal = $score_total;
        $this->subject = $subject;
        
    }
    
    /**
     * 总成绩列表
     *
     * @return JsonResponse
     * @throws Throwable
     */
    public function index() {
        
        if (Request::get('draw')) {
            return response()->json($this->scoreTotal->datatable());
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
        
        $scoreTotal = $this->scoreTotal->find($id);
        if (!$scoreTotal) {
            return $this->notFound();
        }
        return $this->output([
            'score_total' => $scoreTotal,
            'studentname' => $scoreTotal->student->user->realname,
            'subjects'    => $this->subject->subjects($scoreTotal->subject_ids),
            'na_subjects' => $this->subject->subjects($scoreTotal->na_subject_ids),
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
        
        return $this->scoreTotal->statistics($examId) ? $this->succeed() : $this->fail();
        
    }
    
}
