<?php
namespace App\Http\Controllers;

use App\Http\Requests\PqSubjectRequest;
use App\Models\PollQuestionnaireSubject;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
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
    
    function __construct(PollQuestionnaireSubject $pqs) {
    
        $this->middleware(['auth', 'checkrole']);
        $this->pqs = $pqs;
        
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
                $this->pqs->datatable()
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
        
        $this->authorize(
            'cs', PollQuestionnaireSubject::class
        );
        
        return $this->output();
        
    }
    
    /**
     * 保存题目
     *
     * @param PqSubjectRequest $request
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function store(PqSubjectRequest $request) {
    
        $this->authorize(
            'cs', PollQuestionnaireSubject::class
        );
    
        return $this->result(
            $this->pqs->create($request->all())
        );
        
    }
    
    /**
     * 编辑题目
     * @param $id
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function edit($id) {
        
        $pqs = $this->pqs->find($id);
        $this->authorize('eud', $pqs);
        
        return $this->output([
            'pqs' => $pqs
        ]);
        
    }
    
    /**
     * 更新题目
     *
     * @param PqSubjectRequest $request
     * @param $id
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function update(PqSubjectRequest $request, $id) {
    
        $pqs = $this->pqs->find($id);
        $this->authorize('eud', $pqs);
    
        return $this->result(
            $pqs->update($request->all())
        );
        
    }
    
    /**
     * 删除题目
     *
     * @param $id
     * @return JsonResponse
     * @throws Exception
     */
    public function destroy($id) {
        
        $pqs = $this->pqs->find($id);
        $this->authorize('eud', $pqs);
        
        return $this->result(
            $pqs->remove($id)
        );
        
    }
    
}
