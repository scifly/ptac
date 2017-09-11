<?php

namespace App\Http\Controllers;

use App\Http\Requests\GroupRequest;
use App\Models\Group;
use Illuminate\Support\Facades\Request;

/**
 * 角色
 *
 * Class GroupController
 * @package App\Http\Controllers
 */
class GroupController extends Controller {
    
    protected $group;
    
    function __construct(Group $group) { $this->group = $group; }
    
    /**
     * 角色列表
     *
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public function index() {
        
        if (Request::get('draw')) {
            return response()->json($this->group->datatable());
        }
        return $this->output(__METHOD__);
        
    }
    
    /**
     * 创建角色
     *
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public function create() {
        
        return $this->output(__METHOD__);
        
    }
    
    /**
     * 保存角色
     *
     * @param GroupRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(GroupRequest $request)
    {

        return $this->group->create($request->all()) ? $this->succeed() : $this->fail();
        
    }
    
    /**
     * 角色详情
     *
     * @param $id
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public function show($id) {
        
        $group = $this->group->find($id);
        if (!$group) { return $this->notFound(); }
        return $this->output(__METHOD__, ['group' => $group]);
        
    }
    
    /**
     * 编辑角色
     *
     * @param $id
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public function edit($id) {
        
        $group = $this->group->find($id);
        if (!$group) { return $this->notFound(); }
        return $this->output(__METHOD__, ['group' => $group]);
        
    }
    
    /**
     * 更新角色
     *
     * @param GroupRequest $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(GroupRequest $request, $id) {
        $group = $this->group->find($id);
        if (!$group) { return $this->notFound(); }
        return $group->update($request->all()) ? $this->succeed() : $this->fail();
    
    }
    
    /**
     * 删除角色
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id) {
    
        $group = $this->group->find($id);
        if (!$group) { return $this->notFound(); }
        return $group->delete() ? $this->succeed() : $this->fail();
    
    }
    
}
