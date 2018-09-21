<?php
namespace App\Http\Controllers;

use App\Helpers\HttpStatusCode;
use App\Http\Requests\ActionRequest;
use App\Models\Action;
use Exception;
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
    
    public $type = 1;
    protected $action;
    
    /**
     * ActionController constructor.
     * @param Action $action
     */
    function __construct(Action $action) {
        
        $this->middleware(['auth', 'checkrole']);
        $this->action = $action;
        $this->approve($action);
        
    }
    
    /**
     * 功能列表
     *
     * @return bool|JsonResponse
     * @throws Exception
     * @throws Throwable
     */
    public function index() {
        
        if (Request::get('draw')) {
            return response()->json(
                $this->action->index()
            );
        }
        abort_if(!$this->action->scan(), HttpStatusCode::NOT_FOUND);
        
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
     */
    public function update(ActionRequest $request, $id) {
        
        return $this->result(
            $this->action->modify(
                $request->all(), $id
            )
        );
        
    }
    
}
