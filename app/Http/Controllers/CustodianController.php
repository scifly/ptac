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
    
    protected $custodian, $department, $group, $user,$mobile,$departmentUser,$student,$custodianStudent;
    
    function __construct(Custodian $custodian, Department $department, Group $group, User $user,Mobile $mobile,
    DepartmentUser $departmentUser,Student $student,CustodianStudent $custodianStudent) {
    
        $this->custodian = $custodian;
        $this->department = $department;
        $this->group = $group;
        $this->user = $user;
        $this->mobile = $mobile;
        $this->departmentUser = $departmentUser;
        $this->student =$student;
        $this->custodianStudent = $custodianStudent;
        
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
        
        return parent::output(__METHOD__);
        
    }
    
    /**
     * 保存新创建的监护人记录
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
        $custodian = $this->custodian->find($id);
        $user['user'] = $this->user->find($custodian->user_id);
        $user['expiry'] = $custodian->expiry;
        $user['mobile'] = $this->mobile->where('user_id',$custodian->user_id)->first();
        $departmentIds = $this->departmentUser->where('user_id',$custodian->user_id)->get();
        foreach ($departmentIds as $key=>$value)
        {
            $department = Department::whereId($value['department_id'])->first();
            $selectedDepartments[$department['id']] = $department['name'];
        }

        $custodianStudent = $this->custodianStudent->where('custodian_id',$custodian->id)->get()->toArray();
        if($custodianStudent !=null)
        {
            foreach ($custodianStudent as $key=>$value)
            {
                $studentId = $this->student->find($value['student_id']);
                $selectedStudents[$studentId->id] = $studentId->user->realname;
            }
        }else{
            $selectedStudents = [];
        }


        if (!$user) {
            return $this->notFound();
        }
        return $this->output(__METHOD__, [
            'custodian' => $custodian,
            'user' => $user,
//            'departments'=>$this->department->departments([1]),
            'selectedDepartments' => $selectedDepartments,
            'selectedStudents' => $selectedStudents,
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
