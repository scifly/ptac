<?php

namespace App\Http\Controllers;

use App\Http\Requests\PersonalInfoRequest;
use App\Models\User;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Session;

class personalInfoController extends Controller {
    protected $user;
    public $imgPath = array();

    function __construct(User $user) {
        $this->user = $user;
    }

    /**
     * 显示个人信息详情.
     *
     * @return \Illuminate\Http\Response
     */
    /* public function show($id) {

         $info = $this->user->whereId($id)->first();
         $group = $info->group()->whereId($info->group_id)->first();
         return view('personal_info.show', ['info' => $info, 'group' => $group]);
     }*/

    /**
     * 修改个人信息的表单
     *
     * @param $id
     * @return \Illuminate\Http\Response
     * @internal param User $user
     */
    public function edit($id) {
        $personalInfo = $this->user->whereId($id)->first();
        $group = $personalInfo->group()->whereId($personalInfo->group_id)->first();
        return view('personal_info.edit', [
            'js' => 'js/personal_info/edit.js',
            'personalInfo' => $personalInfo,
            'group' => $group,
            'form' => true
        ]);
    }

    /**
     * 修改更新个人信息
     *
     * @param PersonalInfoRequest $request
     * @param $id
     * @return \Illuminate\Http\Response
     * @internal param \Illuminate\Http\Request $request
     * @internal param User $user
     */
    public function update(PersonalInfoRequest $request, $id) {
        $input = $request->except(['group_id']);
        //从session中取出上传成功后图片的数组（二维数组），然后从session中删除取出数据
        $data = Session::pull('imgPath');
        if (!empty($data)) {
            //判断删除上传的多张图片,排除将要保存的图片
            foreach ($data as $value) {
                if ($value[0] !== $input['avatar_url']) {
                    $removePath = storage_path('app/avauploads/') . $value[0];
                    unlink($removePath);
                }
            }
        }
        $personalOldImg = $this->user->whereId($id)->first(['avatar_url']);
        //判断删除该条记录数据库地址对应的原图片
        if ($input['avatar_url'] !== $personalOldImg->avatar_url) {
            $removeOldImg = storage_path('app/avauploads/') . $personalOldImg->avatar_url;
            if (is_file($removeOldImg)) {
                unlink($removeOldImg);
            }
        }
        //更新表单传过来的数据
        if ($this->user->findOrFail($id)->update($input)) {
            $this->result['statusCode'] = self::HTTP_STATUSCODE_OK;
            $this->result['message'] = self::MSG_EDIT_OK;
        } else {
            $this->result['statusCode'] = self::HTTP_STATUSCODE_INTERNAL_SERVER_ERROR;
            $this->result['message'] = '';
        }
        return response()->json($this->result);
    }

    /**
     * 上传头像处理
     */
    public function uploadAvatar() {
        $imgPath = [];
        $file = Request::file('avatar');
        $check = $this->checkFile($file);
        if (!$check['status']) {
            return response()->json(['statusCode' => self::HTTP_STATUSCODE_INTERNAL_SERVER_ERROR, 'message' => $check['msg']]);
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
            return response()->json(['statusCode' => self::HTTP_STATUSCODE_INTERNAL_SERVER_ERROR, 'message' => '文件保存失败']);
        }
        $imgPath[] = $fileName;
        // 将上传移动成功的图片名称存入session
        Session::push('imgPath', $imgPath);
        return response()->json(['statusCode' => '200', 'fileName' => $fileName]);
    }

    /** 
     * 验证上传文件是否成功
     */
    private function checkFile($file) {
        if (!$file->isValid()) {
            return ['status' => false, 'msg' => '文件上传失败'];
        }
        if ($file->getClientSize() > $file->getMaxFilesize()) {
            return ['status' => false, 'msg' => '图片过大'];
        }

        return ['status' => true];
    }
}
