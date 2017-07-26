<?php

namespace App\Http\Controllers;

use App\Http\Requests\CorpRequest;
use App\Models\Corp;
use Illuminate\Support\Facades\Request;


class CorpController extends Controller {
    protected $corp;
    protected $message;

    function __construct(Corp $corp) {
        $this->corp = $corp;
        $this->message = [
            'statusCode' => 200,
            'message' => ''
        ];
    }

    /**
     * 显示企业列表
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {

        if (Request::get('draw')) {
            return response()->json($this->corp->datatable());
        }
        return view('corp.index', [
            'js' => 'js/corp/index.js',
            'dialog' => true
        ]);
    }

    /**
     * 显示创建企业记录的表单
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        return view('corp.create', ['js' => 'js/corp/create.js']);
    }

    /**
     * 保存新创建的企业记录
     * @return \Illuminate\Http\Response
     * @internal param \Illuminate\Http\Request $request
     */
    public function store(CorpRequest $request) {
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
