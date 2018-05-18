<?php
namespace App\Http\Controllers\Wechat;

use App\Helpers\WechatTrait;
use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\Score;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Illuminate\View\View;
use Throwable;

/**
 * 微信端成绩
 *
 * Class ScoreController
 * @package App\Http\Controllers
 */
class ScoreCenterController extends Controller {
    
    use WechatTrait;
    
    protected $score, $exam;
    
    const APP = '成绩中心';
    
    /**
     * MessageCenterController constructor.
     * @param Score $score
     * @param Exam $exam
     */
    public function __construct(Score $score, Exam $exam) {

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
        
        return Auth::id()
            ? $this->score->wIndex()
            : $this->signin(self::APP, Request::url());
        
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
     * 微信 监护人端综合
     */
    public function stat() {
        
        return $this->score->wStat();
        
    }
    
}