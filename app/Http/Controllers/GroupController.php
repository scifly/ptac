<?php

namespace App\Http\Controllers;

use App\Http\Requests\GroupRequest;
use App\Models\Group;
use Illuminate\Support\Facades\Request;

class GroupController extends Controller
{

    protected $group;

    /**
     * GroupController constructor.
     * @param Group $group
     */
    function __construct(Group $group)
    {
        $this->group = $group;
    }

    /**
     * 角色列表主页
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        if (Request::get('draw')) {
            return response()->json($this->group->datatable());
        }
        return view('group.index', [
            'js' => 'js/group/index.js',
            'datatable' => true,
            'dialog' => true
        ]);

    }

    /**
     * 新建角色页面
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('group.create', [
            'js' => 'js/group/create.js',
            'form' => true
        ]);

    }

    /**
     *创建角色
     * @param GroupRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(GroupRequest $request)
    {
        if ($this->group->create($request->all())) {
            return response()->json([
                'statusCode' => self::HTTP_STATUSCODE_OK, 'message' => self::MSG_CREATE_OK,
            ]);
        }
        return response()->json([
            'statusCode' => self::HTTP_STATUSCODE_INTERNAL_SERVER_ERROR, 'message' => '添加失败'
        ]);

    }

    /**
     * 角色详情.
     * @param $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return view('group.show', [
            'group' => $this->group->findOrFail($id)
        ]);

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param $id
     * @return \Illuminate\Http\Response
     * @internal param Group $group
     */
    public function edit($id)
    {

        return view('group.edit', [
            'js' => 'js/group/edit.js',
            'group' => $this->group->findOrFail($id),
            'form' => true
        ]);

    }

    /**
     * Update the specified resource in storage.
     *
     * @param $id
     * @return \Illuminate\Http\Response
     * @internal param Group $group
     */
    public function update($id)
    {

        if ($this->group->findOrFail($id)->update(Request::all())) {
            return response()->json([
                'statusCode' => 200, 'message' => '更改成功',
            ]);
        }

        return response()->json([
            'statusCode' => 500, 'message' => '更改失败'
        ]);

    }

    /**
     * 删除角色.
     * @param $id
     * @return \Illuminate\Http\Response
     * @internal param Group $group
     */
    public function destroy($id)
    {

        if ($this->group->findOrFail($id)->delete()) {
            return response()->json([
                'statusCode' => 200, 'message' => '删除成功',
            ]);
        }
        return response()->json([
            'statusCode' => 500, 'message' => '删除失败'
        ]);

    }

}
