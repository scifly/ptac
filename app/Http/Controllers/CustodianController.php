<?php

namespace App\Http\Controllers;

use App\Http\Requests\CustodianRequest;
use App\Models\Custodian;
use App\Models\Department;
use App\Models\DepartmentUser;
use App\Models\Group;
use App\Models\User;
use App\Models\Mobile;
use App\Models\Student;
use App\Models\CustodianStudent;
use Illuminate\Support\Facades\Request;

/**
 * 监护人
 *
 * Class CustodianController
 * @package App\Http\Controllers
 */
class CustodianController extends Controller {
    
    protected $custodian, $department, $group, $departmentUser,$student,$custodianStudent;
    
    function __construct(Custodian $custodian, Department $department, Group $group,
    DepartmentUser $departmentUser,Student $student,CustodianStudent $custodianStudent) {
    
        $this->custodian = $custodian;
        $this->department = $department;
        $this->group = $group;
        $this->departmentUser = $departmentUser;
        $this->student =$student;
        $this->custodianStudent = $custodianStudent;
        
    }
    /**
     * 监护人列表
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
     * 创建监护人
     *
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public function create() {

        if (Request::method() === 'POST') {
            return $this->department->tree();
        }
        return parent::output(__METHOD__);
        
    }
    
    /**
     * 保存监护人
     *
     * @param CustodianRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(CustodianRequest $request) {

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

        if (Request::method() === 'POST') {
            return $this->department->tree();
        }
        $custodian = $this->custodian->find($id);

        $departments = $custodian->user->departments;
        $selectedDepartmentIds = [];
        foreach ($departments as $department) {
            $selectedDepartmentIds[] = $department->id;
        }

        $selectedDepartments = $this->department->selectedNodes($selectedDepartmentIds);

        $custodianStudents = $this->custodianStudent->where('custodian_id', $custodian->id)->get();
        $relationships = [];
        foreach ($custodianStudents as $r) {
            $relationships[$r->student_id] = $r->relationship;
        }
        $selectedStudents = [];
        foreach ($custodian->students as $key => $value)
        {
            $studentId = $this->student->find($value['id']);
            $selectedStudents[$studentId->id] = $studentId->user->realname;
        }

        if (!$custodian) {
            return $this->notFound();
        }
        return $this->output(__METHOD__, [
            'mobiles' => $custodian->user->mobiles,
            'custodian' => $custodian,
            'selectedDepartmentIds' => implode(',', $selectedDepartmentIds),
            'selectedDepartments' => $selectedDepartments,
            'selectedStudents' => $selectedStudents,
            'relationships' => $relationships
        ]);
        
    }
    
    /**
     * 更新监护人.
     * @param CustodianRequest $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(CustodianRequest $request, $id) {

        return $this->custodian->modify($request,$id) ? $this->succeed() : $this->fail();
        
    }

    /**
     * 删除指定的监护人
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id) {
        return $this->custodian->remove($id) ? $this->succeed() : $this->fail();
    }
}
