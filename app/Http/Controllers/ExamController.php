<?php
namespace App\Http\Controllers;

use App\Http\Requests\ExamRequest;
use App\Models\Exam;
use App\Models\Squad;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Request;
use Throwable;

/**
 * 考试
 *
 * Class ExamController
 * @package App\Http\Controllers
 */
class ExamController extends Controller {
    
    protected $exam;
    protected $squad;
    
    function __construct(Exam $exam, Squad $squad) {
    
        $this->middleware(['auth']);
        $this->exam = $exam;
        $this->squad = $squad;
        
    }
    
    /**
     * 考试列表
     *
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function index() {
        
        if (Request::get('draw')) {
            return response()->json($this->exam->datatable());
        }
        
        return $this->output(__METHOD__);
        
    }
    
    /**
     * 创建考试
     *
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function create() {
        
        return $this->output(__METHOD__);
        
    }
    
    /**
     * 保存考试
     *
     * @param ExamRequest $request
     * @return JsonResponse
     */
    public function store(ExamRequest $request) {
        
        return $this->exam->create($request->all()) ? $this->succeed() : $this->fail();
        
    }
    
    /**
     * 考试详情
     *
     * @param $id
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function show($id) {
        
        $exam = $this->exam->find($id);
        if (!$exam) { return $this->notFound(); }
        
        return $this->output(__METHOD__, [
            'exam'     => $exam,
            'classes'  => $this->exam->classes($exam->class_ids),
            'subjects' => $this->exam->subjects(),
        ]);
        
    }
    
    /**
     * 编辑考试
     *
     * @param $id
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function edit($id) {
        $exam = $this->exam->find($id);
        if (!$exam) { return $this->notFound(); }
        
        return $this->output(__METHOD__, [
            'exam'             => $exam,
            'selectedClasses'  => $this->exam->classes($exam->class_ids),
            'selectedSubjects' => $this->exam->subjects($exam->subject_ids),
        ]);
    }
    
    /**
     * 更新考试
     *
     * @param ExamRequest $request
     * @param $id
     * @return JsonResponse
     */
    public function update(ExamRequest $request, $id) {
        
        $exam = $this->exam->find($id);
        if (!$exam) {
            return $this->notFound();
        }
        
        return $exam->update($request->all()) ? $this->succeed() : $this->fail();
        
    }
    
    /**
     * 删除考试
     *
     * @param $id
     * @return JsonResponse
     * @throws Exception
     */
    public function destroy($id) {
        
        $exam = $this->exam->find($id);
        if (!$exam) {
            return $this->notFound();
        }
        
        return $exam->delete() ? $this->succeed() : $this->fail();
        
    }
    
}
