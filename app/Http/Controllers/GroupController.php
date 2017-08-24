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
        return parent::output(__METHOD__);

    }

    /**
     * 新建角色页面
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return parent::output(__METHOD__);

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
            return response()->json($this->result);
        }else{
            return $this->group->create($data) ? parent::succeed() : parent::fail();
        }



    }

    /**
     * 角色详情.
     * @param $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $groups = $this->group->whereId($id)
            ->first(['name','remark','created_at','updated_at','enabled']);

        $groups->enabled = $groups->enabled==1 ? '已启用' : '已禁用' ;
        if ($groups) {
            $this->result['statusCode'] = self::HTTP_STATUSCODE_OK;
            $this->result['showData'] = $groups;
        } else {
            $this->result['statusCode'] = self::HTTP_STATUSCODE_INTERNAL_SERVER_ERROR;
            $this->result['message'] = '';
        }
        return response()->json($this->result);
//        return view('group.show', [
//            'group' => $this->group->findOrFail($id)
//        ]);

    }

    /**
     * Show the form for editing the specified resource.
     * @param $id
     * @return \Illuminate\Http\Response
     * @internal param Group $group
     */
    public function edit($id)
    {
        $group = $this->group->find($id);
        if (!$group) { return parent::notFound(); }
        return parent::output(__METHOD__, ['group' => $group]);

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

        $group = $this->group->find($id);
        if (!$group) { return parent::notFound(); }
        return $group->delete() ? parent::succeed() : parent::fail();
    }

}
