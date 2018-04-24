<?php
namespace App\Http\Controllers;

use App\Helpers\HttpStatusCode;
use App\Http\Requests\MessageRequest;
use App\Models\Department;
use App\Models\Media;
use App\Models\Message;
use App\Models\User;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Request;
use Throwable;

/**
 * 消息
 *
 * Class MessageController
 * @package App\Http\Controllers
 */
class MessageController extends Controller {
    
    protected $message, $department, $user, $media;
    
    public function __construct(
        Message $message, Department $departement,
        User $user, Media $media
    ) {
        
        $this->middleware(['auth', 'checkrole']);
        $this->message = $message;
        $this->department = $departement;
        $this->user = $user;
        $this->media = $media;
        
    }
    
    /**
     * 消息列表
     *
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function index() {
        
        if (Request::get('draw')) {
            return response()->json(
                $this->message->datatable()
            );
        }
        if (Request::method() == 'POST') {
            return $this->department->contacts();
        }
        
        return $this->output();
        
    }
    
    /**
     * 消息中心 (应用)
     *
     * @return void
     */
    public function message() {
        
        // return $this->output();
    }
    
    /**
     * 保存消息
     *
     * @param MessageRequest $request
     * @return bool|JsonResponse
     * @throws Exception
     * @throws Throwable
     */
    public function send(MessageRequest $request) {
        
        return $this->message->send(
            $request->all()
        );
        
    }
    
    /**
     * 上传媒体文件
     *
     * @return JsonResponse
     */
    public function uploadFile() {
        
        return response()->json(
            $this->message->upload()
        );
        
    }
    
}
