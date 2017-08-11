<?php

namespace App\Http\Controllers;

use App\Models\ProcedureLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;

class ProcedureLogController extends Controller {
    protected $procedureLog;

    function __construct(ProcedureLog $procedureLog) {
        $this->procedureLog = $procedureLog;
    }


    /**
     * 发起申请页
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('procedure_log.create',[
            'js' => 'js/procedure_log/create.js',
            'form' => true
        ]);
    }

    /**
     * 添加申请信息
     */
    public function store()
    {

    }

    /**
     * 我发起的流程列表
     */
    public function myProcedure(){
        $user_id = 6;
        //查询我发布的流程最后一条log记录
        $ids = $this->procedureLog->select(DB::raw('max(procedure_logs.id) as id'))->where('initiator_user_id',$user_id)->groupBy('first_log_id')->pluck('id')->toArray();
        //根据IDs查询数据
        $data = $this->procedureLog
            ->join('procedures', 'procedures.id', '=', 'procedure_logs.procedure_id')
            ->join('procedure_steps', 'procedure_steps.id', '=', 'procedure_logs.procedure_step_id')
            ->whereIn('procedure_logs.id', $ids)
            ->orderBy('id', 'desc')
            ->select('procedure_logs.id', 'procedure_logs.first_log_id', 'procedures.name', 'procedure_logs.procedure_step_id', 'procedure_steps.name as step_name', 'procedure_logs.initiator_msg', 'procedure_logs.step_status')
            ->get();
        return response()->json($data);
        $sql = 'select max(id) as id,`first_log_id`,`procedure_id`,`initiator_user_id`, `initiator_msg`, `step_status` from procedure_logs group by `first_log_id` order by id desc ';
    }


    /**
     * 待审核的流程列表
     */
    public function pending(){
        $user_id = 3;
        //查询待审核的流程最后一条log记录
        $ids = $this->procedureLog->select(DB::raw('max(procedure_logs.id) as id'))->where('step_status',2)->groupBy('first_log_id')->pluck('id')->toArray();
        //根据IDs查询数据
        $data = $this->procedureLog
            ->join('procedures', 'procedures.id', '=', 'procedure_logs.procedure_id')
            ->join('procedure_steps', 'procedure_steps.id', '=', 'procedure_logs.procedure_step_id')
            ->join('users', 'users.id', '=', 'procedure_logs.initiator_user_id')
            ->whereIn('procedure_logs.id', $ids)
            ->orderBy('id', 'desc')
            ->select('procedure_logs.id', 'procedure_logs.first_log_id', 'users.realname', 'procedures.name', 'procedure_logs.procedure_step_id', 'procedure_steps.name as step_name', 'procedure_steps.approver_user_ids', 'procedure_logs.initiator_msg', 'procedure_logs.step_status')
            ->get();
        $result = [];
        foreach ($data as $val){
            if(in_array($user_id, explode(',',$val->approver_user_ids))){
                $result[]=$val;
            }
        }
        return response()->json($result);
    }

    /**
     *流程详情页
     */
    public function procedureInfo($first_log_id){
        //根据IDs查询数据
        $data = $this->procedureLog
            ->join('procedures', 'procedures.id', '=', 'procedure_logs.procedure_id')
            ->join('procedure_steps', 'procedure_steps.id', '=', 'procedure_logs.procedure_step_id')
            ->join('users', 'users.id', '=', 'procedure_logs.initiator_user_id')
            ->where('procedure_logs.first_log_id', $first_log_id)
            ->orderBy('id', 'desc')
            ->select('procedure_logs.id', 'procedure_logs.first_log_id', 'users.realname', 'procedures.name', 'procedure_logs.procedure_step_id', 'procedure_steps.name as step_name', 'procedure_steps.approver_user_ids', 'procedure_logs.initiator_msg', 'procedure_logs.step_status')
            ->get();
        $result = [
            [
                'name' => '班级审批',
                'status' => 0,
            ],
            [
                'name' => '年级审批',
                'status' => 1,
            ],
            [
                'name' => '校级审批',
                'status' => 2,
            ],
        ];
        return view('procedure_log.procedure_info',[
            'js' => 'js/procedure_log/procedure_info.js',
            'data' => $result
        ]);
    }



    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        if (Request::get('draw')) {
            return response()->json($this->procedureLog->datatable());
        }

        return view('procedure_log.index', [
            'js' => 'js/procedure_log/index.js',
            'dialog' => true,
            'datatable' => true
        ]);
    }


    /**
     * Display the specified resource.
     * @return \Illuminate\Http\Response
     * @internal param AttendanceMachine $attendanceMachine
     */
    public function show($id) {

        //根据id 查找单条记录
        $procedureLog = $this->procedureLog->whereId($id)->first();
        $initiator_medias = $procedureLog->operate_ids($procedureLog->initiator_media_ids);
        $operator_medias = $procedureLog->operate_ids($procedureLog->operator_media_ids);
        $initiator_user = $procedureLog->get_user($procedureLog->initiator_user_id);
        $operator_user = $procedureLog->get_user($procedureLog->operator_user_id);

        //记录返回给view
        return view('procedure_log.show', [
            'procedureLog' => $procedureLog,
            'initiator_user' => $initiator_user,
            'initiator_medias' => $initiator_medias,
            'operator_user' => $operator_user,
            'operator_medias' => $operator_medias
        ]);
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
        if ($this->procedureLog->findOrFail($id)->delete()) {
            $this->result['statusCode'] = self::HTTP_STATUSCODE_OK;
            $this->result['message'] = self::MSG_DEL_OK;
        } else {
            $this->result['statusCode'] = self::HTTP_STATUSCODE_INTERNAL_SERVER_ERROR;
            $this->result['message'] = '删除失败';
        }
        return response()->json($this->result);

        /*  $procedureLog = ProcedureLog::whereId($id)->first();
          if ($procedureLog->delete()) {
              return response()->json(['statusCode' => 200, 'message' => '删除成功！']);
          }

          return response()->json(['statusCode' => 500, 'message' => '删除失败！']);
      }*/
    }
}
