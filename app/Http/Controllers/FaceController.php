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
        $this->approve($this->face = $face);
        
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
    
        return $this->face->config();
    
    }
    
    /**
     * 删除人脸识别
     *
     * @param null $id
     * @return JsonResponse|string
     */
    public function destroy($id = null) {
    
        return $this->result(
            $this->face->remove($id),
            __('messages.face.config_started')
        );
    
    }
    
}