<?php
namespace App\Http\Controllers;

use App\Http\Requests\PqRequest;
use App\Models\PollQuestionnaire;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Request;
use Throwable;

/**
 * 调查问卷
 *
 * Class PollQuestionnaireController
 * @package App\Http\Controllers
 */
class PollQuestionnaireController extends Controller {
    
    protected $pq;
    
    function __construct(PollQuestionnaire $pq) {
        
        $this->middleware(['auth', 'checkrole']);
        $this->pq = $pq;
        $this->approve($pq);
        
    }
    
    /**
     * 问卷列表
     *
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function index() {
        
        if (Request::get('draw')) {
            return response()->json(
                $this->pq->index()
            );
        }
        
        return $this->output();
        
    }
    
    /**
     * 创建问卷
     *
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function create() {
        
        return $this->output();
        
    }
    
    /**
     * 保存问卷
     *
     * @param PqRequest $request
     * @return JsonResponse
     */
    public function store(PqRequest $request) {
        
        return $this->result(
            $this->pq->store($request->all())
        );
        
    }
    
    /**
     * 问卷详情
     *
     * @param $id
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function show($id) {
        
        return $this->output([
            'pq' => PollQuestionnaire::find($id),
        ]);
        
    }
    
    /**
     * 编辑问卷
     * @param $id
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function edit($id) {
        
        return $this->output([
            'pq' => PollQuestionnaire::find($id),
        ]);
        
    }
    
    /**
     * 更新问卷
     *
     * @param PqRequest $request
     * @param $id
     * @return JsonResponse
     */
    public function update(PqRequest $request, $id) {
        
        return $this->result(
            $this->pq->modify($request->all(), $id)
        );
        
    }
    
    /**
     * 删除问卷
     *
     * @param $id
     * @return JsonResponse
     * @throws Exception
     */
    public function destroy($id) {
        
        return $this->result(
            $this->pq->remove($id)
        );
        
    }
    
}
