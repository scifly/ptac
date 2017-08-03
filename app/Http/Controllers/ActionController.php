<?php

namespace App\Http\Controllers;

use App\Http\Requests\ActionRequest;
use App\Models\Action;
use App\Models\ActionType;
use Illuminate\Support\Facades\Request;

class ActionController extends Controller {
    
    protected $action;
    
    function __construct(Action $action) { $this->action = $action; }
    
    /**
     * 显示action列表
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\View\View
     */
    public function index() {
        
        if (Request::get('draw')) {
            return response()->json($this->action->datatable());
        }
        // 获取app/Http/Controllers路径下控制器包含的所有自定义public方法
        $this->action->scan();
        return view('action.index', [
            'js' => 'js/action/index.js',
            'datatable' => true,
            'dialog' => true
        ]);
        
    }
    
    /**
     * 显示创建action记录的表单
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create() {
        
        return view('action.create', [
            'js' => 'js/action/create.js',
            'form' => true
        ]);
        
    }
    
    /**
     * 保存新创建的action记录
     *
     * @param ActionRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(ActionRequest $request) {
        
        if ($this->action->create($request->all())) {
            $this->result['statusCode'] = self::HTTP_STATUSCODE_OK;
            $this->result['message'] = self::MSG_CREATE_OK;
        } else {
            $this->result['statusCode'] = self::HTTP_STATUSCODE_INTERNAL_SERVER_ERROR;
            $this->result['message'] = '';
        }
        return response()->json($this->result);
        
    }
    
    /**
     * 显示指定的action记录详情
     *
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show($id) {
        
        $action = $this->action->findOrFail($id);
        return view('action.show', ['score' => $action]);
        
    }
    
    /**
     * 显示编辑指定action记录的表单
     *
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit($id) {
        
        $action = $this->action->findOrFail($id);
        $ids = $action->where('id', $id)->get(['action_type_ids'])->toArray()[0]['action_type_ids'];
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
        return view('action.edit', [
            'js' => 'js/action/edit.js',
            'form' => true,
            'action' => $action,
            'selectedActionTypes' => $selectedActionTypes
        ]);
        
    }
    
    /**
     * 删除指定的action记录
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id) {
    
        if ($this->action->findOrFail($id)->delete()) {
            $this->result['statusCode'] = self::HTTP_STATUSCODE_OK;
            $this->result['message'] = self::MSG_DEL_OK;
        } else {
            $this->result['statusCode'] = self::HTTP_STATUSCODE_INTERNAL_SERVER_ERROR;
            $this->result['message'] = '';
        }
        return response()->json($this->result);
        
    }
    
}
