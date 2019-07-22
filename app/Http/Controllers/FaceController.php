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
     * 设置人脸识别
     *
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function create() {
    
        return Request::method() == 'POST'
            ? $this->face->import()
            : $this->output();
    
    }
    
    /**
     * 保存人脸识别
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
     * 编辑人脸识别
     *
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function edit() {
    
        return Request::method() == 'POST'
            ? $this->face->import()
            : $this->output();
    
    }
    
    /**
     * 修改人脸识别
     *
     * @return JsonResponse|string
     * @throws Throwable
     */
    public function update() {
    
        return $this->result(
            $this->face->modify()
        );
    
    }
    
    /**
     * 删除人脸识别
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