<?php

namespace App\Http\Controllers;

use App\Http\Requests\SquadRequest;
use App\Models\Educator;
use App\Models\Squad;
use App\Models\User;
use Illuminate\Support\Facades\Request;

/**
 * @property array message
 */
class SquadController extends Controller {
    
    protected $class, $educator;
    
    public function __construct(Squad $class, Educator $educator) {
        
        $this->class = $class;
        $this->educator = $educator;
        
    }
    
    /**
     * 显示班级列表
     *
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public function index() {
        
        if (Request::get('draw')) {
            return response()->json($this->class->datatable());
        }
        return $this->output(__METHOD__);
        
    }
    
    /**
     * 显示创建班级记录的表单
     *
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public function create() {
    
        return $this->output(__METHOD__);
        
    }
    
    /**
     * 保存新创建的班级记录
     *
     * @param SquadRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(SquadRequest $request) {
        
        if ($this->class->existed($request)) {
            return $this->fail('已经有此记录');
        }
        return $this->class->create($request->all()) ? $this->succeed() : $this->fail();
        
    }
    
    /**
     * 显示指定的班级记录详情
     *
     * @param $id
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public function show($id) {

        $class = $this->class->find($id);
        if (!$class) { return $this->notFound(); }
        $educatorIds = explode(",", $class->educator_ids);

        return $this->output(__METHOD__, [
            'class' => $class,
            'educators' => $this->educator->educators($educatorIds)
        ]);
        
    }
    
    /**
     * 显示编辑指定班级记录的表单
     *
     * @param $id
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public function edit($id) {
    
        $class = $this->class->find($id);
        if (!$class) { return $this->notFound(); }
        $educatorIds = explode(",", $class->educator_ids);

        return $this->output(__METHOD__, [
            'class' => $class,
            'selectedEducators' => $this->educator->educators($educatorIds)
        ]);
        
        
    }
    
    /**
     * 更新指定的班级记录
     *
     * @param SquadRequest $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(SquadRequest $request, $id) {
    
        $class = $this->class->find($id);
        if (!$class) { return $this->notFound(); }
        if ($this->class->existed($request, $id)) {
            return $this->fail('已经有此记录');
        }
        return $class->update($request->all()) ? $this->succeed() : $this->fail();
        
    }
    
    /**
     * 删除指定的班级记录
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id) {
    
        $class = $this->class->find($id);
        if (!$class) { return $this->notFound(); }
        return $class->delete() ? $this->succeed() : $this->fail();
    
    }
    
}
