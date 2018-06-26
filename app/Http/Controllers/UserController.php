<?php
namespace App\Http\Controllers;

use App\Helpers\HttpStatusCode;
use App\Http\Requests\UserRequest;
use App\Models\Event;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Request;
use Throwable;

/**
 * 用户
 *
 * Class UserController
 * @package App\Http\Controllers
 */
class UserController extends Controller {
    
    protected $user, $message, $event;
    
    function __construct(User $user, Message $message, Event $event) {
        
        $this->middleware(['auth', 'checkrole']);
        $this->user = $user;
        $this->message = $message;
        $this->event = $event;
        
    }
    
    /**
     * 个人信息
     *
     * @throws \Throwable
     */
    public function profile() {
        
        return $this->output();
        
    }
    
    /**
     * 重置密码
     *
     * @return JsonResponse
     * @throws Throwable
     */
    public function reset() {
        
        if (Request::isMethod('post')) {
            $password = Request::input('password');
            $pwd = bcrypt(Request::input('pwd'));
            $user = User::find(Auth::id());
            abort_if(
                !Hash::check($password, $user->password),
                HttpStatusCode::BAD_REQUEST
            );
            if ($user->update(['password' => $pwd])) {
                return response()->json($this->result);
            }
        }
        
        return $this->output();
        
    }
    
    /**
     * 我的消息
     *
     * @throws \Throwable
     */
    public function messages() {
        
        if (Request::get('draw')) {
            return response()->json(
                $this->message->index()
            );
        }
        
        return $this->output();
        
    }
    
    /**
     * 待办事项
     *
     * @throws Throwable
     */
    public function events() {
        
        if (Request::get('draw')) {
            return response()->json(
                $this->event->index()
            );
        }
        
        return $this->output();
        
    }
    
    /**
     * 更新用户
     *
     * @param UserRequest $request
     * @param $id
     * @return JsonResponse|string
     * @throws Throwable
     */
    public function update(UserRequest $request, $id) {
        
        $user = User::find($id);
        abort_if(!$user, HttpStatusCode::NOT_FOUND);
        
        return $this->result(
            $this->user->modify(
                $request->all(), $id
            )
        );
        
    }
    
    /**
     * 上传用户头像
     *
     * @param $id
     * @return JsonResponse
     */
    public function uploadAvatar($id) {
        
        $file = Request::file('avatar');
        $check = $this->checkFile($file);
        abort_if(
            !$check['status'],
            HttpStatusCode::INTERNAL_SERVER_ERROR,
            $check['msg']
        );
        // 存项目路径
        $ymd = date("Ymd");
        $path = storage_path('app/avauploads/') . $ymd . "/";
        // 获取后缀名
        $postfix = $file->getClientOriginalExtension();
        // 存数据库url路径+文件名：/年月日/文件.jpg
        $fileName = $ymd . "/" . date("YmdHis") . '_' . str_random(5) . '.' . $postfix;
        // 判断是否存在路径
        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }
        // 移动
        abort_if(
            !$file->move($path, $fileName),
            HttpStatusCode::INTERNAL_SERVER_ERROR,
            '头像保存失败'
        );
        //如果是create操作，图片路径不能直接存储数据库
        //TODO:需要处理默认头像、图片缓存问题
        if ($id < 1) {
            $this->result['fileName'] = $fileName;
            
            return response()->json($this->result);
        }
        
        return $this->saveImg($id, $fileName);
        
    }
    
    /**
     * 验证文件是否上传成功
     *
     * @param $file
     * @return array
     */
    private function checkFile(UploadedFile $file) {
        
        if (!$file->isValid()) {
            return ['status' => false, 'msg' => '文件上传失败'];
        }
        if ($file->getClientSize() > $file->getMaxFilesize()) {
            return ['status' => false, 'msg' => '图片过大'];
        }
        
        return ['status' => true];
        
    }
    
    /**
     * 将图片路径存入数据库
     *
     * @param $id
     * @param $imgName
     * @return JsonResponse
     */
    private function saveImg($id, $imgName) {
        
        $user = User::find($id);
        //判断数据库头像是否相同
        if ($imgName !== $user->avatar_url) {
            $imgToRemove = storage_path('app/avauploads/') . $user->avatar_url;
            if (
                is_file($imgToRemove) &&
                strcmp($user->avatar_url, 'default_avatar.png') != 0
            ) {
                unlink($imgToRemove);
            }
            $user->avatar_url = $imgName;
            if ($user->save()) {
                $this->result['fileName'] = $imgName;
            } else {
                abort(HttpStatusCode::INTERNAL_SERVER_ERROR, '头像保存失败');
            }
        }
        
        return response()->json($this->result);
        
    }
}
