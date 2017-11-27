<?php

namespace App\Http\Controllers;

use App\Http\Requests\CustodianRequest;
use App\Models\Custodian;
use App\Models\Department;
use App\Models\DepartmentUser;
use App\Models\Group;
use App\Models\Student;
use App\Models\CustodianStudent;
use Illuminate\Support\Facades\Request;
use Maatwebsite\Excel\Facades\Excel;

/**
 * 监护人
 *
 * Class CustodianController
 * @package App\Http\Controllers
 */
class CustodianController extends Controller {

    protected $custodian, $department, $group, $departmentUser, $student, $custodianStudent;

    function __construct(
        Custodian $custodian, Department $department, Group $group,
        DepartmentUser $departmentUser, Student $student,
        CustodianStudent $custodianStudent
    ) {
    
        $this->middleware(['auth']);
        $this->custodian = $custodian;
        $this->department = $department;
        $this->group = $group;
        $this->departmentUser = $departmentUser;
        $this->student = $student;
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
            
            $field = Request::query('field');
            $id = Request::query('id');
            $this->result['html'] = $this->custodian->getFieldList($field, $id);
            return response()->json($this->result);
    
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

        return $this->custodian->store($request)
            ? $this->succeed() : $this->fail();

    }

    /**
     * 编辑监护人
     *
     * @param $id
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public function edit($id) {

        if (Request::method() === 'POST') {
            return $this->department->tree();
        }
        $custodian = $this->custodian->find($id);
        if (!$custodian) { return $this->notFound(); }
        $pupils = $custodian->custodianStudents;

        return $this->output(__METHOD__, [
            'mobiles'               => $custodian->user->mobiles,
            'custodian'             => $custodian,
            'pupils'                => $pupils,
        ]);

    }

    /**
     * 更新监护人.
     * @param CustodianRequest $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(CustodianRequest $request, $id) {

        return $this->custodian->modify($request, $id)
            ? $this->succeed() : $this->fail();

    }

    /**
     * 删除指定的监护人
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id) {
        
        return $this->custodian->remove($id)
            ? $this->succeed() : $this->fail();
        
    }
    /**
     * 导出数据
     * @return \Illuminate\Http\JsonResponse
     */
    public function export() {
        $data = $this->custodian->export();
        /** @noinspection PhpMethodParametersCountMismatchInspection */
        /** @noinspection PhpUndefinedMethodInspection */
        Excel::create(iconv('UTF-8', 'GBK', '监护人列表'), function ($excel) use ($data) {
            /** @noinspection PhpUndefinedMethodInspection */
            $excel->sheet('score', function($sheet) use ($data) {
                /** @noinspection PhpUndefinedMethodInspection */
                $sheet->rows($data);
                /** @noinspection PhpUndefinedMethodInspection */
                $sheet->setWidth(array(
                    'A'     =>  30,
                    'B'     =>  30,
                    'C'     =>  30,
                    'D'     =>  30,
                    'E'     =>  30,
                    'F'     =>  30,
                ));
                
            });
            
        },'UTF-8')->export('xls');
    }
}
