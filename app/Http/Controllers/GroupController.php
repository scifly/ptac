<?php

namespace App\Http\Controllers;

use App\Http\Requests\GroupRequest;
use App\Models\Group;
use Illuminate\Support\Facades\Request;


class GroupController extends Controller {

    protected $group;
    protected $message;

    function __construct(Group $group) {
        $this->group = $group;
        $this->message = [
            'statusCode' => 200,
            'message' => ''
        ];
    }

    /**
     * 显示角色列表
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {

        if (Request::get('draw')) {
            return response()->json($this->group->datatable());
        }
        return view('group.index', [
            'js' => 'js/group/index.js',
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
        return view('group.create', ['js' => 'js/group/create.js']);
    }

    /**
     * 添加角色
     * @return \Illuminate\Http\Response
     * @internal param \Illuminate\Http\Request $request
     */
    public function store(Gr) {
        //验证
        $input = $request->except('_token');
        //逻辑
        $res = Corp::create($input);
        if (!$res) {
            $this->message['statusCode'] = 202;
            $this->message['message'] = 'add filed';
        } else {
            $this->message['statusCode'] = 200;
            $this->message['message'] = 'nailed it!';
        }
        return response()->json($this->message);
    }

    /**
     * 显示企业记录详情
     *
     * @param  \App\Models\Corp $corp
     * @return \Illuminate\Http\Response
     */
    public function show($id) {
        // find the record by id
        $corp = Corp::whereId($id)->first();
        return view('corp.show', ['corp' => $corp]);
    }

    /**
     * 显示编辑企业记录的表单
     *
     * @param  \App\Models\Corp $corp
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {
        $corp = Corp::whereId($id)->first();
        return view('corp.edit', [
            'js' => 'js/corp/edit.js',
            'corp' => $corp
        ]);
    }

    /**
     * 更新指定企业记录
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Models\Corp $corp
     * @return \Illuminate\Http\Response
     */
    public function update(CorpRequest $request,$id) {
        // find the record by id
        // update the record with the request data
        $corp = Corp::find($id);
        $corp->name = $request->get('name');
        $corp->company_id = $request->get('company_id');
        $corp->corpid = $request->get('corpid');
        $corp->enabled = $request->get('enabled');
        $res = $corp->save();
        if (!$res) {
            $this->message['statusCode'] = 202;
            $this->message['message'] = 'add filed';
        } else {
            $this->message['statusCode'] = 200;
            $this->message['message'] = 'nailed it!';

        }
        return response()->json($this->message);
    }

    /**
     *删除指定企业记录
     *
     * @param  \App\Models\Corp $corp
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        $res = Corp::destroy($id);
        if (!$res) {
            $this->message['statusCode'] = 202;
            $this->message['message'] = 'add filed';
        } else {
            $this->message['statusCode'] = 200;
            $this->message['message'] = 'nailed it!';

        }
        return response()->json($this->message);
    }
}
