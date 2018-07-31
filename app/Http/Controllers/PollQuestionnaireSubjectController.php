<?php
namespace App\Http\Controllers;

use App\Http\Requests\PqSubjectRequest;
use App\Models\PollQuestionnaireSubject;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Request;
use Throwable;

/**
 * 问卷题目
 *
 * Class PqSubjectController
 * @package App\Http\Controllers
 */
class PollQuestionnaireSubjectController extends Controller {
    
    protected $pqs;
    
    /**
     * PollQuestionnaireSubjectController constructor.
     * @param PollQuestionnaireSubject $pqs
     */
    function __construct(PollQuestionnaireSubject $pqs) {
        
        $this->middleware(['auth', 'checkrole']);
        $this->pqs = $pqs;
        $this->approve($pqs);
        
    }
    
    /**
     * 题目列表
     *
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function index() {
        
        if (Request::get('draw')) {
            return response()->json(
                $this->pqs->index()
            );
        }
        
        return $this->output();
        
    }
    
    /**
     * 创建题目
     *
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function create() {
        
        return $this->output();
        
    }
    
    /**
     * 保存题目
     *
     * @param PqSubjectRequest $request
     * @return JsonResponse
     */
    public function store(PqSubjectRequest $request) {
        
        return $this->result(
            $this->pqs->store(
                $request->all()
            )
        );
        
    }
    
    /**
     * 编辑题目
     * @param $id
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function edit($id) {
        
        return $this->output([
            'pqs' => $this->pqs->find($id),
        ]);
        
    }
    
    /**
     * 更新题目
     *
     * @param PqSubjectRequest $request
     * @param $id
     * @return JsonResponse
     */
    public function update(PqSubjectRequest $request, $id) {
        
        return $this->result(
            $this->pqs->modify(
                $request->all(), $id
            )
        );
        
    }
    
    /**
     * 删除题目
     *
     * @param $id
     * @return JsonResponse
     * @throws Throwable
     */
    public function destroy($id) {
        
        return $this->result(
            $this->pqs->remove($id)
        );
        
    }
    
}
