<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProcedureStepRequest;
use App\Models\ProcedureStep;
use Illuminate\Support\Facades\Request;

class ProcedureStepController extends Controller {
    protected $procedureStep;

    function __construct(ProcedureStep $procedureStep) {
        $this->procedureStep = $procedureStep;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        if (Request::get('draw')) {
            return response()->json($this->procedureStep->datatable());
        }

        return view('procedure_step.index', [
            'js' => 'js/procedure_step/index.js',
            'dialog' => true
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        return view('procedure_step.create', ['js' => 'js/procedure_step/create.js']);
    }

    /**
     * Store a newly created resource in storage.
     * @param ProcedureStepRequest $request
     * @return \Illuminate\Http\Response
     * @internal param \Illuminate\Http\Request|Request $request
     */
    public function store(ProcedureStepRequest $request) {
        //创建一个流程步骤空记录
        //将request 请求中包含的表单数据填入空记录对应的字段中
        //保存记录

        $procedureStep = new ProcedureStep;
        $procedureStep->procedure_id = $request->procedure_id;
        $procedureStep->name = $request->name;
        $procedureStep->approver_user_ids = $request->approver_user_ids;
        $procedureStep->related_user_ids = $request->related_user_ids;
        $procedureStep->remark = $request->remark;
        $procedureStep->enabled = $request->enabled;
        if ($procedureStep->save()) {
            return response()->json(['statusCode' => 200, 'message' => '创建成功！']);
        }

        return response()->json(['statusCode' => 500, 'message' => '创建失败！']);
    }

    /**
     * Display the specified resource.
     * @return \Illuminate\Http\Response
     * @internal param AttendanceMachine $attendanceMachine
     */
    public function show($id) {
        //根据id 查找单条记录
        $procedureStep = ProcedureStep::whereId($id)->first();

        //记录返回给view
        return view('procedure_step.show', ['procedureStep' => $procedureStep]);
    }

    /**
     * Show the form for editing the specified resource.
     * @param $id
     * @return \Illuminate\Http\Response
     * @internal param AttendanceMachine $attendanceMachine
     */
    public function edit($id) {
        //根据id 查找单条记录
        $procedureStep = ProcedureStep::whereId($id)->first();

        //记录返回给view
        return view('procedure_step.edit', [
            'js' => 'js/procedure_step/edit.js',
            'procedureStep' => $procedureStep
        ]);
    }

    /**
     * Update the specified resource in storage.
     * @param ProcedureStepRequest $request
     * @param $id
     * @return \Illuminate\Http\Response
     * @internal param \Illuminate\Http\Request|Request $request
     * @internal param AttendanceMachine $attendanceMachine
     */
    public function update(ProcedureStepRequest $request, $id) {
        //根据id查找记录，
        //把request 传的值，赋值给对应的字段
        //保存当前记录
        //根据操作结果返回不同的json数据
        $procedureStep = ProcedureStep::whereId($id)->first();
        $procedureStep->procedure_id = $request->procedure_id;
        $procedureStep->name = $request->name;
        $procedureStep->approver_user_ids = $request->approver_user_ids;
        $procedureStep->related_user_ids = $request->related_user_ids;
        $procedureStep->remark = $request->remark;
        $procedureStep->enabled = $request->enabled;
        if ($procedureStep->save()) {
            return response()->json(['statusCode' => 200, 'message' => '更新成功！']);
        }

        return response()->json(['statusCode' => 500, 'message' => '更新失败！']);


    }

    /**
     * Remove the specified resource from storage.
     *
     * @param $id
     * @return \Illuminate\Http\Response
     * @internal param Procedure $procedure
     * @internal param AttendanceMachine $attendanceMachine
     */
    public function destroy($id) {
        //根据id查找需要删除的数据
        //进行删除操作
        //返回json 格式的操作结果
        $procedureStep = Procedure::whereId($id)->first();

        if ($procedureStep->delete()) {
            return response()->json(['statusCode' => 200, 'message' => '删除成功！']);
        }

        return response()->json(['statusCode' => 500, 'message' => '删除失败！']);
    }
}
