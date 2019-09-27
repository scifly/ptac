<?php
namespace App\Http\Controllers;

use App\Http\Requests\ExamRequest;
use App\Models\Exam;
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
    
    /**
     * ExamController constructor.
     * @param Exam $exam
     */
    function __construct(Exam $exam) {
        
        $this->middleware(['auth', 'checkrole']);
        $this->exam = $exam;
        $this->approve($exam);
        
    }
    
    /**
     * 考试列表
     *
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function index() {
        
        return Request::get('draw')
            ? response()->json($this->exam->index())
            : $this->output();
        
    }
    
    /**
     * 创建考试
     *
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function create() {
        
        return $this->output();
        
    }
    
    /**
     * 保存考试
     *
     * @param ExamRequest $request
     * @return JsonResponse
     */
    public function store(ExamRequest $request) {
        
        return $this->result(
            $this->exam->store(
                $request->all()
            )
        );
        
    }
    
    /**
     * 编辑考试
     *
     * @param $id
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function edit($id) {
        
        return $this->output([
            'exam' => Exam::find($id),
        ]);
    }
    
    /**
     * 更新考试
     *
     * @param ExamRequest $request
     * @param $id
     * @return JsonResponse
     * @throws Throwable
     */
    public function update(ExamRequest $request, $id) {
        
        return $this->result(
            $this->exam->modify(
                $request->all(), $id
            )
        );
        
    }
    
    /**
     * 删除考试
     *
     * @param $id
     * @return JsonResponse
     * @throws Throwable
     */
    public function destroy($id) {
        
        return $this->result(
            $this->exam->remove($id)
        );
        
    }
    
}
