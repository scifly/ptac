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
            'datatable' => true
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
        //TODO:remeber_token、password 需要特殊处理
        $user->remember_token = $request->remember_token;
//        $user->password = $request->password;
        $user->email = $request->email;
        $user->gender = $request->gender;
        $user->realname = $request->realname;
        $user->avatar_url = $request->avatar_url;
        $user->enabled = $request->enabled;
        if ($user->save()) {
            return response()->json(['statusCode' => 200, 'message' => '创建成功！']);
        }

        return response()->json(['statusCode' => 500, 'message' => '创建失败！']);
    }

    /**
     * Display the specified resource.
     * @return \Illuminate\Http\Response
     * @internal param AttendanceMachine $attendanceMachine
     */
    public function show($id) {
        //根据id 查找单条记录
        $user = User::whereId($id)->first();

        $gender = $user->gender?'男':'女';

        //记录返回给view
        return view('user.show', ['user' => $user, 'gender' => $gender]);
    }

    /**
     * Show the form for editing the specified resource.
     * @param $id
     * @return \Illuminate\Http\Response
     * @internal param AttendanceMachine $attendanceMachine
     */
    public function edit($id) {
        //根据id 查找单条记录
        $user = User::whereId($id)->first();

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
        $user = User::whereId($id)->first();
        $user->group_id = $request->group_id;
        $user->username = $request->username;
        //TODO:remeber_token、password 需要特殊处理
//        $user->remember_token = $request->remember_token;
//        $user->password = $request->password;
        $user->email = $request->email;
        $user->gender = $request->gender;
        $user->realname = $request->realname;
        $user->avatar_url = $request->avatar_url;
        $user->enabled = $request->enabled;
        if ($user->save()) {
            return response()->json(['statusCode' => 200, 'message' => '更新成功！']);
        }

        return response()->json(['statusCode' => 500, 'message' => '更新失败！']);


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
        //进行删除操作
        //返回json 格式的操作结果

        $user = User::whereId($id)->first();

//        if ($user->delete()) {
//            return response()->json(['statusCode' => 200, 'message' => '删除成功！']);
//        }

        return response()->json(['statusCode' => 500, 'message' => '，关联数据较多，暂不做处理！']);
    }

    /**
     * 上传头像
     * @return \Illuminate\Http\JsonResponse
     */
    public function uploadAvatar(){

        if (Request::isMethod('post')) {

            $files = Request::file('img');
            if (empty($files)){
                $result['statusCode'] = 0;
                $result['message'] = '您还未选择图片！';
                return $result;
            }
            $result['data']=array();
            $mes = array();
            foreach ($files  as $key=>$v){
                if ($v->isValid()) {
                    // 获取文件相关信息
                    $originalName = $v->getClientOriginalName(); // 文件原名
                    $ext = $v->getClientOriginalExtension();     // 扩展名//
                    $realPath = $v->getRealPath();   //临时文件的绝对路径
                    $type = $v->getClientMimeType();     // image/jpeg/

                    // 上传图片
                    $filename = date('Y-m-d-H-i-s') . '-' . uniqid() . '.' . $ext;
                    // 使用我们新建的uploads本地存储空间（目录）
                    $init=0;
                    $bool = Storage::disk('uploads')->put($filename,file_get_contents($realPath));

                    $filePath = '/storage/app/uploads/'.$filename;

                    $mes[] = [
                        'path' => $filePath,
                    ];
                }
            }
            $result['statusCode'] = 1;
            $result['message'] = '上传成功！';
            $result['data'] = $mes;

            return response()->json($result);
        }
    }

    /**
     * 头像文件删除
     * @return \Illuminate\Http\JsonResponse
     */
    public function delAvatar(){
        $imagePath = Request::get('avatar_url');

        $file = explode('uploads/',$imagePath);
        $filename = $file[1];
        Storage::disk('uploads')->delete($filename);

        $result['statusCode'] = 1;
        $result['message'] = '删除成功！';

        return response()->json($result);
    }
}
