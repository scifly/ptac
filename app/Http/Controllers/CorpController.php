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

        $res =  $this->corp->create($request->except('_token'));
        if (!$res) {
            $this->message['statusCode'] = 202;
            $this->message['message'] = '添加失败';
        } else {
            $this->message['statusCode'] = 200;
            $this->message['message'] = '添加成功!';
        }
        return response()->json($this->message);
    }

    /**
     * 显示企业记录详情
     *
     * @param $id
     * @return \Illuminate\Http\Response
     */
    public function show($id) {
        // find the record by id
        return view('corp.show', ['corp' => $this->corp->findOrFail($id)]);
    }

    /**
     * 显示编辑企业记录的表单
     *
     * @param $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {
        return view('corp.edit', [
            'js' => 'js/company/edit.js',
            'company' => $this->corp->findOrFail($id),
            'form' => true
        ]);
    }

    /**
     * 更新指定企业记录
     *
     * @param CorpRequest|\Illuminate\Http\Request $request
     * @param $id
     * @return \Illuminate\Http\Response
     */
    public function update(CorpRequest $request,$id) {
        // find the record by id
        // update the record with the request data
        $res = $this->corp->findOrFail($id)->update($request->all());
        if (!$res) {
            $this->message['statusCode'] = 202;
            $this->message['message'] = '更新失败';
        } else {
            $this->message['statusCode'] = 200;
            $this->message['message'] = '更新成功!';
        }
        return response()->json($this->message);
    }

    /**
     *删除指定企业记录
     *
     * @param $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        $res = $this->corp->findOrFail($id)->delete();
        if (!$res) {
            $this->message['statusCode'] = 202;
            $this->message['message'] = '删除失败';
        } else {
            $this->message['statusCode'] = 200;
            $this->message['message'] = '删除成功!';
        }
        return response()->json($this->message);
    }
}
