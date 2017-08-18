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
        $input = $request->except(['group_id', 'avatar_url']);
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
    public function uploadAvatar($id) {
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
            return response()->json(['statusCode' => self::HTTP_STATUSCODE_INTERNAL_SERVER_ERROR, 'message' => '头像保存失败']);
        }
        $personalImg = $this->user->whereId($id)->first();
        //判断数据库头像是否相同
        if ($fileName !== $personalImg->avatar_url) {
            $removeOldImg = storage_path('app/avauploads/') . $personalImg->avatar_url;
            if (is_file($removeOldImg)) {
                unlink($removeOldImg);
            }
            $personalImg->avatar_url = $fileName;
            if ($personalImg->save()) {
                $this->result['statusCode'] = self::HTTP_STATUSCODE_OK;
                $this->result['fileName'] = $fileName;
            } else {
                $this->result['statusCode'] = self::HTTP_STATUSCODE_INTERNAL_SERVER_ERROR;
                $this->result['message'] = '头像保存失败';
            }
        }
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
