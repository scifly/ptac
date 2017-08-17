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

        return view('procedure_type.index', [
            'js' => 'js/procedure_type/index.js',
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
        return view('procedure_type.create', ['js' => 'js/procedure_type/create.js', 'form' => true]);
    }

    /**
     * Store a newly created resource in storage.
     * @param ProcedureTypeRequest $request
     * @return \Illuminate\Http\Response
     * @internal param \Illuminate\Http\Request|Request $request
     */
    public function store(ProcedureTypeRequest $request) {
        $pt = $request->all();
        $record = $this->procedureType->where('name', $pt['name'])->first();
        if (!empty($record)) {
            $this->result['statusCode'] = self::HTTP_STATUSCODE_INTERNAL_SERVER_ERROR;
            $this->result['message'] = '已经有该记录';
        } else {
            if ($this->procedureType->create($pt)) {
                $this->result['statusCode'] = self::HTTP_STATUSCODE_OK;
                $this->result['message'] = self::MSG_CREATE_OK;
            } else {
                $this->result['statusCode'] = self::HTTP_STATUSCODE_INTERNAL_SERVER_ERROR;
                $this->result['message'] = '添加失败';
            }
        }
        return response()->json($this->result);

        /* $pt = new ProcedureType;
         $pt->name = $request->name;
         $pt->remark = $request->remark;
         $pt->enabled = $request->enabled;
         if ($pt->save()) {
             return response()->json(['statusCode' => 200, 'message' => '创建成功！']);
         }
         return response()->json(['statusCode' => 500, 'message' => '创建失败！']);*/
    }

    /**
     * Display the specified resource.
     * @return \Illuminate\Http\Response
     * @internal param AttendanceMachine $attendanceMachine
     */
    public function show($id) {

        //根据id 查找单条记录
        $pt = $this->procedureType->whereId($id)
            ->first(['name','remark','created_at','updated_at','enabled']);


        $pt->enabled = $pt->enabled==1 ? '已启用' : '已禁用' ;

        if ($pt) {
            $this->result['statusCode'] = self::HTTP_STATUSCODE_OK;
            $this->result['showData'] = $pt;
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
        return view('procedure_type.edit', [
            'js' => 'js/procedure_type/edit.js',
            'pt' => $this->procedureType->findOrFail($id),
            'form' => true
        ]);
    }

    /**
     * Update the specified resource in storage.
     * @param ProcedureTypeRequest $request
     * @param $id
     * @return \Illuminate\Http\Response
     * @internal param \Illuminate\Http\Request|Request $request
     * @internal param AttendanceMachine $attendanceMachine
     */
    public function update(ProcedureTypeRequest $request, $id) {
        //根据id查找记录，
        //把request 传的值，赋值给对应的字段
        //保存当前记录
        //根据操作结果返回不同的json数据
        $pt = $request->all();
        $record = $this->procedureType->where('name', $pt['name'])->first();
        if (!empty($record) && ($record->id != $id)) {
            $this->result['statusCode'] = self::HTTP_STATUSCODE_INTERNAL_SERVER_ERROR;
            $this->result['message'] = '已有该记录';
        } else {
            if ($this->procedureType->findOrFail($id)->update($pt)) {
                $this->result['statusCode'] = self::HTTP_STATUSCODE_OK;
                $this->result['message'] = self::MSG_EDIT_OK;
            } else {
                $this->result['statusCode'] = self::HTTP_STATUSCODE_INTERNAL_SERVER_ERROR;
                $this->result['message'] = '更新失败';
            }
        }
        return response()->json($this->result);

        /*$pt = ProcedureType::whereId($id)->first();
        $pt->name = $request->name;
        $pt->remark = $request->remark;
        $pt->enabled = $request->enabled;
        if ($pt->save()) {
            return response()->json(['statusCode' => 200, 'message' => '更新成功！']);
        }

        return response()->json(['statusCode' => 500, 'message' => '更新失败！']);*/

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param $id
     * @return \Illuminate\Http\Response
     * @internal param ProcedureType $procedure_type
     * @internal param AttendanceMachine $attendanceMachine
     */
    public function destroy($id) {
        //根据id查找需要删除的数据
        //进行删除操作
        //返回json 格式的操作结果
        if ($this->procedureType->findOrFail($id)->delete()) {
            $this->result['statusCode'] = self::HTTP_STATUSCODE_OK;
            $this->result['message'] = self::MSG_DEL_OK;
        } else {
            $this->result['statusCode'] = self::HTTP_STATUSCODE_INTERNAL_SERVER_ERROR;
            $this->result['message'] = '删除失败';
        }
        return response()->json($this->result);
    }
       /* $pt = ProcedureType::whereId($id)->first();

        if ($pt->delete()) {
            return response()->json(['statusCode' => 200, 'message' => '删除成功！']);
        }

        return response()->json(['statusCode' => 500, 'message' => '删除失败！']);
    }*/
}
