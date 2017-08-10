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

        return view('procedure_step.index', [
            'js' => 'js/procedure_step/index.js',
            'dialog' => true,
            'datatable' => true
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        return view('procedure_step.create', ['js' => 'js/procedure_step/create.js', 'form' => true]);
    }

    /**
     * Store a newly created resource in storage.
     * @param ProcedureStepRequest $request
     * @return \Illuminate\Http\Response
     * @internal param \Illuminate\Http\Request|Request $request
     */
    public function store(ProcedureStepRequest $request) {
        $procedureStep = $request->all();
        if (!isset($request->approver_user_ids))
            return response()->json(['statusCode' => 500, 'message' => '请选择审批人！']);
        if (!isset($request->related_user_ids))
            return response()->json(['statusCode' => 500, 'message' => '请选择相关人！']);
        $procedureStep['approver_user_ids'] = $this->procedureStep->join_ids($request->approver_user_ids);
        $procedureStep['related_user_ids'] = $this->procedureStep->join_ids($request->related_user_ids);
        $record = $this->procedureStep->where('procedure_id', $procedureStep['procedure_id'])
            ->where('name', $procedureStep['name'])
            ->where('approver_user_ids', $procedureStep['approver_user_ids'])
            ->first();
        if (!empty($record)) {
            $this->result['statusCode'] = self::HTTP_STATUSCODE_INTERNAL_SERVER_ERROR;
            $this->result['message'] = '已经有该记录';
        } else {
            if ($this->procedureStep->create($procedureStep)) {
                $this->result['statusCode'] = self::HTTP_STATUSCODE_OK;
                $this->result['message'] = self::MSG_CREATE_OK;
            } else {
                $this->result['statusCode'] = self::HTTP_STATUSCODE_INTERNAL_SERVER_ERROR;
                $this->result['message'] = '添加失败';
            }
        }
        return response()->json($this->result);

        /*  if (!isset($request->approver_user_ids))
              return response()->json(['statusCode' => 500, 'message' => '请选择审批人！']);

          if (!isset($request->related_user_ids))
              return response()->json(['statusCode' => 500, 'message' => '请选择相关人！']);

          $procedureStep = new ProcedureStep;
          $procedureStep->procedure_id = $request->procedure_id;
          $procedureStep->name = $request->name;
          $procedureStep->approver_user_ids = $procedureStep->join_ids($request->approver_user_ids);
          $procedureStep->related_user_ids = $procedureStep->join_ids($request->related_user_ids);
          $procedureStep->remark = $request->remark;
          $procedureStep->enabled = $request->enabled;
          if ($procedureStep->save()) {
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
        $procedureStep = $this->procedureStep->whereId($id)->first();
        $approver_user_ids = $procedureStep->operate_ids($procedureStep->approver_user_ids);
        $related_user_ids = $procedureStep->operate_ids($procedureStep->related_user_ids);

        //记录返回给view
        return view('procedure_step.show', [
            'procedureStep' => $procedureStep,
            'approver_user_ids' => $approver_user_ids,
            'related_user_ids' => $related_user_ids,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     * @param $id
     * @return \Illuminate\Http\Response
     * @internal param AttendanceMachine $attendanceMachine
     */
    public function edit($id) {
        //记录返回给view
        return view('procedure_step.edit', [
            'js' => 'js/procedure_step/edit.js',
            'procedureStep' => $this->procedureStep->findOrFail($id),
            'form' => true
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
        $procedureStep = $request->all();
        if (!isset($request->approver_user_ids))
            return response()->json(['statusCode' => 500, 'message' => '请选择审批人！']);
        if (!isset($request->related_user_ids))
            return response()->json(['statusCode' => 500, 'message' => '请选择相关人！']);
        $procedureStep['approver_user_ids'] = $this->procedureStep->join_ids($request->approver_user_ids);
        $procedureStep['related_user_ids'] = $this->procedureStep->join_ids($request->related_user_ids);
        $record = $this->procedureStep->where('procedure_id', $procedureStep['procedure_id'])
            ->where('name', $procedureStep['name'])
            ->where('approver_user_ids', $procedureStep['approver_user_ids'])
            ->first();
        if (!empty($record) && ($record->id != $id)) {
            $this->result['statusCode'] = self::HTTP_STATUSCODE_INTERNAL_SERVER_ERROR;
            $this->result['message'] = '已有该记录';
        } else {
            if ($this->procedureStep->findOrFail($id)->update($procedureStep)) {
                $this->result['statusCode'] = self::HTTP_STATUSCODE_OK;
                $this->result['message'] = self::MSG_EDIT_OK;
            } else {
                $this->result['statusCode'] = self::HTTP_STATUSCODE_INTERNAL_SERVER_ERROR;
                $this->result['message'] = '更新失败';
            }
        }
        return response()->json($this->result);

        /*
           if (!isset($request->approver_user_ids))
               return response()->json(['statusCode' => 500, 'message' => '请选择审批人！']);

           if (!isset($request->related_user_ids))
               return response()->json(['statusCode' => 500, 'message' => '请选择相关人！']);

           $procedureStep = $this->procedureStep->whereId($id)->first();
           $procedureStep->procedure_id = $request->procedure_id;
           $procedureStep->name = $request->name;
           $procedureStep->approver_user_ids = $procedureStep->join_ids($request->approver_user_ids);
           $procedureStep->related_user_ids = $procedureStep->join_ids($request->related_user_ids);
           $procedureStep->remark = $request->remark;
           $procedureStep->enabled = $request->enabled;
           if ($procedureStep->save()) {
               return response()->json(['statusCode' => 200, 'message' => '更新成功！']);
           }

           return response()->json(['statusCode' => 500, 'message' => '更新失败！']);*/


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
        if ($this->procedureStep->findOrFail($id)->delete()) {
            $this->result['statusCode'] = self::HTTP_STATUSCODE_OK;
            $this->result['message'] = self::MSG_DEL_OK;
        } else {
            $this->result['statusCode'] = self::HTTP_STATUSCODE_INTERNAL_SERVER_ERROR;
            $this->result['message'] = '删除失败';
        }
        return response()->json($this->result);
    }

    /**
     * @param  $id
     * @return Json user_id:realname
     */
    public function getSchoolEducators($id) {
        $temp = Procedure::whereId($id)->first(['school_id']);
        $data = Educator::with('user')->where('school_id', $temp->school_id)->get()->toArray();
        $educators = [];
        if (!empty($data)) {
            foreach ($data as $v) {
                $educators[$v['user_id']] = $v['user']['realname'];
            }
            return response()->json(['statusCode' => 200, 'educators' => $educators]);
        }
        return response()->json(['statusCode' => 500, 'message' => '查询失败!']);
    }
}
