<?php

namespace App\Http\Controllers;

use App\Http\Requests\CorpRequest;
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
        return parent::output(__METHOD__);
    }

    /**
     * 显示创建企业记录的表单
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        return $this->output(__METHOD__);
    }

    /**
     * 保存新创建的企业记录
     * @param CorpRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(CorpRequest $request) {
        $input = $request->all();
        $record = $this->corp->where('name', $input['name'])
            ->where('company_id', $input['company_id'])
            ->first();
        if (!empty($record)) {
            return response()->json(['statusCode' => self::HTTP_STATUSCODE_INTERNAL_SERVER_ERROR, 'message' => '已经有该记录！']);
        }
        return $this->corp->create($request->all()) ? $this->succeed() : $this->fail();
    }

    /**
     * 显示企业记录详情
     *
     * @param $id
     * @return \Illuminate\Http\Response
     */
    public function show($id) {
        $corp = $this->corp->find($id);
        if (!$corp) {
            return $this->notFound();
        }
        return $this->output(__METHOD__, ['corp' => $corp]);
    }

    /**
     * 显示编辑企业记录的表单
     *
     * @param $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {
        $corp = $this->corp->find($id);
        if (!$corp) {
            return $this->notFound();
        }
        return $this->output(__METHOD__, ['corp' => $corp]);
    }

    /**
     * 更新指定企业记录
     *
     * @param CorpRequest|\Illuminate\Http\Request $request
     * @param $id
     * @return \Illuminate\Http\Response
     */
    public function update(CorpRequest $request, $id) {
        $input = $request->all();
        $record = $this->corp->where('name', $input['name'])
            ->where('company_id', $input['company_id'])
            ->first();
        if (!empty($record) && ($record->id != $id)) {
            return response()->json(['statusCode' => self::HTTP_STATUSCODE_INTERNAL_SERVER_ERROR, 'message' => '已经有该记录！']);
        }
        $corp = $this->corp->find($id);
        if (!$corp) {
            return $this->notFound();
        }
        return $corp->update($request->all()) ? $this->succeed() : $this->fail();
    }

    /**
     *删除指定企业记录
     *
     * @param $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        $corp = $this->corp->find($id);
        if (!$corp) {
            return $this->notFound();
        }
        return $corp->delete() ? $this->succeed() : $this->fail();
    }
}
