<?php

namespace App\Http\Controllers;

use App\Http\Requests\CompanyRequest;
use App\Models\Company;
use Illuminate\Support\Facades\Request;

class CompanyController extends Controller {

    protected $company;

    function __construct(Company $company) {
        $this->company = $company;
    }

    /**
     * 显示运营者公司列表
     *
     * @return \Illuminate\Http\Response
     * @internal param null $arg
     * @internal param Request $request
     */
    public function index() {
        if (Request::get('draw')) {
            return response()->json($this->company->datatable());
        }
        return parent::output(__METHOD__);
    }

    /**
     * 显示创建运营者公司记录的表单
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        return $this->output(__METHOD__);
    }

    /**
     * 保存新创建的运营者公司记录
     *
     * @param CompanyRequest $request
     * @return \Illuminate\Http\Response
     * @internal param \Illuminate\Http\Request|Request $request
     */
    public function store(CompanyRequest $request) {
        $data = $request->all();
        $record = $this->company->where('name', $data['name'])
            ->where('corpid', $data['corpid'])
            ->first();
        if (!empty($record)) {
            return response()->json(['statusCode' => self::HTTP_STATUSCODE_INTERNAL_SERVER_ERROR, 'message' => '已经有该记录！']);
        }
        return $this->company->create($request->all()) ? $this->succeed() : $this->fail();
    }

    /**
     * 显示运营者公司记录详情
     *
     * @param $id
     * @return \Illuminate\Http\Response
     */
    public function show($id) {
        $company = $this->company->find($id);
        if (!$company) {
            return $this->notFound();
        }
        return $this->output(__METHOD__, ['company' => $company]);
    }

    /**
     * 显示编辑运营者公司记录的表单
     *
     * @param $id
     * @return \Illuminate\Http\Response
     * @internal param Company $company
     */
    public function edit($id) {
        $company = $this->company->find($id);
        if (!$company) {
            return $this->notFound();
        }
        return $this->output(__METHOD__, ['company' => $company]);
    }

    /**
     * 更新指定运营者公司记录
     *
     * @param CompanyRequest $request
     * @param $id
     * @return \Illuminate\Http\Response
     */
    public function update(CompanyRequest $request, $id) {
       // dd($id);
        $input = $request->all();
        $record = $this->company->where('name', $input['name'])
            ->where('corpid', $input['corpid'])
            ->first();
        if (!empty($record) && ($record->id != $id)) {
            return response()->json(['statusCode' => self::HTTP_STATUSCODE_INTERNAL_SERVER_ERROR, 'message' => '已经有该记录！']);
        }
        $company = $this->company->find($id);
        if (!$company) {
            return $this->notFound();
        }
        return $company->update($request->all()) ? $this->succeed() : $this->fail();
    }

    /**
     * 删除指定运营者公司记录
     * @param $id
     * @return \Illuminate\Http\Response
     * @internal param Company $company
     */
    public function destroy($id) {
        $company = $this->company->find($id);
        if (!$company) {
            return $this->notFound();
        }
        return $company->delete() ? $this->succeed() : $this->fail();
    }
}

