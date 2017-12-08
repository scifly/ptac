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
    
    protected $pollQuestionnaire;
    
    function __construct(PollQuestionnaire $pollQuestionnaire) {
        
        $this->middleware(['auth']);
        $this->pollQuestionnaire = $pollQuestionnaire;
        
    }
    
    /**
     * 问卷列表
     *
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function index() {
        
        if (Request::get('draw')) {
            return response()->json($this->pollQuestionnaire->dataTable());
        }
        
        return $this->output(__METHOD__);
        
    }
    
    /**
     * 创建问卷
     *
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function create() {
        
        return $this->output(__METHOD__);
        
    }
    
    /**
     * 保存问卷
     *
     * @param PqRequest $request
     * @return JsonResponse
     */
    public function store(PqRequest $request) {
        
        $data = $request->all();
        $data['user_id'] = 6;
        
        return $this->pollQuestionnaire->create($data)
            ? $this->succeed() : $this->fail();
        
    }
    
    /**
     * 问卷详情
     *
     * @param $id
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function show($id) {
        
        $pollQuestionnaire = $this->pollQuestionnaire->find($id);
        if (!$pollQuestionnaire) { return $this->notFound(); }
        
        return $this->output(__METHOD__, [
            'pollQuestionnaire' => $pollQuestionnaire,
        ]);
        
    }
    
    /**
     * 编辑问卷
     * @param $id
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function edit($id) {
        
        $pollQuestionnaire = $this->pollQuestionnaire->find($id);
        if (!$pollQuestionnaire) { return $this->notFound(); }
        
        return $this->output(__METHOD__, [
            'pollQuestionnaire' => $pollQuestionnaire
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
        
        $pollQuestionnaire = $this->pollQuestionnaire->find($id);
        if (!$pollQuestionnaire) { return $this->notFound(); }
        
        return $pollQuestionnaire->update($request->all())
            ? $this->succeed() : $this->fail();
        
    }
    
    /**
     * 删除问卷
     *
     * @param $id
     * @return JsonResponse
     * @throws Exception
     */
    public function destroy($id) {
        
        $pq = $this->pollQuestionnaire->find($id);
        if (!$pq) { return $this->notFound(); }
        
        return $pq->remove($id)
            ? $this->succeed() : $this->fail('失败：该问卷存在有效关联数据，不能删除');
        
    }
    
}
