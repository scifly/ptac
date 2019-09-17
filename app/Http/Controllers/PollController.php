<?php
namespace App\Http\Controllers;

use App\Http\Requests\PollRequest;
use App\Models\Poll;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Request;
use Throwable;

/**
 * 调查问卷
 *
 * Class PollController
 * @package App\Http\Controllers
 */
class PollController extends Controller {
    
    protected $pq;
    
    /**
     * PollController constructor.
     * @param Poll $pq
     */
    function __construct(Poll $pq) {
        
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
     * @param PollRequest $request
     * @return JsonResponse
     */
    public function store(PollRequest $request) {
        
        return $this->result(
            $this->pq->store($request->all())
        );
        
    }
    
    /**
     * 编辑问卷
     * @param $id
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function edit($id) {
        
        return $this->output([
            'pq' => Poll::find($id),
        ]);
        
    }
    
    /**
     * 更新问卷
     *
     * @param PollRequest $request
     * @param $id
     * @return JsonResponse
     */
    public function update(PollRequest $request, $id) {
        
        return $this->result(
            $this->pq->modify($request->all(), $id)
        );
        
    }
    
    /**
     * 删除问卷
     *
     * @param $id
     * @return JsonResponse
     * @throws Throwable
     */
    public function destroy($id) {
        
        return $this->result(
            $this->pq->remove($id)
        );
        
    }
    
}
