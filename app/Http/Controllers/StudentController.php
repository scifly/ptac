<?php

namespace App\Http\Controllers;

use App\Http\Requests\StudentRequest;
use App\Models\Custodian;
use App\Models\CustodianStudent;
use App\Models\Department;
use App\Models\DepartmentUser;
use App\Models\Group;
use App\Models\Student;
use App\Models\User;
use Illuminate\Support\Facades\Request;

/**
 * 学生
 *
 * Class StudentController
 * @package App\Http\Controllers
 */
class StudentController extends Controller {
    
    protected $custodian, $department, $group, $user, $mobile, $departmentUser, $student, $custodianStudent;
    
    function __construct(Custodian $custodian, Department $department, Group $group, User $user,
                         DepartmentUser $departmentUser, Student $student, CustodianStudent $custodianStudent) {
        
        $this->custodian = $custodian;
        $this->department = $department;
        $this->group = $group;
        $this->user = $user;
        $this->departmentUser = $departmentUser;
        $this->student = $student;
        $this->custodianStudent = $custodianStudent;
        
    }
    
    /**
     * 学生记录列表
     *
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public function index() {
        
        if (Request::get('draw')) {
            return response()->json($this->student->datatable());
        }
        return $this->output(__METHOD__);
        
    }
    
    /**
     * 创建学生记录
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function create() {
        
        return $this->output(__METHOD__);
        
    }
    
    /**
     * 保存学生记录
     *
     * @param StudentRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StudentRequest $request) {
        dd($this->student->store($request));
        return $this->student->store($request) ? $this->succeed() : $this->fail();
        
    }
    
    /**
     * 学生记录详情
     *
     * @param $id
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public function show($id) {
        
        $student = $this->student->find($id);
        if (!$student) {
            return $this->notFound();
        }
        return $this->output(__METHOD__, ['student' => $student]);
        
    }
    
    /**
     * 编辑学生记录
     *
     * @param $id
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public function edit($id) {
        $student = $this->student->find($id);
        $student['student'] = $this->student->find($id);
        $departmentIds = $this->departmentUser->where('user_id', $student->user_id)->get();
        foreach ($departmentIds as $key => $value) {
            $department = Department::whereId($value['department_id'])->first();
            $selectedDepartments[$department['id']] = $department['name'];
        }
        
        # 根据学生Id查询监护人学生表的数据
//        $custodianStudent = $this->custodianStudent->where('student_id',$student->id)->get()->toArray();
        
        
        foreach ($student->custodians as $key => $value) {
            # 被选中的监护人信息
            $custodianId = $this->custodian->find($value['id']);
            # 被选中的监护人
            $selectedCustodians[$value['id']] = $custodianId->user->realname;
            
        }
        
        # 查询学生信息
        if (!$student) {
            return $this->notFound();
        }
        
        return $this->output(__METHOD__, [
            'student' => $student,
            'selectedDepartments' => $selectedDepartments,
            'selectedCustodians' => $selectedCustodians,
        ]);
        
    }
    
    /**
     * 更新学生记录
     *
     * @param StudentRequest $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(StudentRequest $request, $id) {
        dd($this->student->modify($request, $id));
        return $this->student->modify($request, $id) ? $this->succeed() : $this->fail();
        
    }
    
    /**
     * 删除学生记录
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id) {
        
        return $this->custodian->remove($id) ? $this->succeed() : $this->fail();
        
    }
    
}
