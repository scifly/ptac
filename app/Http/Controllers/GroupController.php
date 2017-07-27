<?php

namespace App\Http\Controllers;

use App\Http\Requests\GroupRequest;
use App\Models\Group;
use Illuminate\Support\Facades\Request;

class GroupController extends Controller
{
    protected $group;

    protected $message;

    function __construct(Group $group)
    {
        $this->group = $group ;
        $this->message = [
            'statusCode' => 200,
            'message' => ''
        ];
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (Request::get('draw')) {
            return response()->json($this->group->datatable());
        }
        return view('group.index', [
            'js' => 'js/group/index.js',
            'dialog' => true
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('group.create',['js' => 'js/group/create.js']);
    }

    /**
     * 新增权限
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(GroupRequest $request)
    {

        $data = $request->except('_token');

        if($data !=null){
            $res = Group::create($data);

            if ($res) {
                $this->message['statusCode'] = 200;
                $this->message['message'] = 'nailed it!';
            } else {
                $this->message['statusCode'] = 202;
                $this->message['message'] = 'add filed';
            }
            return response()->json($this->message);
        }

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Group  $group
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $group = Group::whereId($id)->first();

        return view('group.show', ['group' => $group]);
    }

    /**
     *编辑角色页面.
     *
     * @param  \App\Models\Group  $group
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $group = Group::whereId($id)->first();
        return view('group.edit', [
            'js' => 'js/group/edit.js',
            'group' => $group
        ]);
    }

    /**
     * 更新角色.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Group  $group
     * @return \Illuminate\Http\Response
     */
    public function update(GroupRequest $request, $id)
    {
        $group = Group::find($id);

        $group->name = $request->get('name');

        $group->remark = $request->get('remark');

        $group->enabled = $request->get('enabled');

        if ($group->save()) {
            $this->message['statusCode'] = 200;
            $this->message['message'] = 'nailed it!';
        } else {
            $this->message['statusCode'] = 202;
            $this->message['message'] = 'add filed';
        }
        return response()->json($this->message);

    }

    /**
     * 删除角色.
     *
     * @param  \App\Models\Group  $group
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $res = Group::destroy($id);
        if ($res) {
            $this->message['statusCode'] = 200;
            $this->message['message'] = 'nailed it!';
        }else{
            $this->message['statusCode'] = 202;
            $this->message['message'] = 'add filed';
        }
        return response()->json($this->message);
    }
}
