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
     *
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
        $data = $request->all();
        $result = $this->group->where('name',$data['name'])->first();
        if (!empty($result))
        {
            $this->result['statusCode'] = self::MSG_BAD_REQUEST;
            $this->result['message'] = '该角色已经存在,请勿重复添加!';
        }else{
            if ($this->group->create($data))
            {
                $this->result['statusCode'] = self::HTTP_STATUSCODE_OK;
                $this->result['message'] = self::MSG_CREATE_OK;
            }else{
                $this->result['statusCode'] = self::HTTP_STATUSCODE_INTERNAL_SERVER_ERROR;
                $this->result['message'] = '添加失败';
            }
        }

        return response()->json($this->result);



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
     * 更改角色
     * @param GroupRequest $request
     * @param $id
     * @return \Illuminate\Http\Response
     * @internal param Group $group
     */
    public function update(GroupRequest $request, $id)
    {
        $data = $request->all();
        $result = $this->group->where('name',$data['name'])->first();
        if(!empty($result) && $result->id!= $id)
        {
            $this->result['statusCode'] = self::MSG_BAD_REQUEST;
            $this->result['message'] = '该角色已经存在!';
        }else{
            if ($this->group->findOrFail($id)->update($data)){
                $this->result['statusCode'] = self::HTTP_STATUSCODE_OK;
                $this->result['message'] = self::MSG_EDIT_OK;
            } else {
                $this->result['statusCode'] = self::HTTP_STATUSCODE_INTERNAL_SERVER_ERROR;
                $this->result['message'] = '更新失败';
            }
        }

        return response()->json($this->result);

    }

    /**
     * 删除角色.
     * @param $id
     * @return \Illuminate\Http\Response
     * @internal param Group $group
     */
    public function destroy($id)
    {

        if ($this->group->findOrFail($id)->delete())
        {
            $this->result['message'] = self::MSG_DEL_OK;
        } else {
            $this->result['statusCode'] = self::HTTP_STATUSCODE_INTERNAL_SERVER_ERROR;
            $this->result['message'] = '删除失败';
        }
        return response()->json($this->result);

    }

}
