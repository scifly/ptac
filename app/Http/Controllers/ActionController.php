<?php
namespace App\Http\Controllers;

use App\Http\Requests\ActionRequest;
use App\Models\Action;
use Illuminate\Support\Facades\Request;

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
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public function index() {

        if (Request::get('draw')) {
            return response()->json($this->action->datatable());
        }
        if (!$this->action->scan()) { return $this->notFound(); }

        return parent::output(__METHOD__);

    }

    /**
     * 编辑功能
     *
     * @param $id
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public function edit($id) {

        $action = $this->action->find($id);
        if (!$action) { return parent::notFound(); }

        return parent::output(__METHOD__, ['action' => $action]);

    }

    /**
     * 更新功能
     *
     * @param ActionRequest $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(ActionRequest $request, $id) {

        $action = $this->action->find($id);
        if (!$action) { return parent::notFound(); }

        return $action->update($request->all())
            ? parent::succeed() : parent::fail();

    }

}
