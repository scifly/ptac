<?php
namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Models\User;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Request;
use Throwable;

/**
 * 用户
 *
 * Class UserController
 * @package App\Http\Controllers
 */
class UserController extends Controller {
    
    protected $user;
    
    function __construct(User $user) {
    
        $this->middleware(['auth']);
        $this->user = $user;
    
    }
    
    /**
     * 用户列表
     *
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function index() {
        
        if (Request::get('draw')) {
            return response()->json($this->user->datatable());
        }

        return $this->output(__METHOD__);
        
    }
    
    /**
     * 创建用户
     *
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function create() {
        return $this->output(__METHOD__);
        
    }
    
    /**
     * 保存用户
     *
     * @param UserRequest $request
     * @return JsonResponse
     */
    public function store(UserRequest $request) {
        if ($this->user->existed($request)) {
            return $this->fail('已经有此记录');
        }
        
        return $this->user->create($request->all()) ? $this->succeed() : $this->fail();
        
    }
    
    /**
     * 用户详情
     *
     * @param $id
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function show($id) {
        $user = $this->user->find($id);
        if (!$user) {
            return $this->notFound();
        }
        
        return $this->output(__METHOD__, ['user' => $user]);
        
    }
    
    /**
     * 编辑用户
     *
     * @param $id
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function edit($id) {
        $user = $this->user->find($id);
        if (!$user) {
            return $this->notFound();
        }
        
        return $this->output(__METHOD__, ['user' => $user]);
        
    }
    
    /**
     * 更新用户
     *
     * @param UserRequest $request
     * @param $id
     * @return JsonResponse
     */
    public function update(UserRequest $request, $id) {
        $user = $this->user->find($id);
        if (!$user) {
            return $this->notFound();
        }
        if ($this->user->existed($request, $id)) {
            return $this->fail('已经有此记录');
        }
        
        return $user->update($request->all()) ? $this->succeed() : $this->fail();
        
    }
    
    /**
     * 删除用户
     *
     * @param $id
     * @return JsonResponse
     * @throws Exception
     */
    public function destroy($id) {
        $user = $this->user->find($id);
        if (!$user) {
            return $this->notFound();
        }
        
        return $user->delete() ? $this->succeed() : $this->fail();
        
    }
    
    /**
     * 修改个人信息
     *
     * @param $id
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function profile($id){
        $user = $this->user->find($id);
        if (!$user) {
            return $this->notFound();
        }

        return $this->output(__METHOD__, ['user' => $user]);
    }
    
    /**
     * 重置密码
     * @param $id
     * @return JsonResponse
     * @throws Throwable
     */
    public function reset($id){
        
        $user = $this->user->find($id);
        if(!$user) { return $this->notFound(); }

        return $this->output(__METHOD__, ['user' => $user]);
        
    }

    /**
     * 我的消息
     * @param $id
     */
    public function messages($id){

    }

    /**
     * 待办事项
     */
    public function event(){

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
            $this->result['statusCode'] = self::HTTP_STATUSCODE_OK;
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
        $user = $this->user->find($id);
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
                $this->result['statusCode'] = self::HTTP_STATUSCODE_OK;
                $this->result['fileName'] = $imgName;
            } else {
                $this->result['statusCode'] = self::HTTP_STATUSCODE_INTERNAL_SERVER_ERROR;
                $this->result['message'] = '头像保存失败';
            }
        }
        
        return response()->json($this->result);
        
    }
    
}
