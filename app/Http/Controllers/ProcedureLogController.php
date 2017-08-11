<?php

namespace App\Http\Controllers;

use App\Models\ProcedureLog;
use Illuminate\Support\Facades\Request;

class ProcedureLogController extends Controller {
    protected $procedureLog;

    function __construct(ProcedureLog $procedureLog) {
        $this->procedureLog = $procedureLog;
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('procedure_log.create',[
            'js' => 'js/score_range/create.js',
            'form' => true
        ]);
    }

    /**
     * 添加流程信息
     */
    public function store()
    {

    }


    /**
     * 我发起的流程列表
     */
    public function myProcedure(){
        $user_id = 6;
        $sql = 'select max(id) as id,`first_log_id`,`procedure_id`,`initiator_user_id`, `initiator_msg`, `step_status` from procedure_logs group by `first_log_id` order by id desc ';
    }


    /**
     * 待审核的流程列表
     */
    public function pending(){

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
        $procedureLog = ProcedureLog::whereId($id)->first();
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
        $procedureLog = ProcedureLog::whereId($id)->first();

        if ($procedureLog->delete()) {
            return response()->json(['statusCode' => 200, 'message' => '删除成功！']);
        }

        return response()->json(['statusCode' => 500, 'message' => '删除失败！']);
    }
}
