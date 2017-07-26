<?php

namespace App\Http\Controllers;

use App\Http\Requests\AttendanceMachineRequest;
use App\Models\AttendanceMachine;
use Illuminate\Support\Facades\Request;

class AttendanceMachineController extends Controller {
    protected $attendanceMachine;

    function __construct(AttendanceMachine $attendanceMachine) {
        $this->attendanceMachine = $attendanceMachine;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        if (Request::get('draw')) {
            return response()->json($this->attendanceMachine->datatable());
        }

        return view('attendance_machine.index', [
            'js' => 'js/attendance_machine/index.js',
            'dialog' => true
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        return view('attendance_machine.create', ['js' => 'js/attendance_machine/create.js']);
    }

    /**
     * Store a newly created resource in storage.
     * @param AttendanceMachineRequest $request
     * @return \Illuminate\Http\Response
     * @internal param \Illuminate\Http\Request|Request $request
     */
    public function store(AttendanceMachineRequest $request) {
        //创建一个考勤机空记录
        //将request 请求中包含的表单数据填入空记录对应的字段中
        //保存记录

        $am = new AttendanceMachine;
        $am->name = $request->name;
        $am->location = $request->location;
        $am->school_id = $request->school_id;
        $am->machineid = $request->machineid;
        $am->enabled = $request->enabled;
        if ($am->save()) {
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
        $am = AttendanceMachine::find($id);

        //记录返回给view
        return view('attendance_machine.show', ['am' => $am]);
    }

    /**
     * Show the form for editing the specified resource.
     * @param $id
     * @return \Illuminate\Http\Response
     * @internal param AttendanceMachine $attendanceMachine
     */
    public function edit($id) {
        //根据id 查找单条记录
        $am = AttendanceMachine::whereId($id)->first();

        //记录返回给view
        return view('attendance_machine.edit', [
            'js' => 'js/attendance_machine/edit.js',
            'am' => $am
        ]);
    }

    /**
     * Update the specified resource in storage.
     * @param AttendanceMachineRequest $request
     * @param $id
     * @return \Illuminate\Http\Response
     * @internal param \Illuminate\Http\Request|Request $request
     * @internal param AttendanceMachine $attendanceMachine
     */
    public function update(AttendanceMachineRequest $request, $id) {
        //跟进id查找记录，
        //把request 传的值，赋值给对应的字段
        //保存当前记录
        //根据操作结果返回不同的json数据

        $am = AttendanceMachine::whereId($id)->first();
        $am->name = $request->name;
        $am->location = $request->location;
        $am->school_id = $request->school_id;
        $am->machineid = $request->machineid;
        $am->enabled = $request->enabled;
        if ($am->save()) {
            return response()->json(['statusCode' => 200, 'message' => '创建成功！']);
        }

        return response()->json(['statusCode' => 500, 'message' => '创建失败！']);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param $id
     * @return \Illuminate\Http\Response
     * @internal param AttendanceMachine $attendanceMachine
     */
    public function destroy($id) {
        //根据id查找需要删除的数据
        //进行删除操作
        //返回json 格式的操作结果
        $am = AttendanceMachine::whereId($id)->first();

        if ($am->delete()) {
            return response()->json(['statusCode' => 200, 'message' => '删除成功！']);
        }

        return response()->json(['statusCode' => 500, 'message' => '删除失败！']);
    }
}
