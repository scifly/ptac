<?php

namespace App\Http\Controllers;

use App\Http\Requests\CustodianRequest;
use App\Models\Custodian;
use App\Models\Department;
use App\Models\Group;
use App\Models\User;
use Illuminate\Support\Facades\Request;

class CustodianController extends Controller {
    
    protected $custodian, $department, $group, $user;
    
    function __construct(Custodian $custodian, Department $department, Group $group, User $user) {
    
        $this->custodian = $custodian;
        $this->department = $department;
        $this->group = $group;
        $this->user = $user;
        
    }
    
    /**
     * 显示监护人列表
     *
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public function index() {
        if (Request::get('draw')) {
            return response()->json($this->custodian->datatable());
        }
        return parent::output(__METHOD__);
    }
    
    /**
     * 显示创建监护人记录的表单
     *
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public function create() {
        
        return parent::output(__METHOD__, [
            'group' => $this->group->group('custodian'),
            'departments' => $this->department->departments([1])
        ]);
        
    }
    
    /**
     * 保存新创建的监护人记录
     *
     * @param CustodianRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(CustodianRequest $request) {
        
        if ($this->custodian->existed($request)) {
            return $this->fail('已经有此记录');
        }
        return $this->custodian->store($request) ? $this->succeed() : $this->fail();
        
    }
    
    /**
     * Display the specified resource.
     * @param  \App\Models\Custodian $custodian
     * @return \Illuminate\Http\Response
     */
    public function show(Custodian $custodian) {
    
    }
    
    /**
     * 编辑监护人.
     * @param $id
     * @return \Illuminate\Http\Response
     * @internal param Custodian $custodian
     */
    public function edit($id) {
       dd($id);
        $custodian = $this->custodian->find($id);
        if (!$custodian) {
            return $this->notFound();
        }
        return $this->output(__METHOD__, ['custodian' => $custodian]);
        
    }
    
    /**
     * 更新监护人.
     * @param CustodianRequest $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(CustodianRequest $request, $id) {
        
        $custodian = $this->custodian->find($id);
        if (!$custodian) {
            return $this->notFound();
        }
        if ($this->custodian->existed($request, $id)) {
            return $this->fail('已经有此记录');
        }
        return $custodian->update($request->all()) ? $this->succeed() : $this->fail();
        
    }


    public function destroy($id) {
        $this->custodian->remove($id);
        $custodian = $this->custodian->find($id);
        if (!$custodian) {
            return $this->notFound();
        }
        return $custodian->delete() ? $this->succeed() : $this->fail();
    }
}
