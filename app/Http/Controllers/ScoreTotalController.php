<?php
namespace App\Http\Controllers;

use App\Models\ScoreTotal;
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
    
    protected $st;
    
    /**
     * ScoreTotalController constructor.
     * @param ScoreTotal $st
     */
    function __construct(ScoreTotal $st) {
        
        $this->middleware(['auth', 'checkrole']);
        $this->st = $st;
        $this->approve($st);
        
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
                $this->st->index()
            );
        }
        
        return $this->output();
        
    }
    
    /**
     * 总成绩统计
     *
     * @param $examId
     * @return JsonResponse
     * @throws Exception
     */
    public function stat($examId) {
        
        return $this->result(
            $this->st->stat($examId)
        );
        
    }
    
}
