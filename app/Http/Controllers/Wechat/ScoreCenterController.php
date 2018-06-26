<?php
namespace App\Http\Controllers\Wechat;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\Score;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Throwable;

/**
 * 微信端成绩
 *
 * Class ScoreController
 * @package App\Http\Controllers
 */
class ScoreCenterController extends Controller {
    
    protected $score, $exam;
    
    /**
     * MessageCenterController constructor.
     * @param Score $score
     * @param Exam $exam
     */
    public function __construct(Score $score, Exam $exam) {
        
        $this->middleware('wechat');
        $this->score = $score;
        $this->exam = $exam;
        
    }
    
    /**
     * 微信端成绩
     *
     * @return string
     * @throws Throwable
     */
    public function index() {
        
        return $this->score->wIndex();
        
    }
    
    /**
     * 考试详情
     *
     * @return array|Factory|JsonResponse|View|null|string|
     */
    public function detail() {
        
        return $this->score->detail();
        
    }
    
    /**
     * 图表成绩详情
     *
     * @return bool|JsonResponse
     */
    public function graph() {
        
        return $this->score->graph();
        
    }
    
    /**
     * 微信 教师端成绩分析
     *
     * @return string
     */
    public function analyze() {
        
        return $this->score->analyze();
        
    }
    
    /**
     * 返回指定学生指定考试的综合成绩分析结果
     *
     * @return Factory|View|string
     */
    public function stat() {
        
        return $this->score->wStat();
        
    }
    
}