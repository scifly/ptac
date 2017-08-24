<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProcedureStepRequest;
use App\Models\Educator;
use App\Models\Procedure;
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
     * Store a newly created resource in storage.
     * @param ProcedureStepRequest $request
     * @return \Illuminate\Http\Response
     * @internal param \Illuminate\Http\Request|Request $request
     */
    public function store(ProcedureStepRequest $request) {
        $input = $request->all();
        $input['approver_user_ids'] = $this->procedureStep->join_ids($request->approver_user_ids);
        $input['related_user_ids'] = $this->procedureStep->join_ids($request->related_user_ids);
        $record = $this->procedureStep->where('procedure_id', $input['procedure_id'])
            ->where('name', $input['name'])
            ->where('approver_user_ids', $input['approver_user_ids'])
            ->first();
        if (!empty($record)) {
            return response()->json(['statusCode' => self::HTTP_STATUSCODE_INTERNAL_SERVER_ERROR, 'message' => '已经有该记录！']);
        }
        return $this->procedureStep->create($input) ? $this->succeed() : $this->fail();

        /*$procedureStep = $request->all();
        $procedureStep['approver_user_ids'] = $this->procedureStep->join_ids($request->approver_user_ids);
        $procedureStep['related_user_ids'] = $this->procedureStep->join_ids($request->related_user_ids);
        $record = $this->procedureStep->where('procedure_id', $procedureStep['procedure_id'])
            ->where('name', $procedureStep['name'])
            ->where('approver_user_ids', $procedureStep['approver_user_ids'])
            ->first();
        if (!empty($record)) {
            return response()->json(['statusCode' => self::HTTP_STATUSCODE_INTERNAL_SERVER_ERROR, 'message' => '已经有该记录！']);
        }
        if ($this->procedureStep->create($procedureStep)) {
            $this->result['statusCode'] = self::HTTP_STATUSCODE_OK;
            $this->result['message'] = self::MSG_CREATE_OK;
        } else {
            $this->result['statusCode'] = self::HTTP_STATUSCODE_INTERNAL_SERVER_ERROR;
            $this->result['message'] = '添加失败';
        }
        return response()->json($this->result);*/
    }

    /**
     * Display the specified resource.
     * @return \Illuminate\Http\Response
     * @internal param AttendanceMachine $attendanceMachine
     */
    public function show($id) {

        $procedureStep = $this->procedureStep->find($id);
        if (!$procedureStep) {
            return $this->notFound();
        }
        return $this->output(__METHOD__, ['procedureStep' => $procedureStep]);

       /* //根据id 查找单条记录
        $ps = $this->procedureStep->whereId($id)
            ->first([
                'procedure_id',
                'name',
                'approver_user_ids',
                'related_user_ids',
                'remark',
                'created_at',
                'updated_at',
                'enabled'
            ]);

        $ps->procedure_id = $ps->procedure->name;
        $ps->approver_user_ids = $ps->user_names($ps->approver_user_ids);
        $ps->related_user_ids = $ps->user_names($ps->related_user_ids);
        $ps->enabled = $ps->enabled==1 ? '已启用' : '已禁用' ;

        if ($ps) {
            $this->result['statusCode'] = self::HTTP_STATUSCODE_OK;
            $this->result['showData'] = $ps;
        } else {
            $this->result['statusCode'] = self::HTTP_STATUSCODE_INTERNAL_SERVER_ERROR;
            $this->result['message'] = '';
        }

        return response()->json($this->result);*/
    }

    /**
     * Show the form for editing the specified resource.
     * @param $id
     * @return \Illuminate\Http\Response
     * @internal param AttendanceMachine $attendanceMachine
     */
    public function edit($id) {
        $procedureStep = $this->procedureStep->find($id);
        if (!$procedureStep) {
            return $this->notFound();
        }
        return $this->output(__METHOD__, ['procedureStep' => $procedureStep]);
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
        $input = $request->all();
        $input['approver_user_ids'] = $this->procedureStep->join_ids($request->approver_user_ids);
        $input['related_user_ids'] = $this->procedureStep->join_ids($request->related_user_ids);
        $record = $this->procedureStep->where('procedure_id', $input['procedure_id'])
            ->where('name', $input['name'])
            ->where('approver_user_ids', $input['approver_user_ids'])
            ->first();
        if (!empty($record) && ($record->id != $id)) {
            return response()->json(['statusCode' => self::HTTP_STATUSCODE_INTERNAL_SERVER_ERROR, 'message' => '已经有该记录！']);
        }
        $procedureStep = $this->procedureStep->find($id);
        if (!$procedureStep) {
            return $this->notFound();
        }
        return $procedureStep->update($input) ? $this->succeed() : $this->fail();

     /*   $procedureStep = $request->all();
        $procedureStep['approver_user_ids'] = $this->procedureStep->join_ids($request->approver_user_ids);
        $procedureStep['related_user_ids'] = $this->procedureStep->join_ids($request->related_user_ids);
        $record = $this->procedureStep->where('procedure_id', $procedureStep['procedure_id'])
            ->where('name', $procedureStep['name'])
            ->where('approver_user_ids', $procedureStep['approver_user_ids'])
            ->first();
        if (!empty($record) && ($record->id != $id)) {
            return response()->json(['statusCode' => self::HTTP_STATUSCODE_INTERNAL_SERVER_ERROR, 'message' => '已经有该记录！']);

        }
        if ($this->procedureStep->findOrFail($id)->update($procedureStep)) {
            $this->result['statusCode'] = self::HTTP_STATUSCODE_OK;
            $this->result['message'] = self::MSG_EDIT_OK;
        } else {
            $this->result['statusCode'] = self::HTTP_STATUSCODE_INTERNAL_SERVER_ERROR;
            $this->result['message'] = '更新失败';
        }
        return response()->json($this->result);*/
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
        $procedureStep = $this->procedureStep->find($id);
        if (!$procedureStep) {
            return $this->notFound();
        }
        return $procedureStep->delete() ? $this->succeed() : $this->fail();
    }

    /**
     * @param  $id
     * @return Jsonobj user_id:realname
     */
    public function getSchoolEducators($id) {
        $temp = Procedure::whereId($id)->first(['school_id']);
        $data = Educator::with('user')->where('school_id', $temp->school_id)->get()->toArray();
        $educators = [];
        if (!empty($data)) {
            foreach ($data as $v) {
                $educators[$v['user_id']] = $v['user']['username'];
            }
            return response()->json(['statusCode' => 200, 'educators' => $educators]);
        }
        return response()->json(['statusCode' => 500, 'message' => '查询失败!']);
    }
}
