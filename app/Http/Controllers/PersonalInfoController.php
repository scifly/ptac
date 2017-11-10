<?php
namespace App\Http\Controllers;

use App\Http\Requests\PersonalInfoRequest;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Request;

/**
 * 个人信息
 *
 * Class PersonalInfoController
 * @package App\Http\Controllers
 */
class PersonalInfoController extends Controller {
    
    public $imgPath = [];
    protected $user;
    
    function __construct(User $user) {
        
        $this->middleware(['auth']);
        $this->user = $user;
        
    }
    
    /**
     * 修改个人信息
     *
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public function index() {
        
        $id = 1;
        $personalInfo = $this->user->find($id);
        $group = $personalInfo->group::whereId($personalInfo->group_id)->first();
        
        return $this->output(__METHOD__, [
            'personalInfo' => $personalInfo,
            'group' => $group
        ]);
        
    }
    
    /**
     * 更新个人信息
     *
     * @param PersonalInfoRequest $request
     * @param $id
     * @return \Illuminate\Http\Response
     */
    public function update(PersonalInfoRequest $request, $id) {
        
        $input = $request->except(['group_id', 'avatar_url']);
        $personInfo = $this->user->find($id);
        if (!$personInfo) {
            return $this->notFound();
        }
        
        return $personInfo->update($input)
            ? $this->succeed() : $this->fail('更新个人信息失败');
        
    }
    
    /**
     * 上传头像
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
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
        
        return $this->saveImg($id, $fileName);
        
    }
    
    /**
     * 校验上传文件
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
     * 保存图片
     *
     * @param $id
     * @param $imgName
     * @return \Illuminate\Http\JsonResponse
     */
    private function saveImg($id, $imgName) {
        
        $personalImg = $this->user->whereId($id)->first();
        //判断数据库头像是否相同
        if ($imgName !== $personalImg->avatar_url) {
            $removeOldImg = storage_path('app/avauploads/') . $personalImg->avatar_url;
            if (is_file($removeOldImg)) {
                unlink($removeOldImg);
            }
        }
        $personalImg->avatar_url = $imgName;
        
        return $personalImg->save() ? $this->succeed($imgName) : $this->fail('头像保存失败');
        
    }
    
}
