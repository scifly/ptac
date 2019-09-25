<?php
namespace App\Http\Controllers\Wechat;

use App\Http\Controllers\Controller;
use App\Models\{Exam, Score};
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Throwable;

/**
 * 成绩中心
 *
 * Class MarkController
 * @package App\Http\Controllers
 */
class MarkController extends Controller {
    
    static $category = 1; # 微信端控制器
    
    protected $score, $exam;
    
    /**
     * MarkController constructor.
     *
     * @param Score $score
     * @param Exam $exam
     */
    public function __construct(Score $score, Exam $exam) {
        
        $this->middleware(['corp.auth', 'corp.role']);
        $this->score = $score;
        $this->exam = $exam;
        
    }
    
    /**
     * 成绩列表
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
     * @throws Throwable
     */
    public function detail() {
        
        return $this->score->detail();
        
    }
    
    /**
     * 成绩图表
     *
     * @return bool|JsonResponse
     */
    public function graph() {
        
        return $this->score->graph();
        
    }
    
    /**
     * 成绩分析
     *
     * @return string
     * @throws Throwable
     */
    public function analyze() {
        
        return $this->score->analyze();
        
    }
    
    /**
     * 成绩统计
     *
     * @return Factory|View|string
     * @throws Throwable
     */
    public function stat() {
        
        return $this->score->wStat();
        
    }
    
}