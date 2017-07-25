<?php

namespace App\Http\Controllers;

use App\Models\Corp;
use Illuminate\Support\Facades\Request;


class CorpController extends Controller {
    protected $corp;

    function __construct(Corp $corp) {
        $this->corp = $corp;
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
        return view('corp.index', ['js' => 'js/corp/index.js']);
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
    public function store() {
        return response()->json(['statusCode' => 200, 'Message' => 'nailed it!']);
    }

    /**
     * 显示企业记录详情
     *
     * @param  \App\Models\Corp $corp
     * @return \Illuminate\Http\Response
     */
    public function show() {
        // find the record by id
        //return view('corp.show', ['corp' => $corp]);
    }

    /**
     * 显示编辑企业记录的表单
     *
     * @param  \App\Models\Corp $corp
     * @return \Illuminate\Http\Response
     */
    public function edit() {
        return view('corp.edit', ['js' => 'js/corp/edit.js']);
    }

    /**
     * 更新指定企业记录
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Models\Corp $corp
     * @return \Illuminate\Http\Response
     */
    public function update() {
        // find the record by id
        // update the record with the request data
        return response()->json([]);
    }

    /**
     *删除指定企业记录
     *
     * @param  \App\Models\Corp $corp
     * @return \Illuminate\Http\Response
     */
    public function destroy() {
        return response()->json([]);
    }
}
