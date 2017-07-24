<?php

namespace App\Http\Controllers;

use App\Http\Requests\CompanyRequest;
use App\Models\Company;
use Illuminate\Support\Facades\Request;


class CompanyController extends Controller
{

    protected $company;

    function __construct(Company $company)
    {
        $this->company = $company;
    }

    /**
     * 显示运营者公司列表
     * @return \Illuminate\Http\Response
     * @internal param null $arg
     * @internal param Request $request
     */
    public function index()
    {

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
    public function create()
    {
        return view('company.create', ['js' => 'js/company/create.js']);
    }

    /**
     * 保存新创建的运营者公司记录
     * @param CompanyRequest $request
     * @return \Illuminate\Http\Response
     * @internal param \Illuminate\Http\Request|Request $request
     */
    public function store(CompanyRequest $request)
    {
        //验证
        $input = $request->all();
        //逻辑
        $res = Company::create($input);
        if (!$res) {
            return response()->json(['statusCode' => 202, 'Message' => 'add filed']);
        }
        return response()->json(['statusCode' => 200, 'Message' => 'nailed it!']);
    }

    /**
     * 显示运营者公司记录详情
     * @param $id
     * @return \Illuminate\Http\Response
     * @internal param Company $company
     */
    public function show($id)
    {
        // find the record by id
        $company = Company::where('id', $id);
        return view('company.show', ['company' => $company]);
    }

    /**
     * 显示编辑运营者公司记录的表单
     * @param $id
     * @return \Illuminate\Http\Response
     * @internal param Company $company
     */
    public function edit($id)
    {

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
    public function update($id)
    {
        // find the record by id
        // update the record with the request data
        $company = Company::find($id);
        return response()->json([]);
    }

    /**
     * 删除指定运营者公司记录
     * @return \Illuminate\Http\Response
     * @internal param Company $company
     */
    public function destroy($id)
    {
        Company::destroy($id);
        return response()->json(['statusCode' => 200, 'Message' => 'nailed it!']);
    }
}
