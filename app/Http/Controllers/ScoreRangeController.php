<?php
namespace App\Http\Controllers;

use App\Http\Requests\ScoreRangeRequest;
use App\Models\ScoreRange;
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
    
    protected $sr;
    
    /**
     * ScoreRangeController constructor.
     * @param ScoreRange $sr
     */
    function __construct(ScoreRange $sr) {
        
        $this->middleware(['auth']);
        $this->approve($this->sr = $sr);
        
    }
    
    /**
     * 成绩统计项列表
     *
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function index() {
        
        return Request::get('draw')
            ? response()->json($this->sr->index())
            : $this->output();
        
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
     * @throws Throwable
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
     * @throws Throwable
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
        
        return Request::method() == 'POST'
            ? $this->sr->stat()
            : $this->output();
        
    }
    
}