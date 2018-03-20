<?php

namespace App\Http\Controllers;

use App\Helpers\HttpStatusCode;
use App\Http\Requests\EducatorRequest;
use App\Models\Department;
use App\Models\Educator;
use App\Models\School;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Request;
use Throwable;

/**
 * 教职员工
 *
 * Class EducatorController
 * @package App\Http\Controllers
 */
class EducatorController extends Controller {
    
    protected $educator, $department, $school;

    public function __construct(Educator $educator, Department $department, School $school) {

        $this->middleware(['auth', 'checkrole']);
        $this->educator = $educator;
        $this->department = $department;
        $this->school = $school;

    }

    /**
     * 教职员工列表
     *
     * @return bool|JsonResponse
     * @throws \Throwable
     */
    public function index() {

        if (Request::get('draw')) {
            return response()->json(
                $this->educator->datatable()
            );
        }

        return $this->output();

    }

    /**
     * 创建教职员工
     *
     * @return bool|JsonResponse
     * @throws \Throwable
     */
    public function create() {

        $this->authorize('c', Educator::class);
        if (Request::method() === 'POST') {
            return response()->json(
                $this->department->tree(
                    $this->school->find($this->school->schoolId())->department_id
                )
            );
        }

        return $this->output();

    }

    /**
     * 保存教职员工
     *
     * @param EducatorRequest $request
     * @return JsonResponse
     * @throws \Throwable
     */
    public function store(EducatorRequest $request) {

        $this->authorize(
            'c', Educator::class
        );

        return $this->result(
            $this->educator->store($request)
        );

    }

    /**
     * 教职员工详情
     *
     * @param $id
     * @return bool|JsonResponse
     * @throws \Throwable
     */
    public function show($id) {

        $educator = Educator::find($id);
        $this->authorize('rud', $educator);

        return $this->output(['educator' => $educator]);

    }

    /**
     * 编辑教职员工
     *
     * @param $id
     * @return bool|JsonResponse
     * @throws \Throwable
     */
    public function edit($id) {

        $educator = Educator::find($id);
        $this->authorize('rud', $educator);
        if (Request::method() === 'POST') {
            return response()->json($this->department->tree());
        }
        $selectedTeams = [];
        foreach ($educator->teams as $v) {
            $selectedTeams[$v->id] = $v->name;
        }
        $selectedDepartmentIds = [];
        foreach ($educator->user->departments as $department) {
            $selectedDepartmentIds[] = $department->id;
        }
        $selectedDepartments = $this->department->selectedNodes($selectedDepartmentIds);
        
        return $this->output([
            'mobiles' => $educator->user->mobiles,
            'educator' => $educator,
            'selectedTeams' => $selectedTeams,
            'selectedDepartmentIds' => implode(',', $selectedDepartmentIds),
            'selectedDepartments' => $selectedDepartments,
        ]);

    }

    /**
     * 教职员工充值
     *
     * @param $id
     * @return bool|JsonResponse
     * @throws \Throwable
     */
    public function recharge($id) {

        $educator = Educator::find($id);
        $this->authorize('rud', $educator);

        return $this->output([
            'educator' => $educator,
        ]);

    }

    /**
     * 更新教职员工
     *
     * @param EducatorRequest $request
     * @param $id
     * @return JsonResponse
     * @throws AuthorizationException
     * @throws Throwable
     */
    public function update(EducatorRequest $request, $id) {

        $educator = Educator::find($id);
        $this->authorize('rud', $educator);

        return $this->result(
            $educator->modify($request)
        );

    }

    /**
     * 更新教职员工充值
     *
     * @param $id
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function rechargeStore($id) {

        $educator = Educator::find($id);
        $this->authorize('rud', $educator);

        $recharge = Request::get('recharge');
        $educator->sms_quote += $recharge;

        return $this->result(
            $educator->save()
        );

    }
    
    /**
     * 删除教职员工
     *
     * @param $id
     * @return JsonResponse
     * @throws AuthorizationException
     * @throws Throwable
     * @throws Exception
     */
    public function destroy($id) {

        $educator = Educator::find($id);
        $this->authorize('rud', $educator);

        return $this->result(
            $educator->remove($id, true)
        );

    }
    
    /**
     * 导入教职员工
     *
     * @return JsonResponse|null
     * @throws AuthorizationException
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Reader\Exception
     */
    public function import() {

        $this->authorize(
            'c', Educator::class
        );
        if (Request::isMethod('post')) {
            $file = Request::file('file');
            abort_if(
                empty($file),
                HttpStatusCode::INTERNAL_SERVER_ERROR,
                '您还没选择文件！'
            );
            
            // 文件是否上传成功
            if ($file->isValid()) {
                return response()->json(
                    $this->educator->upload($file)
                );
            }
        }

        return abort(HttpStatusCode::METHOD_NOT_ALLOWED);

    }
    
    /**
     * 导出教职员工
     *
     * @return mixed
     * @throws AuthorizationException
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    public function export() {

        $this->authorize(
            'c', Educator::class
        );
        
        $range = Request::query('range');
        $departmentId = Request::query('department_id');
        
        return $this->educator->export(
            $range, $departmentId
        );

    }

}
