<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProcedureLogRequest;
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
        if (Request::get('draw')) {
            $user_id = 6;
            //查询我发布的流程最后一条log记录
            $ids = $this->procedureLog->select(DB::raw('max(procedure_logs.id) as id'))
                ->where('initiator_user_id',$user_id)
                ->groupBy('first_log_id')
                ->pluck('id')->toArray();    
            $where = 'ProcedureLog.id in (' . implode(',', $ids) . ')';

            return response()->json($this->procedureLog->datatable($where));

        }
        return view('procedure_log.index', [
            'js' => 'js/procedure_log/index.js',
            'dialog' => true,
            'datatable' => true
        ]);

//        $user_id = 6;
//        //查询我发布的流程最后一条log记录
//        $ids = $this->procedureLog->select(DB::raw('max(procedure_logs.id) as id'))
//            ->where('initiator_user_id',$user_id)
//            ->groupBy('first_log_id')
//            ->pluck('id')->toArray();
//        //根据IDs查询数据
//        $data = $this->procedureLog
//            ->with('procedure', 'procedure_step')
//            ->whereIn('id', $ids)
//            ->orderBy('id', 'desc')
//            ->get();
//        return response()->json($data);
    }


    /**
     * 待审核的流程列表
     */
    public function pending(){
        if (Request::get('draw')) {
            $user_id = 3;
            //查询待审核的流程最后一条log记录
            $ids = $this->procedureLog->select(DB::raw('max(procedure_logs.id) as id'))
                ->where('step_status',2)
                ->groupBy('first_log_id')
                ->pluck('id')
                ->toArray();
            $where = 'ProcedureLog.id in (' . implode(',', $ids) . ') and FIND_IN_SET('. $user_id .',ProcedureStep.related_user_ids)';

            return response()->json($this->procedureLog->datatable($where));

        }
        return view('procedure_log.index', [
            'js' => 'js/procedure_log/index.js',
            'dialog' => true,
            'datatable' => true
        ]);

//        $user_id = 3;
//        //查询待审核的流程最后一条log记录
//        $ids = $this->procedureLog->select(DB::raw('max(procedure_logs.id) as id'))
//            ->where('step_status',2)
//            ->groupBy('first_log_id')
//            ->pluck('id')
//            ->toArray();
//        //根据IDs查询数据
//        $data = $this->procedureLog
//            ->with('procedure', 'procedure_step', 'initiator_user')
//            ->whereIn('procedure_logs.id', $ids)
//            ->orderBy('id', 'desc')
//            ->get();
//        $result = [];
//        foreach ($data as $val){
//            if(in_array($user_id, explode(',',$val->procedure_step->approver_user_ids))){
//                $result[]=$val;
//            }
//        }
//        return response()->json($result);
    }

    /**
     * 流程详情页
     *
     * @param $first_log_id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function procedureInfo($first_log_id){
        $user_id = 7;
        //根据IDs查询数据
        $data = $this->procedureLog
            ->with('procedure', 'procedure_step', 'initiator_user', 'operator_user')
            ->where('first_log_id', $first_log_id)
            ->orderBy('id', 'asc')
            ->get();
        return view('procedure_log.procedure_info',[
            'js' => 'js/procedure_log/procedure_info.js',
            'data' => $data,
            'user_id' => $user_id
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
     *
     * @param ProcedureLogRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(ProcedureLogRequest $request)
    {
        $user_id = 6;
        $media_ids = $request->input('media_ids');
        $procedure_step = DB::table('procedure_steps')
            ->where('procedure_id', $request->input('procedure_id'))
            ->orderBy('id','asc')
            ->first();
        $data = [
            'procedure_id' => $request->input('procedure_id'),
            'initiator_user_id' => $user_id,
            'procedure_step_id' => $procedure_step->id,
            'operator_user_id' => 0,
            'operator_msg' => 0,
            'operator_media_ids' => 0,
            'step_status' => 2,
            'first_log_id' => 0,
            'initiator_msg' => $request->input('initiator_msg'),
            'initiator_media_ids' => empty($media_ids) ? 0 : implode(',', $media_ids)
        ];

        if($id = $this->procedureLog->insertGetId($data))
        {
            $this->procedureLog->where('id', $id)->update(['first_log_id' => $id]);
            $this->result['statusCode'] = self::HTTP_STATUSCODE_OK;
            $this->result['message'] = self::MSG_CREATE_OK;
        } else {
            $this->result['statusCode'] = self::HTTP_STATUSCODE_INTERNAL_SERVER_ERROR;
            $this->result['message'] = '';
        }
        return response()->json($this->result);
    }

    /**
     * 审批申请
     */
    public function decision()
    {
        $user_id = 3;
        $request = Request::all();
        $update = $this->procedureLog->where('id', $request['id'])
            ->update([
                'step_status' => $request['step_status'],
                'operator_user_id' => $user_id,
                'operator_msg' => $request['operator_msg'],
                'operator_media_ids' => empty($request['media_ids']) ? 0 : implode(',', $request['media_ids'])
            ]);

        if($update){
            if($request['step_status'] == 0){
                $procedure_step = DB::table('procedure_steps')->where([
                    ['procedure_id', '=', $request['procedure_id']],
                    ['id', '>', $request['procedure_step_id']]
                ])->orderBy('id','asc')->first();
                if(!empty($procedure_step)){
                    $data = [
                        'procedure_id' => $request['procedure_id'],
                        'initiator_user_id' => $request['initiator_user_id'],
                        'procedure_step_id' => $procedure_step->id,
                        'operator_user_id' => 0,
                        'operator_msg' => 0,
                        'operator_media_ids' => 0,
                        'step_status' => 2,
                        'first_log_id' => $request['first_log_id'],
                        'initiator_msg' => $request['initiator_msg'],
                        'initiator_media_ids' => empty($request['initiator_media_ids']) ? 0 : $request['initiator_media_ids']
                    ];
                    $this->procedureLog->insertGetId($data);
                }
            }
            $result['statusCode'] = self::HTTP_STATUSCODE_OK;
            $result['message'] = '保存成功';
        }else{
            $result['statusCode'] = self::HTTP_STATUSCODE_INTERNAL_SERVER_ERROR;
            $result['message'] = '请求失败';
        }
        return response()->json($result);
    }


    /**
     * 上传文件
     */
    public function uploadMedias(){
        $files = Request::file('medias');
        if (empty($files)){
            $result['statusCode'] = 500;
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
                        'media_type_id' => 1,
                        'enabled' => '1',
                    ];
                    $mediaId = Media::insertGetId($data);
                    $mes [] = [
                        'id' => $mediaId,
                        'path' => $filePath,
                        'type' => $ext,
                        'filename' => $originalName,
                    ];
                }
            }
            $result['statusCode'] = 200;
            $result['message'] = '上传成功！';
            $result['data'] = $mes;
        }
        return response()->json($result);
    }

    /**
     * 删除上传的文件
     */
    public function deleteMedias($id){
        $path = Media::where('id',$id)->value('path');
        $path_arr = explode("/",$path);
        Storage::disk('uploads')->delete($path_arr[5]);

        if(Media::where('id',$id)->delete()){
            $result['statusCode'] = self::HTTP_STATUSCODE_OK;
            $result['message'] = self::MSG_DEL_OK;
        }else{
            $result['statusCode'] = self::HTTP_STATUSCODE_INTERNAL_SERVER_ERROR;
            $result['message'] = self::MSG_BAD_REQUEST;
        }
        return response()->json($result);
    }

}