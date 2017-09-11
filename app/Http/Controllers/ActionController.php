<?php

namespace App\Http\Controllers;

use App\Http\Requests\ActionRequest;
use App\Models\Action;
use App\Models\School;
use Illuminate\Support\Facades\Request;

/**
 * Action
 *
 * Class ActionController
 * @package App\Http\Controllers
 */
class ActionController extends Controller {
    
    protected $action;
    
    function __construct(Action $action) {
        
        $this->action = $action;
        
    }
    
    /**
     * 显示Action列表
     *
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public function index() {

        if (Request::get('draw')) {
            return response()->json($this->action->datatable());
        }
        if (!$this->action->scan()) { return parent::notFound(); }
        return parent::output(__METHOD__);
        
    }
    
    /**
     * 编辑action
     *
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit($id) {
        
        $action = $this->action->find($id);
        if (!$action) { return parent::notFound(); }
        $ids = $action->where('id', $id)
            ->get(['action_type_ids'])
            ->toArray()[0]['action_type_ids'];
        $actionTypeIds = explode(',', $ids);
        $selectedActionTypes = [];
        if (empty($actionTypeIds[0])) {
            $selectedActionTypes = NULL;
        } else {
            foreach ($actionTypeIds as $actionTypeId) {
                $actionType = School::whereId($actionTypeId)->first()->toArray();
                $selectedActionTypes[$actionTypeId] = $actionType['name'];
            }
        }
        
        return parent::output(__METHOD__, [
            'action' => $action,
            'selectedActionTypes' => $selectedActionTypes
        ]);
        
    }
    
    /**
     * 更新action
     *
     * @param ActionRequest $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(ActionRequest $request, $id) {
        
        $action = $this->action->find($id);
        if (!$action) { return parent::notFound(); }
        return $action->update($request->all()) ? parent::succeed() : parent::fail();
        
    }
    
}
