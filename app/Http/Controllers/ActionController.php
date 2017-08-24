<?php

namespace App\Http\Controllers;

use App\Http\Requests\ActionRequest;
use App\Models\Action;
use App\Models\ActionType;
use Illuminate\Support\Facades\Request;

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
     * 显示创建Action记录的表单
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function create() {

        return parent::output(__METHOD__);
        
    }
    
    /**
     * 保存新创建的action记录
     *
     * @param ActionRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(ActionRequest $request) {
        
        return $this->action->store($request) ? parent::succeed() : parent::fail();
        
    }
    
    /**
     * 显示指定的action记录详情
     *
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show($id) {
        
        $action = $this->action->find($id);
        if (!$action) { return parent::notFound(); }
        return parent::output(__METHOD__, ['action' => $action]);
        
    }
    
    /**
     * 显示编辑指定action记录的表单
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
                $actionType = ActionType::whereId($actionTypeId)->first()->toArray();
                $selectedActionTypes[$actionTypeId] = $actionType['name'];
            }
        }
        
        return parent::output(__METHOD__, [
            'action' => $action,
            'selectedActionTypes' => $selectedActionTypes
        ]);
        
    }
    
    /**
     * 更新指定的action记录
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
    
    /**
     * 删除指定的action记录
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id) {

        $action = $this->action->find($id);
        if (!$action) { return parent::notFound(); }
        return $action->delete() ? parent::succeed() : parent::fail();
        
    }
    
}
