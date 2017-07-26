<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProcedureTypeRequest;
use App\Models\ProcedureType;
use Illuminate\Support\Facades\Request;

class ProcedureTypeController extends Controller {
    protected $procedureType;

    function __construct(ProcedureType $procedureType) {
        $this->procedureType = $procedureType;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        if (Request::get('draw')) {
            return response()->json($this->procedureType->datatable());
        }

        return view('procedure_type.index', ['js' => 'js/procedure_type/index.js']);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        return view('procedure_type.create', ['js' => 'js/procedure_type/create.js']);
    }

    /**
     * Store a newly created resource in storage.
     * @param ProcedureTypeRequest $request
     * @return \Illuminate\Http\Response
     * @internal param \Illuminate\Http\Request|Request $request
     */
    public function store(ProcedureTypeRequest $request) {
        //创建一个考勤机空记录
        //将request 请求中包含的表单数据填入空记录对应的字段中
        //保存记录

        $pt = new ProcedureType;
        $pt->name = $request->name;
        $pt->remark = $request->remark;
        $pt->enabled = $request->enabled;
        if ($pt->save()) {
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
        $pt = ProcedureType::find($id);

        //记录返回给view
        return view('procedure_type.show', ['pt' => $pt]);
    }

    /**
     * Show the form for editing the specified resource.
     * @param $id
     * @return \Illuminate\Http\Response
     * @internal param AttendanceMachine $attendanceMachine
     */
    public function edit($id) {
        //根据id 查找单条记录
        $pt = ProcedureType::whereId($id)->first();

        //记录返回给view
        return view('procedure_type.edit', [
            'js' => 'js/procedure_type/edit.js',
            'am' => $pt
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
     * @param ProcedureType $procedure_type
     * @return \Illuminate\Http\Response
     * @internal param AttendanceMachine $attendanceMachine
     */
    public function destroy(ProcedureType $procedure_type) {
        //根据id查找需要删除的数据
        //进行删除操作
        //返回json 格式的操作结果
    }
}
