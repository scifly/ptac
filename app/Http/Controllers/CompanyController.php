<?php

namespace App\Http\Controllers;

use App\Http\Requests\CompanyRequest;
use App\Models\Company;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Facades\Request;


class CompanyController extends Controller {

    protected $company;
    protected $message;

    function __construct(Company $company) {
        $this->company = $company;
        $this->message = [
            'statusCode' => 200,
            'message' => ''
        ];
    }

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
            'dialog' => true
        ]);

    }

    /**
     * 显示创建运营者公司记录的表单
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        return view('company.create', ['js' => 'js/company/create.js']);
    }

    /**
     * 保存新创建的运营者公司记录
     * @param CompanyRequest $request
     * @return \Illuminate\Http\Response
     * @internal param \Illuminate\Http\Request|Request $request
     */
    public function store(CompanyRequest $request) {
        //验证
        $input = $request->except('_token');
        //逻辑
        try {

        } catch (\Exception $exception){

        }

        $res = Company::create($input);
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
     * 显示运营者公司记录详情
     * @param $id
     * @return \Illuminate\Http\Response
     * @internal param Company $company
     */
    public function show($id) {
        // find the record by id
        $company = Company::whereId($id)->first();
        return view('company.show', ['company' => $company]);
    }

    /**
     * 显示编辑运营者公司记录的表单
     * @param $id
     * @return \Illuminate\Http\Response
     * @internal param Company $company
     */
    public function edit($id) {

        $company = Company::whereId($id)->first();
        return view('company.edit', [
            'js' => 'js/company/edit.js',
            'company' => $company
        ]);

    }

    /**
     * 更新指定运营者公司记录
     * @return \Illuminate\Http\Response
     * @internal param \Illuminate\Http\Request $request
     * @internal param Company $company
     */
    public function update(CompanyRequest $request, $id) {
        // find the record by id
        // update the record with the request data
        $company = Company::find($id);
        $company->name = $request->get('name');
        $company->remark = $request->get('remark');
        $company->corpid = $request->get('corpid');
        $company->enabled = $request->get('enabled');
        $res = $company->save();
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
     * 删除指定运营者公司记录
     * @return \Illuminate\Http\Response
     * @internal param Company $company
     */
    public function destroy($id) {
        $res = Company::destroy($id);
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
