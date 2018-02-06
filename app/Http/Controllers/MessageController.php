<?php
namespace App\Http\Controllers;

use App\Facades\Wechat;
use App\Http\Requests\MessageRequest;
use App\Models\App;
use App\Models\Corp;
use App\Models\Department;
use App\Models\Media;
use App\Models\Message;
use App\Models\User;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
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
            Log::debug(response()->json(Message::datatable()));
            return response()->json(Message::datatable());
        }
        if (Request::method() == 'POST') {
            return Department::contacts();
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
            return response()->json(Department::tree());
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
        
        $input = $request->all();
        $message = new Message();
        return $message->sendMessage($input);
        
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
            'medias'  => Media::medias(explode(',', $message->media_ids)),
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
        
        return $this->result(Message::modify($request, $id));
        
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
    
    public function getDepartmentUsers() {
        
        return Department::showDepartments($this->checkRole());
        
    }
    
    private function checkRole($userId = 1) {
        
        $user = User::find($userId);
        $departments = [];
        $childDepartmentId = [];
        foreach ($user->departments as $department) {
            $departments[] = $department['id'];
        }
        foreach ($departments as $departmentId) {
            $childDepartmentId = $this->departmentChildIds($departmentId);
        }
        $departmentIds = array_merge($departments, $childDepartmentId);
        
        return array_unique($departmentIds);
        
    }
    
    /**
     * 获取该部门下所有部门id
     * @param $id
     * @return array
     */
    private function departmentChildIds($id) {
        
        static $childIds = [];
        $firstIds = Department::whereParentId($id)->get(['id'])->toArray();
        if ($firstIds) {
            foreach ($firstIds as $firstId) {
                $childIds[] = $firstId['id'];
                $this->departmentChildIds($firstId['id']);
            }
        }
        
        return $childIds;
        
    }
    
    public function uploadFile() {
        
        $file = Request::file('uploadFile');
        $type = Request::input('type');
        if (empty($file)) {
            $result['statusCode'] = 0;
            $result['message'] = '您还未选择文件！';
            
            return $result;
        } else {
            $result['data'] = [];
            $mes = Media::upload($file, '消息中心');
            if ($mes) {
                $result['statusCode'] = 1;
                $result['message'] = '上传成功！';
                #window env 2018-02-06 by wenw
                #$path =public_path().'\\'.str_replace('/','\\',$mes['path']) ;
                #linux env 2018-02-06 by wenw
                $path =$mes['path'];
                $data = ["media" => curl_file_create($path)];
                $crop = Corp::whereName('万浪软件')->first();
                $app = App::whereAgentid('999')->first();
                $token = Wechat::getAccessToken($crop->corpid, $app->secret);
                $status = Wechat::uploadMedia($token, $type, $data);
                $message = json_decode($status);

                if ($message->errcode == 0) {
                    $mes['media_id'] = $message->media_id;
                    $result['data'] = $mes;
                } else {
                    $result['statusCode'] = 0;
                    $result['message'] = '微信服务器上传失败！';
                }
            } else {
                $result['statusCode'] = 0;
                $result['message'] = '文件上传失败！';
            }
        }
        
        return response()->json($result);
        
    }
    
}
