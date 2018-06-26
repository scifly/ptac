<?php
namespace App\Http\Controllers;

use App\Http\Requests\ScoreRangeRequest;
use App\Models\ScoreRange;
use App\Models\Subject;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Request;
use Throwable;

/**
 * 成绩统计项
 *
 * Class ScoreRangeController
 * @package App\Http\Controllers
 */
class ScoreRangeController extends Controller {
    
    protected $sr, $subject;
    
    function __construct(ScoreRange $sr, Subject $subject) {
        
        $this->middleware(['auth']);
        $this->sr = $sr;
        $this->subject = $subject;
        $this->approve($sr);
        
    }
    
    /**
     * 成绩统计项列表
     *
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function index() {
        
        if (Request::get('draw')) {
            return response()->json(
                $this->sr->index()
            );
        }
        
        return $this->output();
        
    }
    
    /**
     * 创建成绩统计项
     *
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function create() {
        
        return $this->output();
        
    }
    
    /**
     * 保存成绩统计项
     *
     * @param ScoreRangeRequest $request
     * @return JsonResponse|Response|string
     */
    public function store(ScoreRangeRequest $request) {
        
        return $this->result(
            $this->sr->store(
                $request->all()
            )
        );
        
    }
    
    /**
     * 编辑成绩统计项
     *
     * @param $id
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function edit($id) {
        
        return $this->output([
            'sr' => $this->sr->find($id),
        ]);
        
    }
    
    /**
     * 更新成绩统计项
     *
     * @param ScoreRangeRequest $request
     * @param $id
     * @return JsonResponse
     */
    public function update(ScoreRangeRequest $request, $id) {
        
        return $this->result(
            $this->sr->modify(
                $request->all(), $id
            )
        );
        
    }
    
    /**
     * 删除成绩统计项
     *
     * @param $id
     * @return JsonResponse
     * @throws Exception
     */
    public function destroy($id) {
        
        return $this->result(
            $this->sr->remove($id)
        );
        
    }
    
    /**
     * 按统计项进行统计
     *
     * @return JsonResponse
     * @throws Throwable
     */
    public function stat() {
        
        if (Request::method() == 'POST') {
            return $this->sr->stat();
        }
        
        return $this->output();
        
    }
    
}