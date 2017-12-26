<?php
namespace App\Http\Controllers;

use App\Http\Requests\PqRequest;
use App\Models\PollQuestionnaire;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
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
    
    function __construct() {
        
        $this->middleware(['auth', 'checkrole']);
        
    }
    
    /**
     * 问卷列表
     *
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function index() {
        
        if (Request::get('draw')) {
            return response()->json(PollQuestionnaire::dataTable());
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
        
        $this->authorize('c', PollQuestionnaire::class);
        
        return $this->output();
        
    }
    
    /**
     * 保存问卷
     *
     * @param PqRequest $request
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function store(PqRequest $request) {
    
        $this->authorize('c', PollQuestionnaire::class);
        
        return $this->result(PollQuestionnaire::create($request->all()));
        
    }
    
    /**
     * 问卷详情
     *
     * @param $id
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function show($id) {
        
        $pq = PollQuestionnaire::find($id);
        $this->authorize('rud', $pq);
        
        return $this->output([
            'pollQuestionnaire' => $pq,
        ]);
        
    }
    
    /**
     * 编辑问卷
     * @param $id
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function edit($id) {
        
        $pq = PollQuestionnaire::find($id);
        $this->authorize('rud', $pq);
        
        return $this->output(['pollQuestionnaire' => $pq]);
        
    }
    
    /**
     * 更新问卷
     *
     * @param PqRequest $request
     * @param $id
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function update(PqRequest $request, $id) {
        
        $pq = PollQuestionnaire::find($id);
        $this->authorize('rud', $pq);
        
        return $this->result($pq->update($request->all()));
        
    }
    
    /**
     * 删除问卷
     *
     * @param $id
     * @return JsonResponse
     * @throws Exception
     */
    public function destroy($id) {
        
        $pq = PollQuestionnaire::find($id);
        $this->authorize('rud', $pq);
        
        return $this->result($pq->remove($id));
        
    }
    
}
