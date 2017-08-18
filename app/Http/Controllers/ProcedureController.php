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

        return view('procedure.index', [
            'js' => 'js/procedure/index.js',
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
        return view('procedure.create', ['js' => 'js/procedure/create.js', 'form' => true]);
    }

    /**
     * Store a newly created resource in storage.
     * @param ProcedureRequest $request
     * @return \Illuminate\Http\Response
     * @internal param \Illuminate\Http\Request|Request $request
     */
    public function store(ProcedureRequest $request) {
        $procedure = $request->all();
        $record = $this->procedure->where('procedure_type_id', $procedure['procedure_type_id'])
            ->where('school_id', $procedure['school_id'])
            ->where('name', $procedure['name'])
            ->first();
        if (!empty($record)) {
            $this->result['statusCode'] = self::HTTP_STATUSCODE_INTERNAL_SERVER_ERROR;
            $this->result['message'] = '已经有该记录';
        } else {
            if ($this->procedure->create($procedure)) {
                $this->result['statusCode'] = self::HTTP_STATUSCODE_OK;
                $this->result['message'] = self::MSG_CREATE_OK;
            } else {
                $this->result['statusCode'] = self::HTTP_STATUSCODE_INTERNAL_SERVER_ERROR;
                $this->result['message'] = '添加失败';
            }
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
        $procedure = $this->procedure->whereId($id)
            ->first(['name','school_id','procedure_type_id','remark','created_at','updated_at','enabled']);

        $procedure->school_id = $procedure->school->name;
        $procedure->procedure_type_id = $procedure->procedureType->name;
        $procedure->enabled = $procedure->enabled==1 ? '已启用' : '已禁用' ;

        if ($procedure) {
            $this->result['statusCode'] = self::HTTP_STATUSCODE_OK;
            $this->result['showData'] = $procedure;
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
        //记录返回给view
        return view('procedure.edit', [
            'js' => 'js/procedure/edit.js',
            'procedure' => $this->procedure->findOrFail($id),
            'form' => true
        ]);
    }

    /**
     * Update the specified resource in storage.
     * @param ProcedureRequest $request
     * @param $id
     * @return \Illuminate\Http\Response
     * @internal param \Illuminate\Http\Request|Request $request
     * @internal param AttendanceMachine $attendanceMachine
     */
    public function update(ProcedureRequest $request, $id) {

        $procedure = $request->all();

        $record = $this->procedure->where('procedure_type_id', $procedure['procedure_type_id'])
            ->where('school_id', $procedure['school_id'])
            ->where('name', $procedure['name'])
            ->first();
        if (!empty($record) && ($record->id != $id)) {
            $this->result['statusCode'] = self::HTTP_STATUSCODE_INTERNAL_SERVER_ERROR;
            $this->result['message'] = '已有该记录';
        } else {
            if ($this->procedure->findOrFail($id)->update($procedure)) {
                $this->result['statusCode'] = self::HTTP_STATUSCODE_OK;
                $this->result['message'] = self::MSG_EDIT_OK;
            } else {
                $this->result['statusCode'] = self::HTTP_STATUSCODE_INTERNAL_SERVER_ERROR;
                $this->result['message'] = '更新失败';
            }
        }
        return response()->json($this->result);

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
        //根据id查找需要删除表数据
        //进行删除操作
        //返回json 格式的操作结果
        if ($this->procedure->findOrFail($id)->delete()) {
            $this->result['statusCode'] = self::HTTP_STATUSCODE_OK;
            $this->result['message'] = self::MSG_DEL_OK;
        } else {
            $this->result['statusCode'] = self::HTTP_STATUSCODE_INTERNAL_SERVER_ERROR;
            $this->result['message'] = '删除失败';
        }
        return response()->json($this->result);
    }
}
