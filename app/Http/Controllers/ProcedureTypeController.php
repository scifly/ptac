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
     * 显示审批类型列表
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        if (Request::get('draw')) {
            return response()->json($this->procedureType->datatable());
        }
        return parent::output(__METHOD__);
    }

    /**
     * 显示创建新的审批类型的表单
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        //return view('procedure_type.create', ['js' => 'js/procedure_type/create.js', 'form' => true]);
        return $this->output(__METHOD__);
    }

    /**
     * Store a newly created resource in storage.
     * @param ProcedureTypeRequest $request
     * @return \Illuminate\Http\Response
     * @internal param \Illuminate\Http\Request|Request $request
     */
    public function store(ProcedureTypeRequest $request) {
        $input = $request->all();
        $record = $this->procedureType->where('name', $input['name'])->first();
        if (!empty($record)) {
            return response()->json(['statusCode' => self::HTTP_STATUSCODE_INTERNAL_SERVER_ERROR, 'message' => '已经有该记录！']);
        }
        return $this->procedureType->create($request->all()) ? $this->succeed() : $this->fail();

        /*   $pt = $request->all();
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
           return response()->json($this->result);*/

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
        $procedureType = $this->procedureType->find($id);
        if (!$procedureType) {
            return $this->notFound();
        }
        return $this->output(__METHOD__, ['procedureType' => $procedureType]);
    }

    /**
     * Show the form for editing the specified resource.
     * @param $id
     * @return \Illuminate\Http\Response
     * @internal param AttendanceMachine $attendanceMachine
     */
    public function edit($id) {
        //记录返回给view
        $procedureType = $this->procedureType->find($id);
        if (!$procedureType) {
            return $this->notFound();
        }
        return $this->output(__METHOD__, ['procedureType' => $procedureType]);

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
        $input = $request->all();
        $record = $this->procedureType->where('name', $input['name'])->first();
        if (!empty($record) && ($record->id != $id)) {
            return response()->json(['statusCode' => self::HTTP_STATUSCODE_INTERNAL_SERVER_ERROR, 'message' => '已经有该记录！']);
        }
        $procedureType = $this->procedureType->find($id);
        if (!$procedureType) {
            return $this->notFound();
        }
        return $procedureType->update($request->all()) ? $this->succeed() : $this->fail();

        //根据id查找记录，
        //把request 传的值，赋值给对应的字段
        //保存当前记录
        //根据操作结果返回不同的json数据
//        $pt = $request->all();
//        $record = $this->procedureType->where('name', $pt['name'])->first();
//        if (!empty($record) && ($record->id != $id)) {
//            $this->result['statusCode'] = self::HTTP_STATUSCODE_INTERNAL_SERVER_ERROR;
//            $this->result['message'] = '已有该记录';
//        } else {
//            if ($this->procedureType->findOrFail($id)->update($pt)) {
//                $this->result['statusCode'] = self::HTTP_STATUSCODE_OK;
//                $this->result['message'] = self::MSG_EDIT_OK;
//            } else {
//                $this->result['statusCode'] = self::HTTP_STATUSCODE_INTERNAL_SERVER_ERROR;
//                $this->result['message'] = '更新失败';
//            }
//        }
//        return response()->json($this->result);

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

        $procedureType = $this->procedureType->find($id);
        dd($procedureType);
        if (!$procedureType) {
            return $this->notFound();
        }
        return $procedureType->delete() ? $this->succeed() : $this->fail();

        //根据id查找需要删除的数据
        //进行删除操作
        //返回json 格式的操作结果
//        if ($this->procedureType->findOrFail($id)->delete()) {
//            $this->result['statusCode'] = self::HTTP_STATUSCODE_OK;
//            $this->result['message'] = self::MSG_DEL_OK;
//        } else {
//            $this->result['statusCode'] = self::HTTP_STATUSCODE_INTERNAL_SERVER_ERROR;
//            $this->result['message'] = '删除失败';
//        }
//        return response()->json($this->result);
    }
    /* $pt = ProcedureType::whereId($id)->first();

     if ($pt->delete()) {
         return response()->json(['statusCode' => 200, 'message' => '删除成功！']);
     }

     return response()->json(['statusCode' => 500, 'message' => '删除失败！']);
 }*/
}
