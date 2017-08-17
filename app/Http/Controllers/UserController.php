<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Models\Group;
use App\Models\User;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller {
    protected $user;

    function __construct(User $user) {
        $this->user = $user;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        if (Request::get('draw')) {
            return response()->json($this->user->datatable());
        }

        return view('user.index', [
            'js' => 'js/user/index.js',
            'dialog' => true,
            'datatable' => true,
            'show' => true
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        return view('user.create', ['js' => 'js/user/create.js', 'form' => true]);
    }

    /**
     * Store a newly created resource in storage.
     * @param UserRequest $request
     * @return \Illuminate\Http\Response
     * @internal param \Illuminate\Http\Request|Request $request
     */
    public function store(UserRequest $request) {
        //创建一个考勤机空记录
        //将request 请求中包含的表单数据填入空记录对应的字段中
        //保存记录

        $user = new User;
        $user->group_id = $request->group_id;
        $user->username = $request->username;
        //TODO:remeber_token、wechatid、password 需要特殊处理
        $user->remember_token = $request->_token;
        //使用默认值
        $user->password = '111111';
        $user->email = $request->email;
        $user->gender = $request->gender;
        $user->realname = $request->realname;
        $user->avatar_url = $request->avatar_url;
        $user->wechatid = $request->wechatid;
        $user->enabled = $request->enabled;

        if ($user->save()) {
            $this->result['statusCode'] = self::HTTP_STATUSCODE_OK;
            $this->result['message'] = self::MSG_CREATE_OK;
        }else {
            $this->result['statusCode'] = self::HTTP_STATUSCODE_INTERNAL_SERVER_ERROR;
            $this->result['message'] = self::MSG_BAD_REQUEST;
        }

        return response()->json($this->result);
    }

    /**
     * Display the specified resource.
     * @return \Illuminate\Http\Response
     * @internal param AttendanceMachine $attendanceMachine
     */
    public function show($id) {
        //根据id 查找单条记录

        $user = $this->user->whereId($id)
            ->first(['username','group_id' ,'realname','gender','email', 'wechatid','created_at','updated_at','enabled']);

        $user->group_id = $user->group->name;
        $user->gender = $user->gender==1 ? '男':'女' ;
        $user->enabled = $user->enabled==1 ? '已启用' : '已禁用' ;
        if ($user) {
            $this->result['statusCode'] = self::HTTP_STATUSCODE_OK;
            $this->result['showData'] = $user;
        } else {
            $this->result['statusCode'] = self::HTTP_STATUSCODE_INTERNAL_SERVER_ERROR;
            $this->result['message'] = '';
        }

        return response()->json($this->result);
    }

    /**
     * Show the form for editing the specified resource.
     * @param $id
     * @return \Illuminate\Http\Response
     * @internal param AttendanceMachine $attendanceMachine
     */
    public function edit($id) {
        //根据id 查找单条记录
        $user = User::findOrFail($id);

        //记录返回给view
        return view('user.edit', [
            'js' => 'js/user/edit.js',
            'user' => $user,
            'form' => true
        ]);
    }

    /**
     * Update the specified resource in storage.
     * @param UserRequest $request
     * @param $id
     * @return \Illuminate\Http\Response
     * @internal param \Illuminate\Http\Request|Request $request
     * @internal param AttendanceMachine $attendanceMachine
     */
    public function update(UserRequest $request, $id) {
        //根据id查找记录，
        //把request 传的值，赋值给对应的字段
        //保存当前记录
        //根据操作结果返回不同的json数据
        $user = User::findOrFail($id);
        $user->group_id = $request->group_id;
        $user->username = $request->username;
        $user->remember_token = $request->_token;
        $user->email = $request->email;
        $user->gender = $request->gender;
        $user->realname = $request->realname;
        $user->avatar_url = $request->avatar_url;
        $user->wechatid = $request->wechatid;
        $user->enabled = $request->enabled;

        if ($user->save()) {
            $this->result['statusCode'] = self::HTTP_STATUSCODE_OK;
            $this->result['message'] = self::MSG_EDIT_OK;
        }else {
            $this->result['statusCode'] = self::HTTP_STATUSCODE_INTERNAL_SERVER_ERROR;
            $this->result['message'] = self::MSG_BAD_REQUEST;
        }

        return response()->json($this->result);


    }

    /**
     * Remove the specified resource from storage.
     *
     * @param $id
     * @return \Illuminate\Http\Response
     * @internal param Procedure $user
     * @internal param AttendanceMachine $attendanceMachine
     */
    public function destroy($id) {
        //根据id查找需要删除表数据
        //TODO:是否删除关联数据？？进行删除操作
        //返回json 格式的操作结果

        $user = User::findOrFail($id);

        if ($user->delete()) {
            $this->result['statusCode'] = self::HTTP_STATUSCODE_OK;
            $this->result['message'] = self::MSG_DEL_OK;
        }else {
            $this->result['statusCode'] = self::HTTP_STATUSCODE_INTERNAL_SERVER_ERROR;
            $this->result['message'] = self::MSG_BAD_REQUEST;
        }

        return response()->json($this->result);
    }

    /**
     * 上传头像处理
     * @param $id
     * @return \Illuminate\Http\JsonResponse
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

        //如果是create操作，图片路径不能直接存储数据库
        //TODO:需要处理默认头像、图片缓存问题
        if($id<1)
        {
            $this->result['statusCode'] = self::HTTP_STATUSCODE_OK;
            $this->result['fileName'] = $fileName;

            return response()->json($this->result);
        }

        return $this->saveImg($id, $fileName);
    }

    /**
     * 将图片路径存入数据库
     * @param $id
     * @param $imgName
     * @return \Illuminate\Http\JsonResponse
     */
    private function saveImg($id, $imgName) {

        $personalImg = $this->user->whereId($id)->first();

        //判断数据库头像是否相同
        if ($imgName !== $personalImg->avatar_url) {

            $removeOldImg = storage_path('app/avauploads/') . $personalImg->avatar_url;

            if (is_file($removeOldImg)&&strcmp($personalImg->avatar_url, 'default_avatar.png')!=0) {
                unlink($removeOldImg);
            }

            $personalImg->avatar_url = $imgName;

            if ($personalImg->save()) {
                $this->result['statusCode'] = self::HTTP_STATUSCODE_OK;
                $this->result['fileName'] = $imgName;
            } else {
                $this->result['statusCode'] = self::HTTP_STATUSCODE_INTERNAL_SERVER_ERROR;
                $this->result['message'] = '头像保存失败';
            }
        }
        return response()->json($this->result);
    }

    /**
     * 验证文件是否上传成功
     * @param $file
     * @return array
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
