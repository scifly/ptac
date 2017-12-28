<?php
namespace App\Http\Controllers;

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

    protected $action;

    function __construct(Action $action) {

        $this->middleware(['auth']);
        $this->action = $action;

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
            return response()->json($this->action->datatable());
        }
        if (!$this->action->scan()) { return $this->notFound(); }

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

        $action = $this->action->find($id);
        if (!$action) { return $this->notFound(); }

        return $this->output(['action' => $action]);

    }

    /**
     * 更新功能
     *
     * @param ActionRequest $request
     * @param $id
     * @return JsonResponse
     */
    public function update(ActionRequest $request, $id) {

        $action = $this->action->find($id);
        if (!$action) { return $this->notFound(); }

        return $this->result($action->update($request->all()));

    }
    
}
