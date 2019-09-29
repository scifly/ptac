<?php
namespace App\Http\Controllers;

use App\Models\Camera;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Request;
use Throwable;

/**
 * 人脸识别设备
 *
 * Class CameraController
 * @package App\Http\Controllers
 */
class CameraController extends Controller {
    
    protected $camera;
    
    /**
     * CameraController constructor.
     * @param Camera $camera
     */
    function __construct(Camera $camera) {
        
        $this->middleware(['auth', 'checkrole']);
        $this->approve($this->camera = $camera);
        
    }
    
    /**
     * 人脸识别设备列表
     *
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function index() {
        
        return Request::get('draw')
            ? response()->json($this->camera->index())
            : $this->output();
        
    }
    
    /**
     * 更新人脸识别设备
     *
     * @return JsonResponse|string
     * @throws Throwable
     */
    public function store() {
    
        return $this->result(
            $this->camera->store()
        );
    
    }
    
}
