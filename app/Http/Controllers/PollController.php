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
    
    protected $poll;
    
    /**
     * PollController constructor.
     * @param Poll $poll
     */
    function __construct(Poll $poll) {
        
        $this->middleware(['auth', 'checkrole']);
        $this->approve($this->poll = $poll);
        
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
                $this->poll->index()
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
            $this->poll->store($request->all())
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
            'poll' => $this->poll->find($id),
        ]);
        
    }
    
    /**
     * 更新问卷
     *
     * @param PollRequest $request
     * @param $id
     * @return JsonResponse
     * @throws Throwable
     */
    public function update(PollRequest $request, $id) {
        
        return $this->result(
            $this->poll->modify($request->all(), $id)
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
            $this->poll->remove($id)
        );
        
    }
    
}
