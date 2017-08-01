<?php

namespace App\Http\Controllers;

use App\Http\Requests\CompanyRequest;
use App\Models\Company;
use Illuminate\Support\Facades\Request;


class CompanyController extends Controller {

    protected $company;

    function __construct(Company $company) {$this->company = $company;}

    /**
     * 显示运营者公司列表
     * @return \Illuminate\Http\Response
     * @internal param null $arg
     * @internal param Request $request
     */
    public function index() {

        if (Request::get('draw')) {
            return response()->json($this->company->datatable());
        }
        return view('company.index', [
            'js' => 'js/company/index.js',
            'dialog' => true,
            'datatable' => true,
        ]);
    }

    /**
     * 显示创建运营者公司记录的表单
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        return view('company.create', ['js' => 'js/company/create.js', 'form' => true]);
    }

    /**
     * 保存新创建的运营者公司记录
     * @param CompanyRequest $request
     * @return \Illuminate\Http\Response
     * @internal param \Illuminate\Http\Request|Request $request
     */
    public function store(CompanyRequest $request) {

        if ($this->company->create($request->all())) {
            $this->result['statusCode'] = self::HTTP_STATUSCODE_OK;
            $this->result['message'] = self::MSG_CREATE_OK;
        } else {
            $this->result['statusCode'] = self::HTTP_STATUSCODE_INTERNAL_SERVER_ERROR;
            $this->result['message'] = '';
        }
        return response()->json($this->result);
    }

    /**
     * 显示运营者公司记录详情
     * @param $id
     * @return \Illuminate\Http\Response
     * @internal param Company $company
     */
    public function show($id) {
        return view('company.show', ['company' => $this->company->findOrFail($id)]);
    }

    /**
     * 显示编辑运营者公司记录的表单
     * @param $id
     * @return \Illuminate\Http\Response
     * @internal param Company $company
     */
    public function edit($id) {

        return view('company.edit', [
            'js' => 'js/company/edit.js',
            'company' => $this->company->findOrFail($id),
            'form' => true
        ]);

    }

    /**
     * 更新指定运营者公司记录
     * @param CompanyRequest $request
     * @param $id
     * @return \Illuminate\Http\Response
     */
    public function update(CompanyRequest $request, $id) {
        if ($this->company->findOrFail($id)->update($request->all())) {
            $this->result['statusCode'] = self::HTTP_STATUSCODE_OK;
            $this->result['message'] = self::MSG_EDIT_OK;
        } else {
            $this->result['statusCode'] = self::HTTP_STATUSCODE_INTERNAL_SERVER_ERROR;
            $this->result['message'] = '';
        }
        return response()->json($this->result);
    }

    /**
     * 删除指定运营者公司记录
     * @param $id
     * @return \Illuminate\Http\Response
     * @internal param Company $company
     */
    public function destroy($id) {
        if ($this->company->findOrFail($id)->delete()) {
            $this->result['statusCode'] = self::HTTP_STATUSCODE_OK;
            $this->result['message'] = self::MSG_DEL_OK;
        } else {
            $this->result['statusCode'] = self::HTTP_STATUSCODE_INTERNAL_SERVER_ERROR;
            $this->result['message'] = '';
        }
        return response()->json($this->result);
    }
}

