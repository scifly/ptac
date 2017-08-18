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
            'dialog' => true,
            'datatable' => true,
            'show' => true
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
        //判断，同一个学校的考勤机，不能拥有同名、同设备编号的考勤机
        //保存记录

        $am = new AttendanceMachine;
        $am->name = $request->name;
        $am->location = $request->location;
        $am->school_id = $request->school_id;
        $am->machineid = $request->machineid;
        $am->enabled = $request->enabled;

        $record = $this->attendanceMachine->where('name', $am['name'])
            ->where('school_id', $am['school_id'])
            ->where('machineid', $am['machineid'])
            ->first();

        if (!empty($record)) {
            return response()->json(['statusCode' => self::HTTP_STATUSCODE_INTERNAL_SERVER_ERROR, 'message' => '考勤设备已存在！']);
        }

        if ($am->save()) {
            $this->result['statusCode'] = self::HTTP_STATUSCODE_OK;
            $this->result['message'] = self::MSG_CREATE_OK;
        }else {
            $this->result['statusCode'] = self::HTTP_STATUSCODE_INTERNAL_SERVER_ERROR;
            $this->result['message'] = self::MSG_BAD_REQUEST;
        }

        return response()->json($this->result);
    }

    /**
     * Display the specified resource.
     * @return \Illuminate\Http\Response
     * @internal param AttendanceMachine $attendanceMachine
     */
    public function show($id) {
        //根据id 查找单条记录

        $am = $this->attendanceMachine->whereId($id)
            ->first([
                'name',
                'location',
                'school_id',
                'machineid',
                'created_at',
                'updated_at',
                'enabled'
            ]);

        $am->school_id = $am->school->name;
        $am->enabled = $am->enabled==1 ? '已启用' : '已禁用' ;
        if ($am) {
            $this->result['statusCode'] = self::HTTP_STATUSCODE_OK;
            $this->result['showData'] = $am;
        } else {
            $this->result['statusCode'] = self::HTTP_STATUSCODE_INTERNAL_SERVER_ERROR;
            $this->result['message'] = '';
        }

        return response()->json($this->result);
    }

    /**
     * Show the form for editing the specified resource.
     * @param $id
     * @return \Illuminate\Http\Response
     * @internal param AttendanceMachine $attendanceMachine
     */
    public function edit($id) {
        //根据id 查找单条记录
        $am = AttendanceMachine::findOrFail($id);

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

        $am = AttendanceMachine::findOrFail($id);
        $am->name = $request->name;
        $am->location = $request->location;
        $am->school_id = $request->school_id;
        $am->machineid = $request->machineid;
        $am->enabled = $request->enabled;
        if ($am->save()) {
            $this->result['statusCode'] = self::HTTP_STATUSCODE_OK;
            $this->result['message'] = self::MSG_EDIT_OK;
        }else {
            $this->result['statusCode'] = self::HTTP_STATUSCODE_INTERNAL_SERVER_ERROR;
            $this->result['message'] = self::MSG_BAD_REQUEST;
        }

        return response()->json($this->result);

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
        $am = AttendanceMachine::findOrFail($id);

        if ($am->delete()) {
            $this->result['statusCode'] = self::HTTP_STATUSCODE_OK;
            $this->result['message'] = self::MSG_DEL_OK;
        }else {
            $this->result['statusCode'] = self::HTTP_STATUSCODE_INTERNAL_SERVER_ERROR;
            $this->result['message'] = self::MSG_BAD_REQUEST;
        }

        return response()->json($this->result);
    }
}
