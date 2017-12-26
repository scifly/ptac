<?php
namespace App\Http\Controllers;

use App\Http\Requests\MessageRequest;
use App\Models\CommType;
use App\Models\Department;
use App\Models\DepartmentUser;
use App\Models\Media;
use App\Models\Message;
use App\Models\MessageSendingLog;
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
    
    public function __construct() {
    
        $this->middleware(['auth', 'checkrole']);
        
    }
    
    /**
     * 消息列表
     *
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function index() {
        
        if (Request::get('draw')) {
            return response()->json(Message::datatable());
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
     * 创建消息
     *
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function create() {
        
        if (Request::method() === 'POST') {
            return Department::tree();
        }
        
        return $this->output();
        
    }
    
    /**
     * 保存消息
     *
     * @param MessageRequest $request
     * @return bool|JsonResponse
     * @throws Exception
     * @throws Throwable
     */
    public function store(MessageRequest $request) {
        
        $commTypeName = ['微信', '短信', '应用'];
        $input = $request->all();
        $commType = CommType::whereId($input['comm_type_id'])->first();
        if ($commType->name == $commTypeName[0]) {
            return $this->result(Message::store($request));
        }
        
        return true;
        
    }
    
    /**
     * 消息详情
     *
     * @param $id
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function show($id) {
        
        $message = Message::find($id);
        if (!$message) { return $this->notFound(); }
        
        return $this->output([
            'message' => $message,
            'users'   => User::users($message->user_ids),
            'medias'  => Media::medias($message->media_ids),
        ]);
        
    }
    
    /**
     * 编辑消息
     *
     * @param $id
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function edit($id) {
        
        $message = Message::find($id);
        if (!$message) { return $this->notFound(); }
        
        return $this->output([
            'message'       => $message,
            'selectedUsers' => User::users($message->user_ids),
            'medias'        => Media::medias($message->media_ids),
        ]);
        
    }
    
    /**
     * 更新消息
     *
     * @param MessageRequest $request
     * @param $id
     * @return JsonResponse
     * @throws Exception
     * @throws Throwable
     */
    public function update(MessageRequest $request, $id) {
        
        $message = Message::find($id);
        if (!$message) { return $this->notFound(); }
        
        return $this->result($message->modify($request, $id));
        
    }
    
    /**
     * 删除消息
     *
     * @param $id
     * @return JsonResponse
     * @throws Exception
     */
    public function destroy($id) {
        
        $message = Message::find($id);
        if (!$message) { return $this->notFound(); }
        
        return $this->result($message->delete());
        
    }
    
}
