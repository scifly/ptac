<?php
namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Throwable;

/**
 * 用户
 *
 * Class UserController
 * @package App\Http\Controllers
 */
class UserController extends Controller {
    
    function __construct() {
        
        $this->middleware(['auth', 'checkrole']);
        
    }


    /**
     * 修改个人信息
     *
     * @throws \Throwable
     */
    public function profile() {

        return $this->output();

    }
    
    /**
     * 重置密码
     * @return JsonResponse
     * @throws Throwable
     */
    public function reset() {

        if (Request::isMethod('post')) {
            $password = Request::input('password');
            $pwd = bcrypt(Request::input('pwd'));
            $user = User::find(Auth::id());
            if (!Auth::attempt(['password' => $password])) {
                return response()->json(['statusCode' => self::BAD_REQUEST]);
            }
            $res = $user->update(['password' => $pwd]);
            if ($res) {
                return response()->json(['statusCode' => self::OK]);
            }
        }

        return $this->output();

    }
    
    /**
     * 我的消息
     * @throws \Throwable
     */
    public function messages(){

        if (Request::get('draw')) {
            return response()->json(Message::datatable());
        }

        return $this->output();

    }

    /**
     * 待办事项
     * @throws Throwable
     */
    public function event(){

        if (Request::get('draw')) {
            return response()->json(Event::datatable());
        }

        return $this->output();

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
        if (!$check['status']) {
            return $this->fail($check['msg']);
        }
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
        if (!$file->move($path, $fileName)) {
            return $this->fail('头像保存失败');
        }
        //如果是create操作，图片路径不能直接存储数据库
        //TODO:需要处理默认头像、图片缓存问题
        if ($id < 1) {
            $this->result['statusCode'] = self::OK;
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
                $this->result['statusCode'] = self::OK;
                $this->result['fileName'] = $imgName;
            } else {
                $this->result['statusCode'] = self::INTERNAL_SERVER_ERROR;
                $this->result['message'] = '头像保存失败';
            }
        }
        
        return response()->json($this->result);
        
    }
    
}
