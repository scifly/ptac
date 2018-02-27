<?php
namespace App\Http\Controllers;

use App\Http\Requests\ConferenceQueueRequest;
use App\Models\ConferenceQueue;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Request;
use Throwable;

/**
 * 会议
 *
 * Class ConferenceQueueController
 * @package App\Http\Controllers
 */
class ConferenceQueueController extends Controller {
    
    protected $cq;
    
    function __construct(ConferenceQueue $cq) {
    
        $this->middleware(['auth', 'checkrole']);
        $this->cq = $cq;
        
    }
    
    /**
     * 会议列表
     *
     * @return JsonResponse
     * @throws Throwable
     */
    public function index() {
        
        if (Request::get('draw')) {
            return response()->json(
                $this->cq->datatable()
            );
        }
        
        return $this->output();
        
    }
    
    /**
     * 创建会议
     *
     * @return JsonResponse
     * @throws Throwable
     */
    public function create() {
        
        return $this->output();
        
    }
    
    /**
     * 保存会议
     *
     * @param ConferenceQueueRequest $request
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function store(ConferenceQueueRequest $request) {
        
        $this->authorize(
            'c', ConferenceQueue::class
        );
        
        return $this->result(
            $this->cq->store($request->all())
        );
        
    }
    
    /**
     * 会议详情
     *
     * @param $id
     * @return JsonResponse
     * @throws Throwable
     */
    public function show($id) {
        
        $cq = $this->cq->find($id);
<<<<<<< HEAD
        abort_if(!$cq, self::NOT_FOUND);
=======
>>>>>>> a8b77c532a4d09f2fe4f9feaadd84ba5d5a4fd12
        $this->authorize('eud', $cq);
        
        return $this->output([
            'cq' => $cq,
        ]);
        
    }
    
    /**
     * 编辑会议
     *
     * @param $id
     * @return JsonResponse
     * @throws Throwable
     */
    public function edit($id) {
        
        $cq = $this->cq->find($id);
<<<<<<< HEAD
        abort_if(!$cq, self::NOT_FOUND);
=======
>>>>>>> a8b77c532a4d09f2fe4f9feaadd84ba5d5a4fd12
        $this->authorize('rud', $cq);
        
        return $this->output(['cq' => $cq]);
        
    }
    
    /**
     * 更新会议
     *
     * @param ConferenceQueueRequest $request
     * @param $id
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function update(ConferenceQueueRequest $request, $id) {
        
        $cq = $this->cq->find($id);
<<<<<<< HEAD
        abort_if(!$cq, self::NOT_FOUND);
=======
>>>>>>> a8b77c532a4d09f2fe4f9feaadd84ba5d5a4fd12
        $this->authorize('eud', $cq);
        
        return $this->result(
            $cq->modify($request->all(), $id)
        );
        
    }
    
    /**
     * 删除会议
     *
     * @param $id
     * @return JsonResponse
     * @throws Exception
     */
    public function destroy($id) {
        
        $cq = $this->cq->find($id);
<<<<<<< HEAD
        abort_if(!$cq, self::NOT_FOUND);
=======
>>>>>>> a8b77c532a4d09f2fe4f9feaadd84ba5d5a4fd12
        $this->authorize('eud', $cq);
        
        return $this->result($cq->remove($id));
        
    }
    
}
