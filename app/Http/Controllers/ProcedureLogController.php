<?php

namespace App\Http\Controllers;

use App\Models\Media;
use App\Models\ProcedureLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Storage;

class ProcedureLogController extends Controller {
    protected $procedureLog;

    function __construct(ProcedureLog $procedureLog) {
        $this->procedureLog = $procedureLog;
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
            ->with('procedure', 'procedure_step')
            ->whereIn('id', $ids)
            ->orderBy('id', 'desc')
            ->get();
        return response()->json($data);
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
            ->with('procedure', 'procedure_step', 'initiator_user')
            ->whereIn('procedure_logs.id', $ids)
            ->orderBy('id', 'desc')
            ->get();
        $result = [];
        foreach ($data as $val){
            if(in_array($user_id, explode(',',$val->procedure_step->approver_user_ids))){
                $result[]=$val;
            }
        }
        return response()->json($result);
    }

    /**
     *流程详情页
     * @param $first_log_id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function procedureInfo($first_log_id){
        //根据IDs查询数据
        $data = $this->procedureLog
            ->with('procedure', 'procedure_step', 'initiator_user', 'operator_user')
            ->where('first_log_id', $first_log_id)
            ->orderBy('id', 'asc')
            ->get();
        return view('procedure_log.procedure_info',[
            'js' => 'js/procedure_log/procedure_info.js',
            'data' => $data
        ]);
    }


    /**
     * 发起申请页
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $procedure_id = DB::table('procedures')->pluck('name', 'id');
        return view('procedure_log.create',[
            'js' => 'js/procedure_log/create.js',
            'procedure_id' => $procedure_id,
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
     * 上传文件
     */
    public function uploadMedias(){
        $files = Request::file('medias');

        if (empty($files)){
            $result['statusCode'] = 0;
            $result['message'] = '您还未选择文件！';
        }else{
            $result['data']=array();
            $mes = [];
            foreach ($files  as $key=>$v){
                if ($v->isValid()) {
                    // 获取文件相关信息
                    $originalName = $v->getClientOriginalName(); // 文件原名
                    $ext = $v->getClientOriginalExtension();     // 扩展名
                    $realPath = $v->getRealPath();   // 临时文件的绝对路径
                    $type = $v->getClientMimeType();     // 文件类型
                    // 上传图片
                    $filename =  uniqid() . '.' . $ext;
                    // 使用我们新建的uploads本地存储空间（目录）
                    $bool = Storage::disk('uploads')->put($filename,file_get_contents($realPath));

                    $filePath = '/storage/app/uploads/'.date('Y-m-d').'/'.$filename;
                    $data = [
                        'path' => $filePath,
                        'remark' => '流程相关附件',
                        'media_type_id' => $type,
                        'enabled' => '1',
                    ];
                    $mediaId = Media::insertGetId($data);
                    $mes [] = [
                        'id' => $mediaId,
                        'path' => $filePath,
                        'type' => $type,
                        'filename' => $originalName,
                    ];
                }
            }
            $result['statusCode'] = 1;
            $result['message'] = '上传成功！';
            $result['data'] = $mes;
        }
        return response()->json($result);

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