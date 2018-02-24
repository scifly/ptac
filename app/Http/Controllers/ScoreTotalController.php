<?php
namespace App\Http\Controllers;

use App\Helpers\HttpStatusCode;
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
    
    protected $st;
    protected $subject;
    
    function __construct(ScoreTotal $st, Subject $subject) {
    
        $this->middleware(['auth', 'checkrole']);
        $this->st = $st;
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
            return response()->json(
                $this->st->datatable()
            );
        }
        
        return $this->output();
        
    }
    
    /**
     * 删除总成绩
     *
     * @param $id
     * @return JsonResponse
     * @throws Exception
     */
    public function destroy($id) {
        
        $st = ScoreTotal::find($id);
        abort_if(!$st, HttpStatusCode::NOT_FOUND);

        return $this->result(
            $st->remove($id)
        );
        
    }

    /**
     * 总成绩统计
     *
     * @param $examId
     * @return JsonResponse
     * @throws Exception
     */
    public function statistics($examId) {
    
         return $this->result(
             ScoreTotal::statistics($examId)
         );
    
    }
    
}
