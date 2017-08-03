<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Models\Group;
use App\Models\User;
use Illuminate\Support\Facades\Request;

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
//        $user->remember_token = $request->remember_token;
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
}
