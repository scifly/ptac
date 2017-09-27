<?php
namespace App\Http\Controllers;

use App\Helpers\ControllerTrait;
use App\Http\Requests\ProcedureLogRequest;
use App\Models\ProcedureLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Storage;

/**
 * 申请/审批
 *
 * Class ProcedureLogController
 * @package App\Http\Controllers
 */
class ProcedureLogController extends Controller {
    
    use ControllerTrait;
    
    protected $procedureLog;
    
    function __construct(ProcedureLog $procedureLog) {
        $this->procedureLog = $procedureLog;
    }
    
    /**
     * 我发起的流程审批列表
     *
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public function index() {
        
        if (Request::get('draw')) {
            $userId = 6;
            //查询我发布的流程最后一条log记录
            $ids = $this->procedureLog->select(DB::raw('max(procedure_logs.id) as id'))
                ->where('initiator_user_id', $userId)
                ->groupBy('first_log_id')
                ->pluck('id')->toArray();
            $where = 'ProcedureLog.id in (' . implode(',', $ids) . ')';
            return response()->json($this->procedureLog->datatable($where));
            
        }
        
        return $this->output(__METHOD__);
        
    }
    
    /**
     * 待审核的流程审批列表
     *
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public function pending() {
        
        if (Request::get('draw')) {
            $userId = 3;
            //查询待审核的流程最后一条log记录
            $ids = $this->procedureLog->select(DB::raw('max(procedure_logs.id) as id'))
                ->where('step_status', 2)
                ->groupBy('first_log_id')
                ->pluck('id')
                ->toArray();
            $where = 'ProcedureLog.id in (' . implode(',', $ids) . ') and FIND_IN_SET(' . $userId . ',ProcedureStep.approver_user_ids)';
            return response()->json($this->procedureLog->datatable($where));
            
        }
    }
    
    /**
     * 相关流程列表
     *
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public function related() {
        
        if (Request::get('draw')) {
            $userId = 3;
            $where = '(FIND_IN_SET(' . $userId . ',ProcedureStep.related_user_ids) or FIND_IN_SET(' . $userId . ',ProcedureStep.approver_user_ids))';
            
            return response()->json($this->procedureLog->datatable($where));
            
        }
        
        return $this->output(__METHOD__);
        
    }
    
    /**
     * 流程审批详情
     *
     * @param $firstLogId
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public function show($firstLogId) {
        
        $userId = 7;
        //根据IDs查询数据
        $data = $this->procedureLog
            ->with('procedure', 'procedure_step', 'initiator_user', 'operator_user')
            ->where('first_log_id', $firstLogId)
            ->orderBy('id', 'asc')
            ->get();
        
        return $this->output(__METHOD__, [
            'js'      => 'js/procedure_log/show.js',
            'data'    => $data,
            'user_id' => $userId,
        ]);
        
    }
    
    /**
     * 发起申请
     *
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public function create() {
        
        $procedureId = DB::table('procedures')->pluck('name', 'id');
        
        return $this->output(__METHOD__, ['procedure_id' => $procedureId]);
        
    }
    
    /**
     * 保存申请信息
     *
     * @param ProcedureLogRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(ProcedureLogRequest $request) {
        
        $userId = 6;
        $mediaIds = $request->input('media_ids');
        $procedureStep = DB::table('procedure_steps')
            ->where('procedure_id', $request->input('procedure_id'))
            ->orderBy('id', 'asc')
            ->first();
        $data = [
            'procedure_id'        => $request->input('procedure_id'),
            'initiator_user_id'   => $userId,
            'procedure_step_id'   => $procedureStep->id,
            'operator_user_id'    => 0,
            'operator_msg'        => 0,
            'operator_media_ids'  => 0,
            'step_status'         => 2,
            'first_log_id'        => 0,
            'initiator_msg'       => $request->input('initiator_msg'),
            'initiator_media_ids' => empty($mediaIds) ? 0 : implode(',', $mediaIds),
        ];
        if ($id = $this->procedureLog->insertGetId($data)) {
            $this->procedureLog->where('id', $id)->update(['first_log_id' => $id]);
            
            return $this->succeed();
        }
        
        return $this->fail();
        
    }
    
    /**
     * 审批申请
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function decision() {
        
        $userId = 3;
        $request = Request::all();
        $update = $this->procedureLog->where('id', $request['id'])
            ->update([
                'step_status'        => $request['step_status'],
                'operator_user_id'   => $userId,
                'operator_msg'       => $request['operator_msg'],
                'operator_media_ids' => empty($request['media_ids']) ? 0 : implode(',', $request['media_ids']),
            ]);
        if (!$update) {
            return $this->fail();
        }
        if ($request['step_status'] == 0) {
            $procedureStep = DB::table('procedure_steps')->where([
                ['procedure_id', '=', $request['procedure_id']],
                ['id', '>', $request['procedure_step_id']],
            ])->orderBy('id', 'asc')->first();
            if (!empty($procedureStep)) {
                $data = [
                    'procedure_id'        => $request['procedure_id'],
                    'initiator_user_id'   => $request['initiator_user_id'],
                    'procedure_step_id'   => $procedureStep->id,
                    'operator_user_id'    => 0,
                    'operator_msg'        => 0,
                    'operator_media_ids'  => 0,
                    'step_status'         => 2,
                    'first_log_id'        => $request['first_log_id'],
                    'initiator_msg'       => $request['initiator_msg'],
                    'initiator_media_ids' => empty($request['initiator_media_ids']) ? 0 : $request['initiator_media_ids'],
                ];
                $this->procedureLog->insertGetId($data);
            }
        }
        
        return $this->succeed();
        
    }
    
    /**
     * 上传审批流程相关文件
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function uploadMedias() {
        
        $files = Request::file('medias');
        if (empty($files)) {
            $result['statusCode'] = 500;
            $result['message'] = '您还未选择文件！';
        } else {
            $result['data'] = [];
            $mes = [];
            foreach ($files as $file) {
                $mes [] = $this->uploadedMedias($file, '上传审批流程相关文件');
            }
            $result['statusCode'] = 200;
            $result['message'] = '上传成功！';
            $result['data'] = $mes;
        }
        
        return response()->json($result);
        
    }
    
    /**
     * 删除审批流程日志相关文件
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteMedias($id) {
        
        $path = Media::whereId($id)->value('path');
        $path_arr = explode("/", $path);
        Storage::disk('uploads')->delete($path_arr[5]);
        if (Media::whereId($id)->delete()) {
            $result['statusCode'] = self::HTTP_STATUSCODE_OK;
            $result['message'] = self::MSG_DEL_OK;
        } else {
            $result['statusCode'] = self::HTTP_STATUSCODE_INTERNAL_SERVER_ERROR;
            $result['message'] = self::MSG_BAD_REQUEST;
        }
        
        return response()->json($result);
        
    }
    
}