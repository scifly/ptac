<?php
namespace App\Http\Controllers;

use App\Helpers\HttpStatusCode;
use App\Http\Requests\ExamRequest;
use App\Models\Exam;
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
    
    protected $exam;
    
    function __construct(Exam $exam) {
    
        $this->middleware(['auth', 'checkrole']);
        $this->exam = $exam;
        
    }
    
    /**
     * 考试列表
     *
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function index() {
        
        if (Request::get('draw')) {
            return response()->json(
                $this->exam->datatable()
            );
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
        
        return $this->result(
            $this->exam->store($request->all())
        );
        
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
        abort_if(!$exam, HttpStatusCode::NOT_FOUND);
        
        return $this->output([
            'exam'     => $exam,
            'classes'  => $exam->classes($exam->class_ids),
            'subjects' => $exam->subjects(),
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
        abort_if(!$exam, HttpStatusCode::NOT_FOUND);
        
        return $this->output([
            'exam'             => $exam,
            'selectedClasses'  => $exam->classes($exam->class_ids),
            'selectedSubjects' => $exam->subjects($exam->subject_ids),
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
        abort_if(!$exam, HttpStatusCode::NOT_FOUND);
        
        return $this->result(
            $exam->modify($request->all(), $id)
        );
        
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
        abort_if(!$exam, HttpStatusCode::NOT_FOUND);
        
        return $this->result($exam->delete());
        
    }
    
}
