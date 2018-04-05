<?php
namespace App\Http\Controllers;

use App\Http\Requests\ScoreRangeRequest;
use App\Models\ScoreRange;
use App\Models\Subject;
use Exception;
use HttpRequest;
use Illuminate\Auth\Access\AuthorizationException;
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
    protected $subject;
    
    function __construct(ScoreRange $sr, Subject $subject) {
        
        $this->middleware(['auth']);
        $this->sr = $sr;
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
            return response()->json(
                $this->sr->datatable()
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
            $this->sr->store($request->all())
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
        
        $sr = $this->sr->find($id);
        $this->authorize('rud', $sr);
        
        return $this->output([
            'sr'               => $sr,
            'selectedSubjects' => $this->subject->selectedSubjects($sr->subject_ids),
        ]);
        
    }
    
    /**
     * 更新成绩统计项
     *
     * @param ScoreRangeRequest $request
     * @param $id
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function update(ScoreRangeRequest $request, $id) {
        
        $sr = $this->sr->find($id);
        $this->authorize('rud', $sr);
        
        return $this->result(
            $sr->update($request->all())
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
        
        $sr = $this->sr->find($id);
        $this->authorize('rud', $sr);
        
        return $this->result(
            $sr->delete()
        );
        
    }
    
    /**
     * 成绩统计项详情
     *
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function show() {
        
        return $this->output();
        
    }
    
    /**
     * 按统计项进行统计
     *
     * @param HttpRequest $request
     * @return JsonResponse
     */
    public function stat(HttpRequest $request) {
        
        return $this->sr->stat($request);
        
    }
    
}
