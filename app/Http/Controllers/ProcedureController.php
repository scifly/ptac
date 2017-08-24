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
        return parent::output(__METHOD__);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        return $this->output(__METHOD__);
    }

    /**
     * @param ProcedureRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(ProcedureRequest $request) {
        $input = $request->all();
        $record = $this->procedure->where('procedure_type_id', $input['procedure_type_id'])
            ->where('school_id', $input['school_id'])
            ->where('name', $input['name'])
            ->first();
        if (!empty($record)) {
            return response()->json(['statusCode' => self::HTTP_STATUSCODE_INTERNAL_SERVER_ERROR, 'message' => '已经有该记录！']);
        }
        return $this->procedure->create($request->all()) ? $this->succeed() : $this->fail();
    }

    /**
     * Display the specified resource.
     * @return \Illuminate\Http\Response
     * @internal param AttendanceMachine $attendanceMachine
     */
    public function show($id) {

        $procedure = $this->procedure->find($id);
        if (!$procedure) {
            return $this->notFound();
        }
        return $this->output(__METHOD__, ['procedure' => $procedure]);
        //根据id 查找单条记录
        /* $procedure = $this->procedure->whereId($id)
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

         return response()->json($this->result);*/
    }

    /**
     * @param $id
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public function edit($id) {
        $procedure = $this->procedure->find($id);
        if (!$procedure) {
            return $this->notFound();
        }
        return $this->output(__METHOD__, ['procedure' => $procedure]);
    }

    /**
     * @param ProcedureRequest $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(ProcedureRequest $request, $id) {
        $input = $request->all();
        $record = $this->procedure->where('procedure_type_id', $input['procedure_type_id'])
            ->where('school_id', $input['school_id'])
            ->where('name', $input['name'])
            ->first();
        if (!empty($record) && ($record->id != $id)) {
            return response()->json(['statusCode' => self::HTTP_STATUSCODE_INTERNAL_SERVER_ERROR, 'message' => '已经有该记录！']);
        }

        $procedure = $this->procedure->find($id);
        if (!$procedure) {
            return $this->notFound();
        }
        return $procedure->update($request->all()) ? $this->succeed() : $this->fail();
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id) {
        $procedure = $this->procedure->find($id);
        if (!$procedure) {
            return $this->notFound();
        }
        return $procedure->delete() ? $this->succeed() : $this->fail();
    }
}
