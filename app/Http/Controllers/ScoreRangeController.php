<?php
namespace App\Http\Controllers;

use App\Http\Requests\ScoreRangeRequest;
use App\Models\ScoreRange;
use App\Models\Subject;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request as HttpRequest;
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
    
    protected $scoreRange;
    protected $subject;
    
    function __construct(ScoreRange $scoreRange, Subject $subject) {
    
        $this->middleware(['auth']);
        $this->scoreRange = $scoreRange;
        $this->subject = $subject;
        
    }
    
    /**
     * 成绩统计项列表
     *
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function index() {
        
        if (Request::get('draw')) {
            return response()->json($this->scoreRange->datatable());
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
        $input = $request->all();
        if($input['start_score'] > $input['end_score'] || $input['start_score'] == $input['end_score']){
            return $this->fail('截止分数应该小于起始分数！');
        }
        //添加新数据
        return $this->scoreRange->store($request->all())
            ? $this->succeed() : $this->fail();
        
    }
    
    /**
     * 编辑成绩统计项
     *
     * @param $id
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function edit($id) {
        
        $scoreRange = $this->scoreRange->find($id);
        if (!$scoreRange) { return $this->notFound(); }
        return $this->output([
            'scoreRange'       => $scoreRange,
            'selectedSubjects' => $this->subject->selectedSubjects($scoreRange->subject_ids),
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
        $input = $request->all();
        $scoreRange = $this->scoreRange->find($id);
        if (!$scoreRange) { return $this->notFound(); }
        if($input['start_score'] > $input['end_score'] || $input['start_score'] == $input['end_score']){
            return $this->fail('截止分数应该小于起始分数！');
        }
        
        return $scoreRange->update($request->all()) ? $this->succeed() : $this->fail();
        
    }
    
    /**
     * 删除成绩统计项
     *
     * @param $id
     * @return JsonResponse
     * @throws Exception
     */
    public function destroy($id) {
        
        $scoreRange = $this->scoreRange->find($id);
        if (!$scoreRange) { return $this->notFound(); }
        
        return $scoreRange->delete() ? $this->succeed() : $this->fail();
        
    }
    
    /**
     * 成绩统计项详情
     *
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function showStatistics() {
        
        return $this->output();
        
    }
    
    /**
     * 按统计项进行统计
     *
     * @param HttpRequest $request
     * @return JsonResponse
     */
    public function statistics(HttpRequest $request) {
        
        return $this->scoreRange->statistics($request->all());
        
    }
    
}
