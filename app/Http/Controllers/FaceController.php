<?php
namespace App\Http\Controllers;

use App\Models\Face;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Request;
use Throwable;

/**
 * 人脸识别
 *
 * Class FaceController
 * @package App\Http\Controllers
 */
class FaceController extends Controller {
    
    protected $face;
    
    /**
     * FaceController constructor.
     * @param Face $face
     */
    function __construct(Face $face) {
        
        $this->middleware(['auth', 'checkrole']);
        $this->face = $face;
        $this->approve($face);
        
    }
    
    /**
     * 用户列表
     *
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function index() {
    
        return Request::get('draw')
            ? response()->json($this->face->index())
            : $this->output();
    
    }
    
    /**
     * 批量设置人脸识别数据
     *
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function create() {
    
        return $this->output();
    
    }
    
    /**
     * 保存设置结果
     *
     * @return JsonResponse|string
     * @throws Throwable
     */
    public function store() {
    
        return $this->result(
            $this->face->store()
        );
    
    }
    
    /**
     * 编辑人脸识别数据
     *
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function edit() {
    
        return $this->output();
    
    }
    
    /**
     * 修改人脸识别数据
     *
     * @return JsonResponse|string
     * @throws Throwable
     */
    public function modify() {
    
        return $this->result(
            $this->face->modify()
        );
    
    }
    
    /**
     * 清除人脸识别数据
     *
     * @return JsonResponse|string
     * @throws Throwable
     */
    public function destroy() {
    
        return $this->result(
            $this->face->remove()
        );
    
    }
    
}