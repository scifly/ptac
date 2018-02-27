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
    
    protected $pq;
    
    function __construct(PollQuestionnaire $pq) {
        
        $this->middleware(['auth', 'checkrole']);
        $this->pq = $pq;
        
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
                $this->pq->dataTable()
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
        
        $this->authorize(
            'c', PollQuestionnaire::class
        );
        
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
    
        $this->authorize(
            'c', PollQuestionnaire::class
        );
        
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
        
        $pq = PollQuestionnaire::find($id);
<<<<<<< HEAD
        abort_if(!$pq, self::NOT_FOUND);
=======
>>>>>>> a8b77c532a4d09f2fe4f9feaadd84ba5d5a4fd12
        $this->authorize('rud', $pq);
        
        return $this->output([
            'pq' => $pq,
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
<<<<<<< HEAD
        abort_if(!$pq, self::NOT_FOUND);
=======
>>>>>>> a8b77c532a4d09f2fe4f9feaadd84ba5d5a4fd12
        $this->authorize('rud', $pq);
        
        return $this->output(['pq' => $pq]);
        
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
<<<<<<< HEAD
        abort_if(!$pq, self::NOT_FOUND);
=======
>>>>>>> a8b77c532a4d09f2fe4f9feaadd84ba5d5a4fd12
        $this->authorize('rud', $pq);
        
        return $this->result(
            $pq->modify($request->all(), $id)
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
        
        $pq = PollQuestionnaire::find($id);
<<<<<<< HEAD
        abort_if(!$pq, self::NOT_FOUND);
=======
>>>>>>> a8b77c532a4d09f2fe4f9feaadd84ba5d5a4fd12
        $this->authorize('rud', $pq);
        
        return $this->result($pq->remove($id));
        
    }
    
}
