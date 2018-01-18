<?php
namespace App\Http\Controllers\Wechat;

use App\Http\Controllers\Controller;
use App\Http\Requests\ScoreRequest;
use App\Models\Exam;
use App\Models\Score;
use App\Models\Squad;
use App\Models\Student;
use App\Models\Subject;
use Excel;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Request;
use Maatwebsite\Excel\Classes\PHPExcel;
use Maatwebsite\Excel\Files\ExcelFile;
use Throwable;

/**
 * 微信端成绩
 *
 * Class ScoreController
 * @package App\Http\Controllers
 */
class ScoreController extends Controller {


    protected $score;

    /**
     * MessageCenterController constructor.
     * @param Score $score
     */
    public function __construct(Score $score) {
        // $this->middleware();
        $this->score = $score;

    }
    /**
     * 成绩详情
     *
     * @param $id
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function show($id) {

        $score = Score::find($id);
        if (!$score) { return $this->notFound(); }

        return $this->output([
            'score'       => $score,
            'studentName' => $score->student->user->realname,
        ]);
        
    }
    
}

