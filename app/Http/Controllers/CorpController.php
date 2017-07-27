<?php

namespace App\Http\Controllers;

use App\Http\Requests\CorpRequest;
use App\Models\Corp;
use Illuminate\Support\Facades\Request;


class CorpController extends Controller {
    protected $corp;

    function __construct(Corp $corp) {$this->corp = $corp;}

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
            'dialog' => true,
            'datatable' => true,
            'form' => true
        ]);
    }

    /**
     * 显示创建企业记录的表单
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        return view('corp.create', [
            'js' => 'js/corp/create.js',
            'form' => true
        ]);
    }

    /**
     * 保存新创建的企业记录
     * @return \Illuminate\Http\Response
     * @internal param \Illuminate\Http\Request $request
     */
    public function store(CorpRequest $request) {

        if ($this->corp->create($request->all())) {
            $this->result['statusCode'] = self::HTTP_STATUSCODE_OK;
            $this->result['message'] = self::MSG_CREATE_OK;
        } else {
            $this->result['statusCode'] = self::HTTP_STATUSCODE_INTERNAL_SERVER_ERROR;
            $this->result['message'] = '';
        }
        return response()->json($this->result);
    }

    /**
     * 显示企业记录详情
     *
     * @param $id
     * @return \Illuminate\Http\Response
     */
    public function show($id) {
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
        if ($this->corp->findOrFail($id)->update($request->all())) {
            $this->result['statusCode'] = self::HTTP_STATUSCODE_OK;
            $this->result['message'] = self::MSG_EDIT_OK;
        } else {
            $this->result['statusCode'] = self::HTTP_STATUSCODE_INTERNAL_SERVER_ERROR;
            $this->result['message'] = '';
        }
        return response()->json($this->result);
    }

    /**
     *删除指定企业记录
     *
     * @param $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        if ($this->corp->findOrFail($id)->delete()) {
            $this->result['statusCode'] = self::HTTP_STATUSCODE_OK;
            $this->result['message'] = self::MSG_DEL_OK;
        } else {
            $this->result['statusCode'] = self::HTTP_STATUSCODE_INTERNAL_SERVER_ERROR;
            $this->result['message'] = '';
        }
        return response()->json($this->result);
    }
}
