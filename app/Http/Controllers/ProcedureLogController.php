<?php
namespace App\Http\Controllers;

use App\Helpers\HttpStatusCode;
use App\Http\Requests\ProcedureLogRequest;
use App\Models\Media;
use App\Models\ProcedureLog;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Storage;
use Throwable;

/**
 * 申请/审批
 *
 * Class ProcedureLogController
 * @package App\Http\Controllers
 */
class ProcedureLogController extends Controller {
    
    protected $pl, $media;
    
    /**
     * ProcedureLogController constructor.
     * @param ProcedureLog $pl
     * @param Media $media
     */
    function __construct(ProcedureLog $pl, Media $media) {
        
        $this->middleware(['auth', 'checkrole']);
        $this->pl = $pl;
        $this->media = $media;
        
    }
    
    /**
     * 我发起的流程审批列表
     *
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function index() {
        
        if (Request::get('draw')) {
            $userId = 6;
            //查询我发布的流程最后一条log记录
            $ids = ProcedureLog::select(DB::raw('max(procedure_logs.id) as id'))
                ->where('initiator_user_id', $userId)
                ->groupBy('first_log_id')
                ->pluck('id')->toArray();
            $where = 'ProcedureLog.id in (' . implode(',', $ids) . ')';
            
            return response()->json(
                $this->pl->index($where)
            );
        }
        
        return $this->output();
        
    }
    
    /**
     * 待审核的流程审批列表
     *
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function pending() {
        
        if (Request::get('draw')) {
            $userId = 3;
            //查询待审核的流程最后一条log记录
            $ids = ProcedureLog::select(DB::raw('max(procedure_logs.id) as id'))
                ->where('step_status', 2)
                ->groupBy('first_log_id')
                ->pluck('id')
                ->toArray();
            $where = 'ProcedureLog.id in (' . implode(',', $ids) . ') and FIND_IN_SET(' . $userId . ',ProcedureStep.approver_user_ids)';
            
            return response()->json($this->pl->index($where));
        }
        
        return $this->output();
        
    }
    
    /**
     * 相关流程列表
     *
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function related() {
        
        if (Request::get('draw')) {
            $userId = 3;
            $where = '(FIND_IN_SET(' . $userId . ',ProcedureStep.related_user_ids) or FIND_IN_SET(' . $userId . ',ProcedureStep.approver_user_ids))';
            
            return response()->json($this->pl->index($where));
            
        }
        
        return $this->output();
        
    }
    
    /**
     * 流程审批详情
     *
     * @param $firstLogId
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function show($firstLogId) {
        
        //根据IDs查询数据
        $data = ProcedureLog::with('procedure', 'procedure_step', 'initiator_user', 'operator_user')
            ->where('first_log_id', $firstLogId)
            ->orderBy('id', 'asc')
            ->get();
        
        return $this->output([
            'js'      => 'js/procedure_log/show.js',
            'data'    => $data,
            'user_id' => Auth::id(),
        ]);
        
    }
    
    /**
     * 发起申请
     *
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function create() {
        
        $procedureId = DB::table('procedures')->pluck('name', 'id');
        
        return $this->output([
            'procedure_id' => $procedureId,
        ]);
        
    }
    
    /**
     * 保存申请信息
     *
     * @param ProcedureLogRequest $request
     * @return JsonResponse
     */
    public function store(ProcedureLogRequest $request) {
        
        return $this->result(
            $this->pl->store($request->all())
        );
        
    }
    
    /**
     * 审批申请
     *
     * @return JsonResponse
     */
    public function sanction() {
        
        return $this->result(true);
        
    }
    
    /**
     * 上传审批流程相关文件
     *
     * @return JsonResponse
     */
    public function upload() {
        
        $files = Request::file('medias');
        if (empty($files)) {
            $result['statusCode'] = HttpStatusCode::INTERNAL_SERVER_ERROR;
            $result['message'] = '您还未选择文件！';
        } else {
            $result['data'] = [];
            $mes = [];
            foreach ($files as $file) {
                $mes [] = $this->media->import($file, '上传审批流程相关文件');
            }
            $result['statusCode'] = HttpStatusCode::OK;
            $result['message'] = '上传成功！';
            $result['data'] = $mes;
        }
        
        return response()->json($result);
        
    }
    
    /**
     * 删除审批流程日志相关文件
     *
     * @param $id
     * @return JsonResponse
     * @throws Exception
     */
    public function delete($id) {
        
        $path_arr = explode("/", Media::find($id)->path);
        Storage::disk('uploads')->delete($path_arr[5]);
        if (Media::find($id)->delete()) {
            $result['statusCode'] = HttpStatusCode::OK;
            $result['message'] = __('messages.del_ok');
        } else {
            $result['statusCode'] = HttpStatusCode::INTERNAL_SERVER_ERROR;
            $result['message'] = __('messages.bad_request');
        }
        
        return response()->json($result);
        
    }
    
}