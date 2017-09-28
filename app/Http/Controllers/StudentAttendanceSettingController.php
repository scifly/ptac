<?php
namespace App\Http\Controllers;

use App\Http\Requests\StudentAttendanceSettingRequest;
use App\Models\StudentAttendanceSetting;
use Illuminate\Support\Facades\Request;

/**
 * 学生考勤设置
 *
 * Class StudentAttendanceSettingController
 * @package App\Http\Controllers
 */
class StudentAttendanceSettingController extends Controller {

    protected $sas;

    function __construct(StudentAttendanceSetting $studentAttendanceSetting) {

        $this->sas = $studentAttendanceSetting;

    }

    /**
     * 学生考勤设置列表
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {

        if (Request::get('draw')) {
            return response()->json($this->sas->datatable());
        }

        return parent::output(__METHOD__);

    }

    /**
     * 创建学生考勤设置
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {

        return $this->output(__METHOD__);

    }

    /**
     * 保存学生考勤设置
     *
     * @param StudentAttendanceSettingRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StudentAttendanceSettingRequest $request) {

        return $this->sas->create($request->all()) ? $this->succeed() : $this->fail();

    }

    /**
     * 编辑学生考勤设置
     *
     * @param $id
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public function show($id) {

        $sas = $this->sas->find($id);
        if (!$sas) {
            return $this->notFound();
        }

        return $this->output(__METHOD__, [
            'studentAttendanceSetting' => $sas,
        ]);

    }

    /**
     * 编辑学生考勤设置
     *
     * @param $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {

        $sas = $this->sas->find($id);
        if (!$sas) {
            return $this->notFound();
        }

        return $this->output(__METHOD__, [
            'studentAttendanceSetting' => $sas,
        ]);

    }

    /**
     * 更新学生考勤设置
     *
     * @param StudentAttendanceSettingRequest $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(StudentAttendanceSettingRequest $request, $id) {

        $sas = $this->sas->find($id);
        if (!$sas) {
            return $this->notFound();
        }

        return $sas->update($request->all()) ? $this->succeed() : $this->fail();

    }

    /**
     * 删除学生考勤设置
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id) {

        $sas = $this->sas->find($id);
        if (!$sas) {
            return $this->notFound();
        }

        return $sas->delete() ? $this->succeed() : $this->fail();

    }
}
