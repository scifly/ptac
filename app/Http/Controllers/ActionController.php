<?php
namespace App\Http\Controllers;

use App\Http\Requests\ActionRequest;
use App\Models\Action;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Request;
use Throwable;

/**
 * 功能
 *
 * Class ActionController
 * @package App\Http\Controllers
 */
class ActionController extends Controller {
    
    static $type = 1;
    protected $action;
    
    /**
     * ActionController constructor.
     * @param Action $action
     */
    function __construct(Action $action) {
        
        $this->middleware(['auth', 'checkrole']);
        $this->approve($this->action = $action);
        
    }
    
    /**
     * 功能列表
     *
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function index() {
        
        if (Request::get('draw')) {
            return response()->json(
                $this->action->index()
            );
        }
        $this->action->scan();
        
        return $this->output();
        
    }
    
    /**
     * 编辑功能
     *
     * @param $id
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function edit($id) {
        
        return $this->output([
            'action' => $this->action->find($id),
        ]);
        
    }
    
    /**
     * 更新功能
     *
     * @param ActionRequest $request
     * @param $id
     * @return JsonResponse
     * @throws Throwable
     */
    public function update(ActionRequest $request, $id) {
        
        return $this->result(
            $this->action->modify(
                $request->all(), $id
            )
        );
        
    }
    
}
