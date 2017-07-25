<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProcedureRequest;
use App\Models\Procedure;
use Illuminate\Support\Facades\Request;

class ProcedureController extends Controller {
    protected $procedure;

    function __construct(Procedure $procedure) {
        $this->procedure = $procedure;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        if (Request::get('draw')) {
            return response()->json($this->procedure->datatable());
        }

        return view('procedure.index', ['js' => 'js/procedure/index.js']);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        return view('procedure.create', ['js' => 'js/procedure/create.js']);
    }

    /**
     * Store a newly created resource in storage.
     * @param ProcedureRequest $request
     * @return \Illuminate\Http\Response
     * @internal param \Illuminate\Http\Request|Request $request
     */
    public function store(ProcedureRequest $request) {
        //创建一个考勤机空记录
        //将request 请求中包含的表单数据填入空记录对应的字段中
        //保存记录

        $procedure = new Procedure;
        $procedure->procedure_type_id = $request->procedure_type_id;
        $procedure->school_id = $request->school_id;
        $procedure->name = $request->name;
        $procedure->remark = $request->remark;
        $procedure->enabled = $request->enabled;
        if ($procedure->save()) {
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
        $procedure = Procedure::find($id);

        //记录返回给view
        return view('procedure.show', ['pt' => $procedure]);
    }

    /**
     * Show the form for editing the specified resource.
     * @param $id
     * @return \Illuminate\Http\Response
     * @internal param AttendanceMachine $attendanceMachine
     */
    public function edit($id) {
        //根据id 查找单条记录
        $procedure = Procedure::whereId($id)->first();

        //记录返回给view
        return view('procedure.edit', [
            'js' => 'js/procedure/edit.js',
            'am' => $procedure
        ]);
    }

    /**
     * Update the specified resource in storage.
     * @return \Illuminate\Http\Response
     * @internal param \Illuminate\Http\Request|Request $request
     * @internal param AttendanceMachine $attendanceMachine
     */
    public function update() {
        //跟进id查找记录，
        //把request 传的值，赋值给对应的字段
        //保存当前记录
        //根据操作结果返回不同的json数据

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Procedure $procedure
     * @return \Illuminate\Http\Response
     * @internal param AttendanceMachine $attendanceMachine
     */
    public function destroy(Procedure $procedure) {
        //根据id查找需要删除的数据
        //进行删除操作
        //返回json 格式的操作结果
    }
}
