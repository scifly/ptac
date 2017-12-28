<?php
namespace App\Http\Controllers;

use App\Http\Requests\ExamRequest;
use App\Models\Exam;
use App\Models\Squad;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
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
    
    function __construct() {
    
        $this->middleware(['auth', 'checkrole']);
        
    }
    
    /**
     * 考试列表
     *
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function index() {
        
        if (Request::get('draw')) {
            return response()->json(Exam::datatable());
        }
        
        return $this->output();
        
    }
    
    /**
     * 创建考试
     *
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function create() {
        
        $this->authorize('c', Exam::class);
        
        return $this->output();
        
    }
    
    /**
     * 保存考试
     *
     * @param ExamRequest $request
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function store(ExamRequest $request) {
        
        $this->authorize('c', Exam::class);
        
        return $this->result(Exam::create($request->all()));
        
    }
    
    /**
     * 考试详情
     *
     * @param $id
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function show($id) {
        
        $exam = Exam::find($id);
        // $this->authorize('rud', $exam);
        
        return $this->output([
            'exam'     => $exam,
            'classes'  => Exam::classes($exam->class_ids),
            'subjects' => Exam::subjects(),
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
        
        $exam = Exam::find($id);
        // $this->authorize('rud', $exam);
        
        return $this->output([
            'exam'             => $exam,
            'selectedClasses'  => Exam::classes($exam->class_ids),
            'selectedSubjects' => Exam::subjects($exam->subject_ids),
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
        
        $exam = Exam::find($id);
        // $this->authorize('rud', $exam);
        
        return $this->result($exam->update($request->all()));
        
    }
    
    /**
     * 删除考试
     *
     * @param $id
     * @return JsonResponse
     * @throws Exception
     */
    public function destroy($id) {
        
        $exam = Exam::find($id);
        // $this->authorize('rud', $exam);
        
        return $this->result($exam->delete());
        
    }
    
}
